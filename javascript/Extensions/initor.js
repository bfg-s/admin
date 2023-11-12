function _call (execute, target, event, ...params) {
    if (Array.isArray(execute)) {
        for (let i = 0; i <= (execute.length - 1); i++) {
            _call(execute[i], target, event);
        }
    } else if (typeof execute === 'object') {
        const listKeys = Object.keys(execute);
        for (let i = 0; i <= (listKeys.length - 1); i++) {
            const key = listKeys[i];
            const val = execute[key];
            if (Array.isArray(val)) {
                _call(key, target, event, ...val);
            } else {
                _call(key, target, event, val);
            }
        }
    } else if (typeof execute === 'string') {
        execute = String(execute)
            .replace(/^([0-9:]+):/, "");

        const executeFunction = window.libs[execute];

        if (typeof executeFunction === 'function') {

            return executeFunction.bind({target, event})(...params);
        } else {
            console.error(`Function [${execute}] not found in library!`);
        }
    }
}

window.updateInits = () => {
    document.querySelectorAll('[data-load]').forEach((e, k) => {
        if (! e.dataLoaded) {

            const data = e.dataset;
            let fn = data.load;
            let params = data.loadParams ?? [];

            if (typeof params === 'string') {
                try {
                    params = JSON.parse(params);
                } catch (e) {
                    params = String(params).split('&&');
                }
            }

            if (! Array.isArray(params)) {

                params = [params];
            }

            for (let i = 0; i <=  params.length - 1; i++) {
                try {
                    params[i] = JSON.parse(params[i]);
                }  catch (e) {

                }
            }

            try {
                fn = JSON.parse(fn);
            }  catch (e) {

            }

            _call(fn, e, null, ...params);

            e.dataLoaded = true;
        }
    })
};

$(document).on('click', '[data-click]', function (e) {
    let target = e.target;
    if (! target.dataset.click) {
        target = target.closest('[data-click]');
    }
    const data = target.dataset;
    let fn = data.click;
    let params = data.params ?? [];

    if (typeof params === 'string') {
        try {
            params = JSON.parse(params);
        } catch (err) {
            params = String(params).split('&&');
        }
    }

    if (! Array.isArray(params)) {

        params = [params];
    }

    for (let i = 0; i <=  params.length - 1; i++) {
        try {
            params[i] = JSON.parse(params[i]);
        }  catch (err) {

        }
    }

    try {
        fn = JSON.parse(fn);
    }  catch (err) {

    }

    _call(fn, target, e, ...params);
});


$(document).on('change', '[data-change]', function (e) {

    let target = e.target;
    if (! target.dataset.change) {
        target = target.closest('[data-change]');
    }
    const data = target.dataset;
    let fn = data.change;
    let params = data.params ?? [];

    if (typeof params === 'string') {
        try {
            params = JSON.parse(params);
        } catch (err) {
            params = String(params).split('&&');
        }
    }

    if (! Array.isArray(params)) {

        params = [params];
    }

    for (let i = 0; i <=  params.length - 1; i++) {
        try {
            params[i] = JSON.parse(params[i]);
        }  catch (err) {

        }
    }

    try {
        fn = JSON.parse(fn);
    }  catch (err) {

    }

    _call(fn, target, e, ...params);
});
