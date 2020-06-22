module.exports = class extends Executor {

    __invoke () {

        return $(this.currentTarget).editable({
            highlight: false,
            params: (params) => {
                if (params.name) { params[params.name] = params.value; }
                params.__only_has = true;
                return params;
            },
            display: function () {
                //$(this).html('<i class="fas fa-pencil-alt"></i>')
            }
        }).on('save', (e, resource) => {
            if (typeof resource.response === 'object') {
                ljs.exec(resource.response, null, this.storage);
            }
        });
    }

    static __name () {

        return "editable";
    }
};