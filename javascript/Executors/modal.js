import merge from 'lodash/merge';

module.exports = class extends Executor {

    static __name() {

        return "modal";
    }

    __invoke($handle, $options = {}) {

        return $(this.target).modal(merge($options, {
            show: true
        }));
    }
};
