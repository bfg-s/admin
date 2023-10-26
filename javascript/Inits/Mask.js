const Inputmask = require('inputmask').default;

window.libs['mask'] = function ($mask) {
    if (this.target && $mask) {
        return Inputmask($mask).mask(this.target);
    } else {
        if (process.env.NODE_ENV === 'development') {
            console.error('Not found object for input mask!');
        }
    }
};
