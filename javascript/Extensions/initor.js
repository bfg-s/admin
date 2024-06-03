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

window.resetInits = (element = document) => {
    element.querySelectorAll('[data-load]').forEach((e, k) => {
        e.dataLoaded = false;
    });
};

window.updateInits = (element = document) => {
    element.querySelectorAll('[data-load]').forEach((e, k) => {

        if (! e.dataLoaded) {

            const data = e.dataset;
            let vueNum = data.vueNum;
            let fn = data.load;
            let params = data.loadParams ?? [];

            if (vueNum) {
                window.initedVue[vueNum].$destroy();
            }

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

[
    'click',
    'hover',
    'change',
    'keyup',
    'keypress',
    'keydown',
    'input',
    'focus',
    'blur',
    'dblclick',
    'submit',
    'formchange',
    'mousedown',
    'mousemove',
    'mouseout',
    'mouseover',
    'mouseup',
    'mousewheel',
].forEach(eventName => {
    $(document).on(eventName, `[data-${eventName}]`, function (e) {

        let target = e.target;
        if (! target.dataset[eventName]) {
            target = target.closest(`[data-${eventName}]`);
        }
        const data = target.dataset;
        let fn = data[eventName];
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
});
