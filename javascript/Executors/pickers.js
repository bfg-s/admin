const merge = require('lodash/merge');

module.exports = class extends Executor {

    color () {

        return $(this.target).colorpicker().on('colorpickerChange', (event) => {
            $(this.target).parents('.input-group').find('.fa-square').css('color', event.color.toString());
        });
    }

    icon () {

        return $(this.target).iconpicker({
            cols: 10,
            rows: 5,
            footer: true,
            header: true,
            search: true,
            iconset: 'fontawesome5',
            selectedClass: 'btn-success',
            unselectedClass: '',
            arrowPrevIconClass: 'fas fa-angle-left',
            arrowNextIconClass: 'fas fa-angle-right',
        }).on('change', (e) => {

            $(this.target).parents('.input-group').find('input').val(e.icon)
        });
    }

    date () {

        return $(this.target).datetimepicker({
            locale: ljs.cfg('locale'),
            format: this.target.dataset.format ? this.target.dataset.format : 'DD.MM.YYYY',
            icons: {
                time: "far fa-clock",
                date: "far fa-calendar-alt",
                up: "fas fa-arrow-up",
                down: "fas fa-arrow-down"
            }
        });
    }

    time () {

        return $(this.target).datetimepicker({
            locale: ljs.cfg('locale'),
            format: this.target.dataset.format ? this.target.dataset.format : 'HH:mm:ss',
            icons: {
                time: "far fa-clock",
                date: "far fa-calendar-alt",
                up: "fas fa-arrow-up",
                down: "fas fa-arrow-down"
            }
        });
    }

    datetime () {

        return $(this.target).datetimepicker({
            locale: ljs.cfg('locale'),
            format: this.target.dataset.format ? this.target.dataset.format : 'DD.MM.YYYY HH:mm:ss',
            icons: {
                time: "far fa-clock",
                date: "far fa-calendar-alt",
                up: "fas fa-arrow-up",
                down: "fas fa-arrow-down"
            }
        });
    }

    datetimerange () {

        return $(this.target).daterangepicker({
            timePicker: true,
            locale: {
                format: this.target.dataset.format ? this.target.dataset.format : 'DD.MM.YYYY HH:mm:ss'
            }
        });
    }

    daterange () {

        return $(this.target).daterangepicker({
            locale: {
                format: this.target.dataset.format ? this.target.dataset.format : 'DD.MM.YYYY'
            }
        });
    }

    static __name () {
    
        return "picker";
    }
};