module.exports = class extends Executor {

    __invoke ($options = {}) {

        if (!this.target) {

            return ljs._error("Target not fount for Validator!");
        }

        if (this.target.nodeName !== 'FORM') {

            return ljs._error("Must be a form object!");
        }

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

                            "toast:error".exec(err.message, label.innerHTML.replace(/<.*>/gi, ''));
                        }
                    }
                });

                if (scroll_to < 0) {

                    return;
                }

                $('html, body').animate({
                    scrollTop: $(validator.errorList[0].element).offset().top - 65
                }, 100);

            },
            errorElement: 'span',
            errorPlacement: function (error, element) {
                //console.log(element);
                let area = element.closest('.form-group'),
                    lw = area[0] && area[0].dataset.labelWidth !== undefined ? area[0].dataset.labelWidth : 2,
                    label = $('<div></div>').addClass(`col-sm-${lw}`),
                    errWrap = $('<div></div>').addClass(area[0] && !area[0].dataset.vertical ? `col-sm-${12-lw}` : 'error-wrap')
                        .append(error.addClass('invalid-feedback')).prepend('<small class="fas fa-exclamation-triangle err-valid-icon text-danger"></small> ');
                if (area[0] && !area[0].dataset.vertical) {
                    area.append(label);
                }
                area.append(errWrap);
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid');
                let area = element.closest('.form-group'),
                    icon = $(area).find('.err-valid-icon');
                icon.show();
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
                let area = element.closest('.form-group'),
                    icon = $(area).find('.err-valid-icon');
                icon.hide();
            }
        })
    }

    static __name () {
    
        return "valid";
    }
};