window.libs['alert::input'] = function (title, exec = null) {
    return Swal.fire({
        title: title,
        input: 'text',
        inputAttributes: {
            autocapitalize: 'off'
        },
        showCancelButton: true,
        confirmButtonText: window.langs.yes,
    }).then((state) => {

        if (exec) {

            window.exec(exec, state.value, this.storage);
        }

        return state;
    });
};

window.libs['alert::confirm'] = function (title, success = "", cancel = "", options = {}) {
    return Swal.fire({
        text: title,
        type: 'question',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: window.langs.yes,
        cancelButtonText: window.langs.cancel,
        ...options
    }).then((result) => {

        if (result.value) {

            if (typeof success === 'string' || typeof success === 'object') {
                exec(success);
            } else if (typeof success === 'function') {
                success();
            }
        } else {

            if (typeof cancel === 'string' || typeof success === 'object') {
                exec(cancel);
            } else if (typeof cancel === 'function') {
                cancel();
            }
        }

        return result;
    });
};

window.libs['alert::success'] = function (title, text = '', options = {}) {

    return Swal.fire({title: title, text: text, type: 'success', icon: 'success', ...options});
};

window.libs['alert::warning'] = function (title, text = '', options = {}) {
    return Swal.fire({title: title, text: text, type: 'warning', icon: 'warning', ...options});
};

window.libs['alert::error'] = function (title, text = '', options = {}) {
    return Swal.fire({title: title, text: text, type: 'error', icon: 'error', ...options});
};

window.libs['alert::info'] = function (title, text = '', options = {}) {
    return Swal.fire({title: title, text: text, type: 'info', icon: 'info', ...options});
};

window.libs['alert::question'] = function (title, text = '', options = {}) {
    return Swal.fire({title: title, text: text, type: 'question', icon: 'question', ...options});
};

window.libs['alert'] = function (config) {
    return Swal.fire(config);
};
