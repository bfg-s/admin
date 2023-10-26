
const registered_onetime  = {};

window.libs['timer::onetime'] = function ($name, $execute, $ms = 100) {
    if (registered_onetime[$name]) {

        clearTimeout(registered_onetime[$name]);
    }

    registered_onetime[$name] = setTimeout(() => {

        if (typeof $execute === 'function') {
            $execute();
        } else {
            exec($execute);
        }

        delete registered_onetime[$name];

    }, $ms);
};
