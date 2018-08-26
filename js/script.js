(function () {
    'use strict';

    window.onload = function () {
        const compression = document.getElementById('slimage_enable_compression');
        toggleEnabled(compression);
        compression.addEventListener('change', function () {
            toggleEnabled(compression);
        });
    };

    function toggleEnabled(compression) {
        let enabled = compression.disabled ? compression.disabled : compression.checked;
        const status = compression.disabled ? 'disabled' : 'active';
        const settings = document.getElementsByClassName('slimage-setting');
        let i;
        for (i = 0; i < settings.length; i++) {
            if (enabled) {
                settings[i].className += ' ' + status;
            } else {
                settings[i].classList.remove(status);
            }
        }
    }
})();