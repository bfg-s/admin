import VueElementLoading from 'vue-element-loading'
//window.Pusher = require('pusher-js');

let tooltip_selector = '[title]:not([class^="select2"])';

const load = () => {
    require('./admin_load')(tooltip_selector);
    ljs.vue.mixin(require('./vue_mixin'));
    ljs.vue.component('global_search', require('./Components/GlobalSearch').default);
    ljs.vue.component('live_reloader', require('./Components/LiveReloader').default);
    ljs.vue.component('form_action_after_save', require('./Components/FormActionAfterSave').default);
    ljs.vue.component('v-select', require('./Components/Common/Select2').default);
    ljs.vue.component('v-info', require('./Components/Common/Informer').default);
    ljs.vue.component('v-loading', VueElementLoading);
    ljs.vue.component('v-modal-collection', require('./Components/ModalCollection').default);
    ljs.vue.component('v-navigator', require('./Components/Menu/Navigation').default);
    //window.messageConfigure({domain: ljs.cfg('home')});
};

const methods = require('./lar_methods.js');

const applyScripts = ($root = $(document)) => {

    require('./lar_scripts.js')($root, methods);
};

document.addEventListener('ljs:nav:send', (details) => {
    window.Alpine && window.Alpine.deferMutations && window.Alpine.deferMutations();
});

document.addEventListener('ljs:nav:complete', (details) => {

    applyScripts($(ljs.config('pjax-container')));
    "timer::onetime".exec("tooltip", () => $(tooltip_selector).tooltip({placement: 'auto'}));
    window.Alpine && window.Alpine.flushAndStopDeferringMutations && window.Alpine.flushAndStopDeferringMutations();
});

let ins = require('./lar_instance.js');
if (window.ljs === undefined) {
    document.addEventListener("ljs:load", () => {
        ljs.lte = methods;
        load();
        ins(methods);
        applyScripts();
    });
} else {
    ljs.lte = methods;
    load();
    ins(methods);
    applyScripts();
}
