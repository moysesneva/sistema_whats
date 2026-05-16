#!/usr/bin/env python3
"""
convert_inline_handlers.py
Converts inline event handler attributes (onclick, onchange, onsubmit, etc.)
to CSP-compliant data-* attributes consumed by panel-event-dispatcher.js.

Conversion rules:
  onclick="fnName(args)"                -> data-fn="fnName" data-args='[...]'
  onclick="window.print()"             -> data-fn="__window_print"
  onclick="window.location.href='u'"   -> data-fn="__navigate" data-args='["u"]'
  onclick="document.getElementById('id').click()" -> data-fn="__el_click" data-args='["id"]'
  onclick="document.getElementById('id').focus()" -> data-fn="__el_focus" data-args='["id"]'
  onclick="document.getElementById('id').remove()"-> data-fn="__el_remove" data-args='["id"]'
  onclick="$('#id').tab('show')"        -> data-fn="__jq_tab" data-args='["#id","show"]'
  onclick="event.stopPropagation();fnName(args)" -> data-fn="fnName" data-args='[...]' data-stop-propagation="1"
  onclick="return confirm('msg')"       -> data-fn="__confirm" data-args='["msg"]'
  onchange="fnName()"                   -> data-change-fn="fnName"
  onchange="fnName(this.value)"         -> data-change-fn="fnName" data-change-args='["__value__"]'
  onsubmit="return confirm('msg')"      -> data-confirm="msg"
  onsubmit="return fnName()"           -> data-submit-fn="fnName"
  onkeyup="fnName()"                   -> data-keyup-fn="fnName"
  onkeyup="fnName(this.value)"         -> data-keyup-fn="fnName" data-keyup-args='["__value__"]'
  onfocus/onblur, onmouseover/onmouseout -> similar data-* attrs

Security: uses data-fn + window[fnName] in the dispatcher (NOT new Function).
"""
import re
import sys
import os

# ── Arg-list tokeniser ────────────────────────────────────────────────────────

def split_args(s):
    """Split comma-separated JS args, respecting string literals and PHP tags."""
    args, current = [], ''
    depth = 0
    i = 0
    in_sq = in_dq = in_php = False

    while i < len(s):
        c = s[i]

        if in_php:
            if s[i:i+2] == '?>':
                current += '?>'; i += 2; in_php = False
            else:
                current += c; i += 1
            continue

        if in_sq:
            if c == '\\' and i + 1 < len(s):
                current += c + s[i+1]; i += 2
            elif c == "'":
                current += c; in_sq = False; i += 1
            else:
                current += c; i += 1
            continue

        if in_dq:
            if c == '\\' and i + 1 < len(s):
                current += c + s[i+1]; i += 2
            elif c == '"':
                current += c; in_dq = False; i += 1
            else:
                current += c; i += 1
            continue

        if s[i:i+2] == '<?':
            in_php = True
            current += s[i:i+3] if s[i:i+3] == '<?=' else s[i:i+2]
            i += 3 if s[i:i+3] == '<?=' else 2
            continue

        if c == "'":  in_sq = True;  current += c; i += 1; continue
        if c == '"':  in_dq = True;  current += c; i += 1; continue
        if c == '(':  depth += 1;    current += c; i += 1; continue
        if c == ')':  depth -= 1;    current += c; i += 1; continue

        if c == ',' and depth == 0:
            args.append(current.strip()); current = ''; i += 1
            continue

        current += c; i += 1

    if current.strip():
        args.append(current.strip())
    return args


def arg_to_json(a):
    """Convert a single JS argument string to its JSON array element representation."""
    a = a.strip()
    if a in ('this',):       return '"__this__"'
    if a == 'this.value':    return '"__value__"'
    if a in ('true','false','null'): return a

    # PHP expression (short or long echo)
    if a.startswith('<?'):   return a

    # Numeric literal
    if re.match(r'^-?\d+(\.\d+)?$', a): return a

    # Single-quoted JS string → JSON double-quoted string
    if a.startswith("'") and a.endswith("'") and len(a) >= 2:
        inner = a[1:-1]
        # Remove JS escape backslashes before single quotes, escape double quotes
        inner = inner.replace("\\'", "'").replace('"', '\\"')
        return '"' + inner + '"'

    # Already double-quoted
    if a.startswith('"') and a.endswith('"') and len(a) >= 2:
        return a

    # PHP-cast int/float pattern — keep as-is (PHP emits number, valid in JSON)
    if re.match(r'^\(int\)|^\(float\)|^intval\(|^floatval\(', a):
        return a

    # Fallback: keep as-is (might be a PHP expr or complex expression)
    return a


def args_to_json_array(raw):
    """Convert raw JS arg string to a JSON array string suitable for data-args."""
    if not raw.strip():
        return '[]'
    parts = split_args(raw)
    return '[' + ', '.join(arg_to_json(p) for p in parts) + ']'


def build_data_attrs(event_prefix, fn_name, raw_args, stop_prop=False):
    """Build the data-* attribute string for an event."""
    if event_prefix == 'click':
        fn_attr   = 'data-fn'
        args_attr = 'data-args'
    else:
        fn_attr   = 'data-' + event_prefix + '-fn'
        args_attr = 'data-' + event_prefix + '-args'

    json_args = args_to_json_array(raw_args)
    result = fn_attr + '="' + fn_name + '"'
    if json_args != '[]':
        result += " " + args_attr + "='" + json_args + "'"
    if stop_prop:
        result += ' data-stop-propagation="1"'
    return result


# ── Pattern parsers ───────────────────────────────────────────────────────────

def parse_js_value(code, event):
    """
    Parse a JS inline handler value and return the replacement data-* attribute string.
    Returns None if the pattern is unrecognised.
    """
    code = code.strip()

    # Strip 'javascript:' prefix
    code = re.sub(r'^javascript:\s*', '', code, flags=re.IGNORECASE)

    # ── onmouseover / onmouseout style changes ─────────────────────────────
    if event in ('mouseover', 'mouseout'):
        m = re.match(r"^this\.style\.([a-zA-Z]+)\s*=\s*'([^']*)'$", code)
        if m:
            prop, val = m.group(1), m.group(2)
            return (f'data-{event}-fn="__set_style" '
                    f"data-{event}-args='[\"__this__\",\"{prop}\",\"{val}\"]'")
        # generic function call
        m = re.match(r'^([a-zA-Z_$][a-zA-Z0-9_$.]*)\((.*)\);?$', code, re.DOTALL)
        if m:
            return build_data_attrs(event, m.group(1), m.group(2))
        return None

    # ── onfocus / onblur ──────────────────────────────────────────────────
    if event in ('focus', 'blur'):
        m = re.match(r"^this\.style\.([a-zA-Z]+)\s*=\s*'([^']*)'$", code)
        if m:
            prop, val = m.group(1), m.group(2)
            return (f'data-{event}-fn="__set_style" '
                    f"data-{event}-args='[\"__this__\",\"{prop}\",\"{val}\"]'")
        m = re.match(r'^([a-zA-Z_$][a-zA-Z0-9_$.]*)\((.*)\);?$', code, re.DOTALL)
        if m:
            return build_data_attrs(event, m.group(1), m.group(2))
        return None

    # ── onsubmit ──────────────────────────────────────────────────────────
    if event == 'submit':
        # return confirm('msg')
        m = re.match(r"^return\s+confirm\('([^']*)'\);?$", code)
        if m:
            msg = m.group(1).replace('"', '&quot;')
            return f'data-confirm="{msg}"'
        # return fnName()
        m = re.match(r'^return\s+([a-zA-Z_$][a-zA-Z0-9_$.]*)\((.*)\);?$', code)
        if m:
            return 'data-submit-fn="' + m.group(1) + '"' + (
                ' data-submit-args=\'' + args_to_json_array(m.group(2)) + '\''
                if m.group(2).strip() else ''
            )
        return None

    # ── onkeyup ───────────────────────────────────────────────────────────
    if event == 'keyup':
        m = re.match(r'^([a-zA-Z_$][a-zA-Z0-9_$.]*)\((.*)\);?$', code, re.DOTALL)
        if m:
            return build_data_attrs('keyup', m.group(1), m.group(2))
        return None

    # ── onchange ─────────────────────────────────────────────────────────
    if event == 'change':
        m = re.match(r'^([a-zA-Z_$][a-zA-Z0-9_$.]*)\((.*)\);?$', code, re.DOTALL)
        if m:
            return build_data_attrs('change', m.group(1), m.group(2))
        return None

    # ── onclick ───────────────────────────────────────────────────────────
    # window.print()
    if re.match(r'^window\.print\(\);?$', code):
        return 'data-fn="__window_print"'

    # window.location.href = 'url' / window.location='url'
    m = re.match(r"""^(?:window\.)?location(?:\.href)?\s*=\s*['"]([^'"]+)['"];?$""", code)
    if m:
        url = m.group(1).replace('"', '\\"')
        return f'data-fn="__navigate" data-args=\'["{url}"]\''

    # document.getElementById('id').click()
    m = re.match(r"""^document\.getElementById\(['"]([^'"]+)['"]\)\.click\(\);?$""", code)
    if m:
        return f'data-fn="__el_click" data-args=\'["{m.group(1)}"]\''

    # document.getElementById('id').focus()
    m = re.match(r"""^document\.getElementById\(['"]([^'"]+)['"]\)\.focus\(\);?$""", code)
    if m:
        return f'data-fn="__el_focus" data-args=\'["{m.group(1)}"]\''

    # document.getElementById('id').remove()
    m = re.match(r"""^document\.getElementById\(['"]([^'"]+)['"]\)\.remove\(\);?$""", code)
    if m:
        return f'data-fn="__el_remove" data-args=\'["{m.group(1)}"]\''

    # $('sel').tab('method')
    m = re.match(r"""^\$\(['"]([^'"]+)['"]\)\.tab\(['"]([^'"]+)['"]\);?$""", code)
    if m:
        return f'data-fn="__jq_tab" data-args=\'["{m.group(1)}","{m.group(2)}"]\''

    # show/hide pattern: elem.style.display='x'; elem2.style.display='y';
    show_hide_re = re.compile(
        r"""document\.getElementById\(['"]([^'"]+)['"]\)\.style\.display\s*=\s*['"]([^'"]+)['"];?\s*""")
    parts = show_hide_re.findall(code)
    if parts and show_hide_re.sub('', code).strip() == '':
        args = ', '.join(f'"{eid}", "{disp}"' for eid, disp in parts)
        return f"data-fn=\"__show_hide\" data-args='[{args}]'"

    # event.stopPropagation(); fnName(...)
    m = re.match(r'^event\.stopPropagation\(\);\s*(.+)$', code, re.DOTALL)
    if m:
        inner = parse_js_value(m.group(1).strip(), 'click')
        if inner:
            return inner + ' data-stop-propagation="1"'

    # return confirm('msg')  on buttons (not forms)
    m = re.match(r"^return\s+confirm\('([^']*)'\);?$", code)
    if m:
        msg = m.group(1).replace('"', '\\"')
        return f'data-fn="__confirm" data-args=\'["{msg}"]\''

    # return fnName(args)
    m = re.match(r'^return\s+([a-zA-Z_$][a-zA-Z0-9_$.]*)\((.*)\);?$', code, re.DOTALL)
    if m:
        return build_data_attrs('click', m.group(1), m.group(2))

    # Simple function call: fnName(args)
    m = re.match(r'^([a-zA-Z_$][a-zA-Z0-9_$.]*)\((.*)\);?$', code, re.DOTALL)
    if m:
        return build_data_attrs('click', m.group(1), m.group(2))

    return None  # unrecognised


# ── File processor ────────────────────────────────────────────────────────────

# Map from attribute name to event name
EVENT_ATTRS = {
    'onclick':      'click',
    'onchange':     'change',
    'onsubmit':     'submit',
    'onkeyup':      'keyup',
    'onfocus':      'focus',
    'onblur':       'blur',
    'onmouseover':  'mouseover',
    'onmouseout':   'mouseout',
}

# Regex to match on{event}="value" or on{event}='value' OUTSIDE php strings/code
# We use a simple heuristic: match the attribute literally in the file text.
def make_attr_pattern(attr):
    # Matches: attr="..." or attr='...' allowing multi-line values
    return re.compile(
        r'(?<![a-zA-Z_])' + attr + r'\s*=\s*("(?:[^"\\]|\\.)*"|\'(?:[^\'\\]|\\.)*\')',
        re.DOTALL
    )


def is_inside_php_echo(text, match_start):
    """
    Heuristic: check if the match position is inside a PHP echo string.
    Look backwards for unmatched echo " or echo '.
    This is a simple heuristic and may miss edge cases.
    """
    # Look at surrounding context (last 500 chars)
    ctx = text[max(0, match_start - 500):match_start]
    # If we see echo "<...  or echo '<... that's unclosed, we're inside a PHP string
    # Count open/close of PHP strings (very rough)
    # Better: check if we're inside <?php ... ?> block and inside a string
    # For now: skip if line contains 'echo' before the attribute
    line_start = text.rfind('\n', 0, match_start) + 1
    line = text[line_start:match_start]
    if re.search(r'\becho\s+["\']', line):
        return True
    if re.search(r'^\s*echo\s+', line):
        return True
    return False


def process_file(path, dry_run=False):
    with open(path, 'r', encoding='utf-8', errors='replace') as f:
        original = f.read()

    text = original
    changes = []

    for attr, event in EVENT_ATTRS.items():
        pattern = make_attr_pattern(attr)
        offset = 0

        while True:
            m = pattern.search(text, offset)
            if not m:
                break

            full_match = m.group(0)   # e.g. onclick="someFunc()"
            val_with_quotes = m.group(1)  # e.g. "someFunc()"

            # Skip if inside a PHP echo string
            if is_inside_php_echo(text, m.start()):
                offset = m.end()
                continue

            # Extract the raw JS value (strip outer quotes)
            if val_with_quotes.startswith('"'):
                js_val = val_with_quotes[1:-1]
                # Unescape HTML entities that are common in attributes
                js_val = js_val.replace('&lt;', '<').replace('&gt;', '>').replace('&amp;', '&')
            else:
                js_val = val_with_quotes[1:-1]
                js_val = js_val.replace('&lt;', '<').replace('&gt;', '>').replace('&amp;', '&')

            replacement = parse_js_value(js_val, event)

            if replacement is None:
                # Could not parse — skip and leave as-is, log it
                changes.append(('SKIP', attr, js_val[:80]))
                offset = m.end()
                continue

            # Replace the full match with the new data-* attributes
            text = text[:m.start()] + replacement + text[m.end():]
            changes.append(('OK', attr, js_val[:80], replacement[:80]))
            # Don't advance offset — replacement may be shorter/longer,
            # restart search from same position
            offset = m.start() + len(replacement)

    if text != original:
        if not dry_run:
            with open(path, 'w', encoding='utf-8') as f:
                f.write(text)
        return changes, True
    return changes, False


# ── Main ──────────────────────────────────────────────────────────────────────

def main():
    panel_dir = os.path.join(os.path.dirname(__file__), '..', 'login', 'painel')
    panel_dir = os.path.normpath(panel_dir)

    dry_run = '--dry-run' in sys.argv

    # Exclude shared template files already handled manually
    exclude = {'header.php', 'footer.php', 'disk_warning_banner.php',
               'security_headers.php', 'conn.php', 'auth_guard.php',
               'error_config.php', 'session_warning.php'}

    php_files = sorted(
        f for f in os.listdir(panel_dir)
        if f.endswith('.php') and f not in exclude
    )

    total_ok = total_skip = total_files = 0

    for fname in php_files:
        path = os.path.join(panel_dir, fname)
        changes, modified = process_file(path, dry_run=dry_run)
        ok    = sum(1 for c in changes if c[0] == 'OK')
        skip  = sum(1 for c in changes if c[0] == 'SKIP')
        total_ok   += ok
        total_skip += skip
        if modified or skip:
            total_files += 1
            print(f"\n{'[DRY]' if dry_run else '[MOD]'} {fname}  (converted={ok}, skipped={skip})")
            for c in changes:
                if c[0] == 'SKIP':
                    print(f"  SKIP [{c[1]}]: {c[2]}")

    print(f"\n{'='*60}")
    print(f"Files modified : {total_files}")
    print(f"Attrs converted: {total_ok}")
    print(f"Attrs skipped  : {total_skip}")
    if dry_run:
        print("(dry run — no files written)")


if __name__ == '__main__':
    main()
