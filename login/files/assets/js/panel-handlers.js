/**
 * panel-handlers.js
 * Event listeners for shared panel UI elements that previously used inline onclick attributes.
 * Loaded after all other scripts in footer.php so that functions like toggleFullScreen
 * (defined in script.min.js) are already available.
 */
(function () {
    'use strict';

    function ready(fn) {
        if (document.readyState !== 'loading') {
            fn();
        } else {
            document.addEventListener('DOMContentLoaded', fn);
        }
    }

    ready(function () {
        // Fullscreen toggle button (replaces onclick="javascript:toggleFullScreen()")
        var fullscreenBtn = document.getElementById('pcoded-fullscreen-btn');
        if (fullscreenBtn && typeof toggleFullScreen === 'function') {
            fullscreenBtn.addEventListener('click', function (e) {
                e.preventDefault();
                toggleFullScreen();
            });
        }

        // Sidebar toggle — troca vertical-nav-type entre expanded e collapsed (desktop)
        // ou entre offcanvas e expanded (tablet/phone). Independente do pcoded plugin.
        var sidebarBtn = document.getElementById('enam-sidebar-btn');
        var pcodedEl   = document.getElementById('pcoded');
        if (sidebarBtn && pcodedEl) {
            sidebarBtn.addEventListener('click', function (e) {
                e.preventDefault();
                var dt      = pcodedEl.getAttribute('pcoded-device-type') || 'desktop';
                var current = pcodedEl.getAttribute('vertical-nav-type')  || 'expanded';
                var next;
                if (dt === 'desktop') {
                    next = (current === 'expanded') ? 'collapsed' : 'expanded';
                } else {
                    next = (current === 'offcanvas') ? 'expanded' : 'offcanvas';
                }
                pcodedEl.setAttribute('vertical-nav-type', next);
            });
        }
    });
})();
