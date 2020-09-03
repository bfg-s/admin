import VueElementLoading from 'vue-element-loading'

let tooltip_selector = '[title]:not([class^="select2"])';

const load = () => {
    require('./lte_load')(tooltip_selector);
    ljs.vue.component('global_search', require('./Components/GlobalSearch').default);
    ljs.vue.component('gate_tools', require('./Components/GatesTools').default);
    ljs.vue.component('scaffold_tools', require('./Components/ScaffoldTools').default);
    ljs.vue.component('terminal_tools', require('./Components/TerminalTools').default);
    ljs.vue.component('live_reloader', require('./Components/LiveReloader').default);
    ljs.vue.component('v-select', require('./Components/Common/Select2').default);
    ljs.vue.component('v-info', require('./Components/Common/Informer').default);
    ljs.vue.component('v-loading', VueElementLoading);
    ljs.vue.component('v-modal-collection', require('./Components/ModalCollection').default);
};

const methods = require('./lar_methods.js');

const applyScripts = ($root = $(document)) => {

    require('./lar_scripts.js')($root, methods);
};

document.addEventListener('ljs:nav:complete', (details) => {

    applyScripts($(ljs.config('pjax-container')));
    "timer::onetime".exec("tooltip", () => $(tooltip_selector).tooltip({placement: 'auto'}));
});

let ins = require('./lar_instance.js');
if (window.ljs === undefined) { document.addEventListener("ljs:load", () => {ljs.lte = methods; load(); ins(methods);applyScripts();}); }
else {ljs.lte = methods; load();ins(methods);applyScripts(); }
