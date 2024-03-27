const Inputmask = require('inputmask').default;

window.libs['mask'] = function ($mask, $options = {}) {
    if (this.target && $mask) {
        return Inputmask($mask, $options).mask(this.target);
    } else {
        if (process.env.NODE_ENV === 'development') {
            console.error('Not found object for input mask!');
        }
    }
};
