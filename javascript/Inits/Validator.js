window.libs['valid'] = function () {
    if (!this.target) {

        return console.error("Target not fount for Validator!");
    }

    if (this.target.nodeName !== 'FORM') {

        return console.error("Must be a form object!");
    }

    // Add data attribute to target element with validation state.
    this.target.setAttribute('data-valid', 'true');

    return $(this.target).validate({
        ignore: '*:not([name])',
        focusInvalid: false,
        invalidHandler: (form, validator) => {

            if (!validator.numberOfInvalids())
                return;

            let scroll_to = $(validator.errorList[0].element).offset().top - 65;

            validator.errorList.map((err) => {

                let fgr = err.element.closest('.form-group');

                if (fgr) {

                    let label = fgr.querySelector('label');

                    if (label) {

                        exec("toast::error", err.message, label.innerHTML.replace(/<.*>/gi, ''));
                    }
                }
            });

            if (validator.errorList.length) {
                exec('onetime', 'aaa', () => {
                    let tab = $(validator.errorList[0].element).closest('.tab-pane');
                    if (tab[0] && tab[0].hasAttribute('aria-labelledby')) {
                        let label_id = `#${tab.attr('aria-labelledby')}`;
                        let label = $(label_id);
                        if (label[0] && !label.hasClass('text-danger')) {
                            label.addClass('text-danger').append(' <small class="fas fa-exclamation-triangle err-valid-icon text-danger"></small>')
                        }
                    }
                });
            }

            if (scroll_to < 0) {

                return;
            }

            $('html, body').animate({
                scrollTop: $(validator.errorList[0].element).offset().top - 65
            }, 100);

        },
        errorElement: 'span',
        errorPlacement: function (error, element) {
            let area = element.closest('.form-group'),
                lw = area[0] && area[0].dataset.labelWidth !== undefined ? area[0].dataset.labelWidth : 2,
                label = $('<div></div>').addClass(`col-sm-${lw}`),
                errWrap = $('<div></div>').addClass(area[0] && !area[0].dataset.vertical ? `col-sm-${12 - lw}` : 'error-wrap')
                    .append(error.addClass('invalid-feedback')).prepend('<small style="margin-left: 8px;" class="fas fa-exclamation-triangle err-valid-icon text-danger"></small> ');
            if (area[0] && area[0].dataset.vertical !== 'true') {
                area.append(label);
            }
            area.append(errWrap);

            // let tab = element.closest('.tab-pane');
            // if (tab[0] && tab[0].hasAttribute('aria-labelledby')) {
            //     let label_id = `#${tab.attr('aria-labelledby')}`;
            //     let label = $(label_id);
            //     if (label[0] && !label.hasClass('text-danger')) {
            //         label.addClass('text-danger').append(' <small class="fas fa-exclamation-triangle err-valid-icon text-danger"></small>')
            //     }
            // }
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass('is-invalid');
            let area = element.closest('.form-group'),
                icon = $(area).find('.err-valid-icon');
            icon.show();

            let tab = $(element).closest('.tab-pane');
            if (tab[0] && tab[0].hasAttribute('aria-labelledby')) {
                let label_id = `#${tab.attr('aria-labelledby')}`;
                let label = $(label_id);
                if (label[0] && !label.hasClass('text-danger')) {
                    label.addClass('text-danger').append(' <small class="fas fa-exclamation-triangle err-valid-icon text-danger"></small>')
                }
            }
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
            let area = element.closest('.form-group'),
                icon = $(area).find('.err-valid-icon');
            icon.hide();

            let tab = $(element).closest('.tab-pane');

            if (tab[0] && tab[0].hasAttribute('aria-labelledby')) {
                let label_id = `#${tab.attr('aria-labelledby')}`;
                let label = $(label_id);
                if (label[0] && label.hasClass('text-danger')) {
                    label.removeClass('text-danger')
                    label.find('.err-valid-icon').remove();
                }
            }
        }
    });
};
