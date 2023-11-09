function http_build_query(obj, num_prefix = null, temp_key = null) {

    let output_string = []

    if (obj !== null) {

        Object.keys(obj).forEach((val) => {

            let key = val;

            num_prefix && !isNaN(key) ? key = num_prefix + key : '';

            key = encodeURIComponent(key.replace(/[!'()*]/g, escape));

            temp_key ? key = temp_key + '[' + key + ']' : '';

            if (typeof obj[val] === 'object') {

                output_string.push(key + '=' + JSON.stringify(obj[val]))
            } else {

                let value = encodeURIComponent(String(obj[val]).replace(/[!'()*]/g, escape));

                output_string.push(key + '=' + value);
            }

        });
    }

    return output_string.join('&');
}


window.libs  = {
    location (location_path, params = null) {

        if (this.target && this.target.name && !params) {
            params = {};
            params[this.target.name] = this.target.value;
        }

        if (location_path instanceof HTMLElement) {

            params = {[location_path.name]: location_path.value};
            location_path = location.origin + location.pathname;
        }

        if (params instanceof HTMLElement) {

            params = {[location_path.name]: location_path.value};
        }


        if (typeof location_path === 'object') {

            params = location_path;
            location_path = location.origin + location.pathname;
        }

        if (location_path === '') {
            location_path = location.origin + location.pathname;
        }
        if (!location_path) {
            location_path = location.origin + location.pathname;
        }


        if (typeof params !== 'string' && params && location_path) {

            if (!/\?/.test(location_path)) {
                location_path += '?';
            } else {
                location_path += '&';
            }

            location_path += http_build_query(params);
        }

        if (typeof params === 'string') {

            if (!/\?/.test(location_path)) {
                location_path += '?';
            } else {
                location_path += '&';
            }

            location_path += params;
        }

        if (location_path) {

            if ($.pjax) {

                $.pjax({url: location_path, container: '#admin-content'});
            } else {

                location = location_path;
            }
        }
    },
    redirect (url) {
        window.location = url;
    },
    reload () {
        window.libs.location(window.location.href);
    },
    reboot () {
        window.location.reload();
    },
    html (selector, html) {
        $(selector).html(html);
    },
    back () {
        window.history.back();
    },
    token () {
        return document.querySelector('[name="csrf-token"]').getAttribute('content');
    },
};

window.exec = (execute, ...params) => {
    if (Array.isArray(execute)) {
        for (let i = 0; i <= (execute.length - 1); i++) {
            window.exec(execute[i]);
        }
    } else if (typeof execute === 'object') {
        const listKeys = Object.keys(execute);
        for (let i = 0; i <= (listKeys.length - 1); i++) {
            const key = listKeys[i];
            const val = execute[key];
            if (Array.isArray(val)) {
                window.exec(key, ...val);
            } else {
                window.exec(key, val);
            }
        }
    } else if (typeof execute === 'string') {
        execute = String(execute)
            .replace(/^([0-9:]+):/, "");

        const executeFunction = window.libs[execute];

        if (typeof executeFunction === 'function') {
            return executeFunction(...params);
        } else {
            console.error(`Function [${execute}] not found in library!`);
        }
    }
};
