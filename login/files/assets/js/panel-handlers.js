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
    });
})();
