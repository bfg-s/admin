module.exports = class extends Executor {

    __invoke ($text = 'Saving...') {

        if (!this.target) {

            return ljs._error("Target not fount for Validator!");
        }

        if (this.target.nodeName !== 'BUTTON' && this.target.nodeName !== 'A') {

            return ljs._error("Must be a 'button' or 'a' object!");
        }

        let obj = this.target,
            parent = obj.closest('form');

        if (obj.dataset.form) {

            parent = document.getElementById(obj.dataset.form);
        }

        if (!parent) {

            let c = ljs.config('pjax-container');

            parent = document.querySelector(`${c} form`);
        }

        if (parent) {

            if (parent.executors && parent.executors['valid'] && !$(parent).valid()) {
                return false;
            }

            let old_text = obj.innerHTML;
            obj.setAttribute('disabled', 'disabled');
            obj.innerHTML = `<i class="fa fa-spinner fa-spin" title="${$text}"></i> <span>${$text}</span>`;
            if (!parent.querySelector('[name="_token"]')) {
                $('<input />').attr('type', 'hidden')
                    .attr('name', '_token')
                    .attr('value', ljs.token)
                    .appendTo(parent);
            }

            $(parent).submit();
        }
    }

    static __name () {
    
        return "submit";
    }
};