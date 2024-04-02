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

                const formData = new FormData();
                formData.append('_token', exec('token'));
                formData.append('_select2_name', name);
                formData.append(name, true);
                formData.append(`${name}_q`, params.data.q ? params.data.q : '');
                formData.append(`${name}_page`, params.data.page ? params.data.page : 1);

                let new_params = {
                    _slect2_name: name,
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
                    formData.append(`${name}_form`, form);
                }

                let data = $(":input").serializeArray();

                data.map(({name, value}) => {
                    if (String(name)[0] !== '_') {
                        formData.append(name, value);
                    }
                });

                formData.append('_build_modal', 1);

                axios.post(window.load_select2, formData)
                    .then((data) => {
                        // let d = data.data.split("\n");
                        // d = d[d.length-1].trim();
                        // try {
                        //     d = JSON.parse(d);
                        // } catch (e) {
                        //
                        // }
                        success(data.data);
                    }).catch(() => failure());
            }
        }
    });
};
