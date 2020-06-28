const merge = require('lodash/merge');

module.exports = class extends Executor {

    color () {

        return $(this.target).colorpicker({
            format: this.target.dataset.format ? this.target.dataset.format : 'auto'
        }).on('colorpickerChange', (event) => {
            $(event.target).parents('.input-group').find('.fa-square').css('color', event.color.toString());
        });
    }

    icon () {

        return $(this.target).iconpicker({
            cols: this.target.dataset.cols ? this.target.dataset.cols : 10,
            rows: this.target.dataset.rows ? this.target.dataset.rows : 5,
            footer: this.target.dataset.footer ? this.target.dataset.footer : true,
            header: this.target.dataset.header ? this.target.dataset.header : true,
            search: this.target.dataset.search ? this.target.dataset.search : true,
            iconset: 'fontawesome5',
            selectedClass: 'btn-success',
            unselectedClass: '',
            arrowPrevIconClass: 'fas fa-angle-left',
            arrowNextIconClass: 'fas fa-angle-right',
        }).on('change', (e) => {

            //console.log(e.icon, e.target);
            $(e.target).parents('.input-group').find('input').val(e.icon)
        });
    }

    date () {

        return $(this.target).datetimepicker({
            locale: ljs.cfg('locale'),
            format: this.target.dataset.format ? this.target.dataset.format : 'YYYY-MM-DD',
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
            format: this.target.dataset.format ? this.target.dataset.format : 'YYYY-MM-DD HH:mm:ss',
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
            autoUpdateInput: false,
            timePicker: true,
            showDropdowns: this.target.dataset.showDropdowns ? this.target.dataset.showDropdowns : false,
            opens: this.target.dataset.opens ? this.target.dataset.opens : 'right',
            autoApply: this.target.dataset.autoApply ? this.target.dataset.autoApply : false,
            locale: {
                format: this.target.dataset.format ? this.target.dataset.format : 'YYYY-MM-DD HH:mm:ss'
            }
        }).on('apply.daterangepicker', function (ev, picker) {
            $(this).val(
                picker.startDate.format(this.dataset.format ? this.dataset.format : 'YYYY-MM-DD HH:mm:ss')
                + ' - ' +
                picker.endDate.format(this.dataset.format ? this.dataset.format : 'YYYY-MM-DD HH:mm:ss')
            );
        });
    }

    daterange () {

        return $(this.target).daterangepicker({
            autoUpdateInput: false,
            showDropdowns: this.target.dataset.showDropdowns ? this.target.dataset.showDropdowns : false,
            opens: this.target.dataset.opens ? this.target.dataset.opens : 'right',
            autoApply: this.target.dataset.autoApply ? this.target.dataset.autoApply : false,
            locale: {
                format: this.target.dataset.format ? this.target.dataset.format : 'YYYY-MM-DD'
            }
        }).on('apply.daterangepicker', function (ev, picker) {
            $(this).val(
                picker.startDate.format(this.dataset.format ? this.dataset.format : 'YYYY-MM-DD')
                + ' - ' +
                picker.endDate.format(this.dataset.format ? this.dataset.format : 'YYYY-MM-DD')
            );
        });
    }

    static __name () {
    
        return "picker";
    }
};