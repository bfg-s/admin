document.addEventListener('bfg:schema_built', (e) => {

    let app = e.detail;

    app.components.new();
});