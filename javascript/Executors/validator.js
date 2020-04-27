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
                let label = $('<div></div>').addClass('col-sm-2'),
                    errWrap = $('<div></div>').addClass('col-sm-10')
                        .append(error.addClass('invalid-feedback')),
                    area = element.closest('.form-group');
                area.append(label);
                area.append(errWrap);
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            }
        })
    }

    static __name () {
    
        return "valid";
    }
};