const morphdom = require('morphdom').default;

let componentCollection = {};
let interval = 10000;

window.libs['realtime'] = function (cfg) {
    if (! this.target) {

        return console.error("Target not fount for Validator!");
    }
    interval = Number(cfg.timeout);
    componentCollection[cfg.name] = this.target;
}

function loopInterval() {

    const keys = Object.keys(componentCollection);

    if (keys.length) {

        const token = exec('token');

        axios.post(window.realtime + location.search, {
            _token: token,
            names: keys,
            _realtime: 1,
        }).then(data => {

            if (
                data.data.status !== 'fail'
                && ! document.querySelector('.editable-container')
            ) {

                const resultKeys = Object.keys(data.data);

                for (let i = 0; i <= resultKeys.length - 1; i++) {
                    const key = resultKeys[i];
                    const target = componentCollection[key];
                    if (target && ! target.querySelector('.select2-container--open')) {

                        const namesWithValues = {};

                        target.querySelectorAll('input[type="checkbox"]').forEach((el) => {
                            namesWithValues[el.getAttribute('name')] = el.checked ? 1 : 0;
                        });

                        morphdom(target, data.data[key]);
                        window.resetInits(target);
                        window.updateInits(target);
                        window.updateToolTips();

                        target.querySelectorAll('input[type="checkbox"]').forEach((el) => {
                            const name = el.getAttribute('name');
                            if (namesWithValues[name]) {
                                el.checked = true;
                            }
                        });
                    }
                }
            }
        });
    }

    setTimeout(loopInterval, interval);
}

setTimeout(loopInterval, interval);

$(document).on('pjax:complete', () => {

    componentCollection = {};
});
