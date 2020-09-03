import merge from 'lodash/merge';

module.exports = class extends Executor {

    __invoke ($handle, $options = {}) {

        return $(this.target).modal(merge($options, {
            show: true
        }));
    }

    static __name () {
    
        return "modal";
    }
};