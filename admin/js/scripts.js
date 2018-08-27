/**
 * Scripts
 *
 * @package Slimage
 * @since 1.0
 */

(function () {
    'use strict';

    // Toggle fields on the Main Settings page.
    toggleFields(document.getElementById('slimage_enable_compression'));

    // Toggle the override fields on the single attachment page.
    const overrideToggler = document.querySelector('.compat-field-slimage_override input[type="checkbox"]');
    if (overrideToggler) {
        const overrideValue = document.querySelector('.compat-field-slimage_override input[type="number"]');
        toggleFields(overrideToggler, '.compat-field-slimage_quality, .compat-field-slimage_extras');
        overrideToggler.addEventListener('change', function () {
            overrideValue.value = overrideToggler.checked ? 1 : 0;
        });
    }

    // Toggle the fields.
    function toggleFields(selector, classes) {
        if (selector) {
            setStatus(selector, classes);
            selector.addEventListener('change', function () {
                setStatus(selector, classes);
            });
        }
    }

    // Set the status of the fields.
    function setStatus(selector, classes = '.slimage-setting') {
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