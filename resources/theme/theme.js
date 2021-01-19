document.addEventListener('bfg:theme', (e) => {

    let app = e.detail;

    app.bind('dev', process.env.NODE_ENV === 'development');

    app.vue = require('vue');

    app.register(require('bfg-vue').default);

    const context = require.context('./components', true, /^.*\.vue$/);

    context.keys().forEach(file => app.components.new(context(file).default));
});