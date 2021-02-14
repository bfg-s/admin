const vue = require('vue');

document.addEventListener('bfg:theme', (e) => {

    let app = e.detail;

    app.bind('dev', process.env.NODE_ENV === 'development');

    app.vue = vue;

    app.register(require('bfg-vue').default);

    app.singleton('initTemplate', require('./core/iniTemplate'));

    app.schema.context(
        require.context('./components', true, /^.*\.vue$/)
    );
});