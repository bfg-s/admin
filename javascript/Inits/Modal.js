import merge from "lodash/merge";

window.libs['modal'] = function($handle, $options = {}) {
    return $(this.target).modal(merge($options, {
        show: true
    }));
};
