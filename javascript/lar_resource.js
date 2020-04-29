const load = () => {
    require('./lte_load');
};

const methods = require('./lar_methods.js');

const applyScripts = ($root = $(document)) => {

    require('./lar_scripts.js')($root, methods);
};

document.addEventListener('ljs:nav:complete', (details) => {

    applyScripts($(ljs.config('pjax-container')));
    "timer::onetime".exec("tooltip", () => $('[title]').tooltip());
});

let ins = require('./lar_instance.js');
if (window.ljs === undefined) { document.addEventListener("ljs:load", () => {ljs.lte = methods; load(); ins(methods);applyScripts();}); }
else {ljs.lte = methods; load();ins(methods);applyScripts(); }
