window.libs['submit'] = function ($text = 'Saving...') {
    if (!this.target) {

        return console.error("Target not fount for Validator!");
    }

    if (this.target.nodeName !== 'BUTTON' && this.target.nodeName !== 'A') {

        return console.error("Must be a 'button' or 'a' object!");
    }

    let obj = this.target,
        parent = obj.closest('form');

    if (obj.dataset.form) {

        parent = document.getElementById(obj.dataset.form);
    }

    if (!parent) {

        parent = document.querySelector(`#admin-content form`);
    }

    if (parent) {

        if (parent.executors && parent.executors['valid'] && !$(parent).valid()) {
            return false;
        }

        obj.setAttribute('data-old-text', obj.innerHTML);
        obj.setAttribute('disabled', 'disabled');
        obj.innerHTML = `<i class="fa fa-spinner fa-spin" title="${$text}"></i> <span class="d-none d-sm-inline">${$text}</span>`;
        if (!parent.querySelector('[name="_token"]')) {
            $('<input />').attr('type', 'hidden')
                .attr('name', '_token')
                .attr('value', document.querySelector('[name="csrf-token"]').getAttribute('content'))
                .appendTo(parent);
        }

        $(parent).submit();
    }
};
