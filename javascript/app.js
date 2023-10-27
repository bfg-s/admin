import 'nprogress/nprogress.css';
import axios from 'axios'

require('./Extensions/initor');

window.Alpine = require('alpinejs/dist/module.cjs').default;
window.NProgress = require('nprogress/nprogress');
window.Toast = require("toastr");
window.Vue = require("vue");
window.axios = axios;

document.addEventListener('alpine:init', () => {
    require('./AlpineComponents/LiveReloader');
    require('./AlpineComponents/GlobalSearch');
    require('./AlpineComponents/ActionAfterSave');
    require('./AlpineComponents/AdminModals');
    require('./AlpineComponents/ToggleDark');
});

$.fn.editable.defaults.ajaxOptions = {type: "PUT"};

require('./Extensions/executor');
require('./Extensions/adminExecutors');

require('./Inits/Switch');
require('./Inits/Template');
require('./Inits/Mask');
require('./Inits/Pickers');
require('./Inits/Submit');
require('./Inits/Validator');
require('./Inits/Alerts');
require('./Inits/Timer');
require('./Inits/Tabs');
require('./Inits/File');
require('./Inits/Select2');
require('./Inits/Duallist');
require('./Inits/Ckeditor');
require('./Inits/Number');
require('./Inits/Slider');
require('./Inits/Codemirror');
require('./Inits/Md');
require('./Inits/TableAction');
require('./Inits/TableList');
require('./Inits/Str');
require('./Inits/Modal');
require('./Inits/Rating');
require('./Inits/Nestable');
require('./Inits/Editable');
require('./Inits/Cookie');
require('./Inits/Tpl');
require('./Inits/Fancy');

Alpine.start();

exec('tabs');

require('./Extensions/admin_load')();
require('./Extensions/pjax.init');
require('./Extensions/toastExecutors');
require('./Extensions/vueInitor');

window.updateInits();

const tooltip_selector = '[title]:not([class^="select2"])';

$(tooltip_selector).tooltip({placement: 'auto'});
$(document).on('pjax:start', function (event) {
    $(tooltip_selector).tooltip('dispose');
});

window.NProgress.configure({parent: '#admin-content'});

$(document).on('change', '[name]', (e) => {
    let obj = e.target;
    if (!$(obj).parents('.__live__')[0]) {
        exec('flash_document', obj.name, obj.value);
    }
});
