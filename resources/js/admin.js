const app = require('bfg-js').app;

app.register(require('bfg-schema').default);

document.dispatchEvent(
    new CustomEvent(`bfg:theme`, {detail: app})
);

app.provider(require('./core/helpers'));

app.provider({
    register () {
        app.bind('dev', process.env.NODE_ENV === 'development');
        if (process.env.NODE_ENV === 'development') {
            app.execute('globalize');
        }
    }
});

app.boot();