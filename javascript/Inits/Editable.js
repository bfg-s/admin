window.libs['editable'] = async function () {

    const token = exec('token');

    return $(this.target).editable({
        // ajaxOptions: {
        //
        // },
        highlight: false,
        params: (params) => {
            return {[params.name]: params.value, __only_has: true, _token: token};
        },
        display: function () {
            //$(this).html('<i class="fas fa-pencil-alt"></i>')
        }
    }).on('save', (e, resource) => {
        if (typeof resource.response === 'object') {
            exec(resource.response);
        }
    });
};
