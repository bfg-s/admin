window.libs['select2::init'] = function (...args) {
    if (this.target) {
        return $(this.target).select2(...args);
    }
    return undefined;
};

window.libs['select2::ajax'] =  function () {
    let target = this.target;

    return $(target).select2({
        theme: target.dataset.theme ? target.dataset.theme : 'default',
        ajax: {
            transport: (params, success, failure) => {

                let name = target.dataset.selectName;
                let whereHas = target.dataset.withWhere;

                let new_params = {
                    [name]: true,
                    [`${name}_q`]: params.data.q ? params.data.q : '',
                    [`${name}_page`]: params.data.page ? params.data.page : 1
                };

                if (whereHas) {

                    let form = {};
                    let d = $(target).parents('form').serializeArray();
                    if (Array.isArray(d)) {
                        d.map((i) => {
                            form[i.name] = i.value;
                        })
                    }
                    new_params[`${name}_form`] = form;
                }

                let data = $(":input").serializeArray();

                data.map(({name, value}) => {
                    if (String(name)[0] !== '_') {
                        new_params[name] = value;
                    }
                });

                new_params['_build_modal'] = 1;

                axios.get(window.location.href, {params: new_params})
                    .then((data) => {
                        let d = data.data.split("\n");
                        d = d[d.length-1].trim();
                        try {
                            d = JSON.parse(d);
                        } catch (e) {

                        }
                        success(d);
                    }).catch(() => failure());
            }
        }
    });
};
