/**
 * Here scripts will be executed every time the page is loaded.
 * @param $methods
 */
module.exports = ($methods) => {

    $.fn.editable.defaults.ajaxOptions = {type: "PUT"};

    require('./Extensions/validator_rules');

    ljs.progress.configure({ parent: ljs.config('pjax-container') });
    ljs.regExec(require('./Executors/AdminLte'));
    ljs.regExec(require('./Executors/table_list'));

    ljs.regExec(require('./Executors/ckeditor'));
    ljs.regExec(require('./Executors/switch'));
    ljs.regExec(require('./Executors/validator'));
    ljs.regExec(require('./Executors/submit'));
    ljs.regExec(require('./Executors/file'));
    ljs.regExec(require('./Executors/pickers'));
    ljs.regExec(require('./Executors/duallist'));
    ljs.regExec(require('./Executors/number'));
    ljs.regExec(require('./Executors/nestable'));
    ljs.regExec(require('./Executors/md'));
    ljs.regExec(require('./Executors/editable'));
    ljs.regExec(require('./Executors/codemirror'));

    ljs.regExec(require('./Executors/str'));
    ljs.regExec(require('./Executors/table_action'));
};
