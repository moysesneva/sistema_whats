(function () {
    var KEY = 'api_token_warn_dismissed_until';

    function _dismissed() {
        try {
            var until = parseInt(localStorage.getItem(KEY), 10);
            if (isNaN(until)) return false;
            if (Date.now() >= until) { localStorage.removeItem(KEY); return false; }
            return true;
        } catch (e) { return false; }
    }

    function _endOfDay() {
        var d = new Date();
        d.setHours(23, 59, 59, 999);
        return d.getTime();
    }

    function dismiss() {
        try { localStorage.setItem(KEY, _endOfDay()); } catch (e) {}
        var el = document.getElementById('api-token-warning-banner');
        if (el) el.style.display = 'none';
    }

    function init() {
        var banner = document.getElementById('api-token-warning-banner');
        if (!banner) return;

        if (_dismissed()) {
            banner.style.display = 'none';
            return;
        }

        var btn = document.getElementById('api-token-warn-dismiss-btn');
        if (btn) {
            btn.addEventListener('click', dismiss);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
