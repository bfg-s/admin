import map from 'lodash/map';

function _send(message, title = null, options = {}, type = "info") {

    if (typeof message === 'string') {

        Toast[type](message, title, options);

    } else if (typeof message === 'object') {

        if (message["title"] !== undefined && message["text"] !== undefined) {

            let key = type;

            if (message["type"] !== undefined && Toast[message["type"]] !== undefined) {

                key = message["type"];
            }

            Toast[key](message["text"], message["title"], message["options"] !== undefined ? message["options"] : {});
        } else {

            map(message, (item, key) => {

                if (Toast[key] === undefined) {

                    key = type;
                }

                if (typeof item === 'string') {

                    Toast[key](item);
                }

                if (typeof item === 'object') {

                    Toast[key](...item);
                }
            });
        }
    }
}

window.libs['toast::success'] = (message, title = null, options = {}) => {
    _send(message, title, options, 'success');
};

window.libs['toast::warning'] = (message, title = null, options = {}) => {
    _send(message, title, options, 'warning');
};

window.libs['toast::info'] = (message, title = null, options = {}) => {
    _send(message, title, options, 'info');
};

window.libs['toast::error'] = (message, title = null, options = {}) => {
    _send(message, title, options, 'error');
};
