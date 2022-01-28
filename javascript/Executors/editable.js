module.exports = class extends Executor {

    static __name() {

        return "editable";
    }

    __invoke() {

        return $(this.currentTarget).editable({
            highlight: false,
            params: (params) => {
                return {[params.name]: params.value, __only_has: true};
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
};
