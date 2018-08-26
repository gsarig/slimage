(function () {
    'use strict';

    window.onload = function () {
        const overrideToggler = document.querySelector('.compat-field-slimage_override input[type="checkbox"]');
        const overrideValue = document.querySelector('.compat-field-slimage_override input[type="number"]');
        setToggler(document.getElementById('slimage_enable_compression'));
        setToggler(overrideToggler, '.compat-field-slimage_quality, .compat-field-slimage_extras');
        overrideToggler.addEventListener('change', function () {
            overrideValue.value = overrideToggler.checked ? 1 : 0;
            console.log(overrideValue);
        });
    };

    function setToggler(selector, classes) {
        if (selector) {
            toggleEnabled(selector, classes);
            selector.addEventListener('change', function () {
                toggleEnabled(selector, classes);
            });
        }
    }

    function toggleEnabled(selector, classes = '.slimage-setting') {
        let enabled = selector.disabled ? selector.disabled : selector.checked;
        const status = selector.disabled ? 'disabled' : 'active';
        const settings = document.querySelectorAll(classes);
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