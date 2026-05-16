/**
 * panel-event-dispatcher.js
 *
 * Safe CSP-compliant event dispatcher for the MoysesNet admin panel.
 *
 * Security model:
 *   - Replaces inline onclick/onchange/onsubmit/etc. attributes (blocked by CSP) with
 *     data-fn / data-change-fn / data-submit-fn / etc. data attributes.
 *   - Calls functions by name via window[fnName] — NOT new Function(code).
 *       new Function(code) executes arbitrary code strings.
 *       window[fnName] only looks up an existing named function; args are JSON data.
 *   - Args are JSON.parse'd from data-args (structured data, never eval'd).
 *   - Special tokens "__this__" and "__value__" in args resolve to the element
 *     or its .value at call time — no code execution from the DOM.
 *
 * Usage in HTML (after migration from onclick attrs):
 *   <button data-fn="myFunc" data-args='["arg1", 42]'>Click</button>
 *   <button data-fn="myFunc" data-args='[<?= $phpVar ?>]'>Click</button>
 *   <select data-change-fn="onSelect" data-change-args='["__value__"]'>
 *   <form data-submit-fn="validate">
 *   <form data-confirm="Tem certeza?">
 *   <button data-fn="__confirm" data-args='["Excluir?"]' type="submit">
 */
(function () {
    'use strict';

    /* -----------------------------------------------------------------------
     * Helper functions for common DOM patterns that were previously written
     * as inline onclick expressions like window.print(), document.getElementById('x').click()
     * ----------------------------------------------------------------------- */
    window.__window_print = function () { window.print(); };
    window.__navigate     = function (url) { if (url) window.location.href = url; };
    window.__el_click     = function (id)  { var el = document.getElementById(id); if (el) el.click(); };
    window.__el_focus     = function (id)  { var el = document.getElementById(id); if (el) el.focus(); };
    window.__el_remove    = function (id)  { var el = document.getElementById(id); if (el && el.parentNode) el.parentNode.removeChild(el); };
    window.__jq_tab       = function (sel, method) { if (window.$ && $(sel).length) $(sel).tab(method); };
    window.__confirm          = function (msg) { return window.confirm(msg); };
    window.__location_reload  = function () { window.location.reload(); };
    window.__digits_only      = function (el) { el.value = el.value.replace(/[^0-9]/g, ''); };
    /** Show/hide multiple elements: args alternate between id and display value */
    window.__show_hide    = function () {
        for (var i = 0; i + 1 < arguments.length; i += 2) {
            var el = document.getElementById(arguments[i]);
            if (el) el.style.display = arguments[i + 1];
        }
    };
    /** Set a CSS property on an element: __set_style(el, 'color', 'red') */
    window.__set_style = function (el, prop, val) {
        if (el && el.style) el.style[prop] = val;
    };

    /* -----------------------------------------------------------------------
     * Internal helpers
     * ----------------------------------------------------------------------- */
    function parseArgs(raw) {
        if (!raw) return [];
        try { return JSON.parse(raw); } catch (e) { return []; }
    }

    function resolveArgs(args, el) {
        return args.map(function (a) {
            if (a === '__this__')  return el;
            if (a === '__value__') return el.value != null ? el.value : '';
            return a;
        });
    }

    function callFn(fnName, rawArgs, el) {
        var fn = window[fnName];
        if (typeof fn !== 'function') return undefined;
        var args = resolveArgs(parseArgs(rawArgs), el);
        return fn.apply(el, args);
    }

    /* -----------------------------------------------------------------------
     * Click delegation
     * ----------------------------------------------------------------------- */
    document.addEventListener('click', function (e) {
        var el = e.target;
        while (el && el !== document.body) {
            if (el.hasAttribute('data-fn')) {
                var fn      = el.getAttribute('data-fn');
                var rawArgs = el.getAttribute('data-args');
                if (el.hasAttribute('data-stop-propagation')) e.stopPropagation();
                var result  = callFn(fn, rawArgs, el);
                /* If a submit button's handler returns false, prevent form submission */
                if (result === false) e.preventDefault();
                return;
            }
            el = el.parentElement;
        }
    });

    /* -----------------------------------------------------------------------
     * Change delegation
     * ----------------------------------------------------------------------- */
    document.addEventListener('change', function (e) {
        var el = e.target;
        if (el && el.hasAttribute('data-change-fn')) {
            callFn(el.getAttribute('data-change-fn'), el.getAttribute('data-change-args'), el);
        }
    });

    /* -----------------------------------------------------------------------
     * Submit delegation (data-confirm and data-submit-fn on <form>)
     * ----------------------------------------------------------------------- */
    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!form) return;
        if (form.hasAttribute('data-confirm')) {
            if (!window.confirm(form.getAttribute('data-confirm'))) {
                e.preventDefault();
                return;
            }
        }
        if (form.hasAttribute('data-submit-fn')) {
            var result = callFn(form.getAttribute('data-submit-fn'), form.getAttribute('data-submit-args'), form);
            if (result === false) e.preventDefault();
        }
    });

    /* -----------------------------------------------------------------------
     * Input delegation (oninput replacement)
     * ----------------------------------------------------------------------- */
    document.addEventListener('input', function (e) {
        var el = e.target;
        if (el && el.hasAttribute('data-input-fn')) {
            callFn(el.getAttribute('data-input-fn'), el.getAttribute('data-input-args'), el);
        }
    });

    /* -----------------------------------------------------------------------
     * Keyup delegation
     * ----------------------------------------------------------------------- */
    document.addEventListener('keyup', function (e) {
        var el = e.target;
        if (el && el.hasAttribute('data-keyup-fn')) {
            callFn(el.getAttribute('data-keyup-fn'), el.getAttribute('data-keyup-args'), el);
        }
    });

    /* -----------------------------------------------------------------------
     * Focus / blur delegation
     * ----------------------------------------------------------------------- */
    document.addEventListener('focusin', function (e) {
        var el = e.target;
        if (el && el.hasAttribute('data-focus-fn')) {
            callFn(el.getAttribute('data-focus-fn'), el.getAttribute('data-focus-args'), el);
        }
    });

    document.addEventListener('focusout', function (e) {
        var el = e.target;
        if (el && el.hasAttribute('data-blur-fn')) {
            callFn(el.getAttribute('data-blur-fn'), el.getAttribute('data-blur-args'), el);
        }
    });

    /* -----------------------------------------------------------------------
     * Mouseover / mouseout delegation (used for hover style changes)
     * ----------------------------------------------------------------------- */
    document.addEventListener('mouseover', function (e) {
        var el = e.target;
        while (el && el !== document.body) {
            if (el.hasAttribute('data-mouseover-fn')) {
                callFn(el.getAttribute('data-mouseover-fn'), el.getAttribute('data-mouseover-args'), el);
                return;
            }
            el = el.parentElement;
        }
    });

    document.addEventListener('mouseout', function (e) {
        var el = e.target;
        while (el && el !== document.body) {
            if (el.hasAttribute('data-mouseout-fn')) {
                callFn(el.getAttribute('data-mouseout-fn'), el.getAttribute('data-mouseout-args'), el);
                return;
            }
            el = el.parentElement;
        }
    });
})();
