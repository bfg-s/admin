function columns(target) {
    return JSON.parse(target.dataset.columns);
}

function openColumns(target) {
    return JSON.parse(target.dataset.openColumns);
}

function selectedIds(target) {
    let ids = [];
    $(`.select_${target.dataset.table}:checked`).each((i, obj) => {
        ids.push(Number(obj.value));
    });
    return ids;
}


window.libs['table_action'] = async function () {
    const target = this.target;
    let ids = selectedIds(this.target);
    let commandJson = target.dataset.commandJson ? JSON.parse(target.dataset.commandJson) : null;

    if ((ids.length || !target.dataset.warning) && (target.dataset.route || commandJson)) {

        let call_jax = async () => {
            if (commandJson) {
                let key = Object.keys(commandJson)[0];
                if (Array.isArray(commandJson[key][1])) {
                    commandJson[key][1] = {};
                }
                if (typeof commandJson[key][1] === 'object') {
                    commandJson[key][1]['object'] = target.dataset.object;
                    commandJson[key][1]['ids'] = ids;
                    commandJson[key][1]['columns'] = columns(this.target);
                    commandJson[key][1]['order'] = target.dataset.order;
                    commandJson[key][1]['orderType'] = target.dataset.orderType;
                }
                setTimeout(() => {
                    exec(commandJson);
                }, 100);
            } else {
                const token = exec('token');
                NProgress.start();
                axios.post(target.dataset.route, {
                    _token: token,
                    class: target.dataset.object,
                    ids: ids,
                    columns: columns(target),
                    orderBy: target.dataset.order,
                    orderType: target.dataset.orderType
                }).then(data => {
                    exec(data.data);
                }).finally(d => {
                    NProgress.done();
                })
            }
            setTimeout(() => {
                $(this.target).parents('.card').find('.action-selector').each((i, o) => {
                    o.checked = false;
                });
                $(`[name="select_${target.dataset.table}"]`)[0].checked = false;
            }, 200);
        };

        if (target.dataset.confirm) {

            exec("alert::confirm", target.dataset.confirm.replace(":num", ids.length), call_jax);

        } else {

            call_jax();
        }
    } else if (target.dataset.warning) {
        exec('alert::warning', target.dataset.warning);
    }
};

window.libs['table_action::exportToExcel'] = async function () {
    let ids = selectedIds(this.target);
    NProgress.start();
    if (window.langs.downloading_excel) {
        exec("toast::success", window.langs.downloading_excel);
    }

    let link = window.document.createElement('a');
    link.setAttribute('target', '');
    link.href = window.export_excel + '?' + http_build_query({
        model: this.target.dataset.object,
        ids: ids,
        order: this.target.dataset.order,
        order_type: this.target.dataset.orderType,
        table: this.target.dataset.table,
        ...get_query_parameters()
    });
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    NProgress.done();
};

window.libs['table_action::exportToCsv'] = async function () {
    let ids = selectedIds(this.target);
    NProgress.start();
    if (window.langs.downloading_csv) {
        exec("toast::success", window.langs.downloading_csv);
    }

    let link = window.document.createElement('a');
    link.setAttribute('target', '');
    link.href = window.export_csv + '?' + http_build_query({
        model: this.target.dataset.object,
        ids: ids,
        order: this.target.dataset.order,
        order_type: this.target.dataset.orderType,
        table: this.target.dataset.table,
        ...get_query_parameters()
    });
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    NProgress.done();
};

function http_build_query(obj, prefix = '') {
    let queryString = [];

    for (let key in obj) {
        if (obj.hasOwnProperty(key)) {
            let fullKey = prefix ? `${prefix}[${encodeURIComponent(key)}]` : encodeURIComponent(key);
            let value = obj[key];

            if (typeof value === 'object' && value !== null) {
                queryString.push(http_build_query(value, fullKey));
            } else {
                queryString.push(`${fullKey}=${encodeURIComponent(value)}`);
            }
        }
    }

    return queryString.join('&');
}

function get_query_parameters() {
    let query = window.location.search.substring(1);
    let params = new URLSearchParams(query);
    let result = {};

    for (let [key, value] of params) {
        if (decodeURIComponent(key).includes('[')) {

            let keys = key.replace(/\]/g, '').split('[');
            let current = result;

            for (let i = 0; i < keys.length - 1; i++) {
                let partKey = decodeURIComponent(keys[i]);
                if (!current[partKey]) {
                    current[partKey] = {};
                }
                current = current[partKey];
            }

            current[decodeURIComponent(keys[keys.length - 1])] = decodeURIComponent(value);
        } else {
            result[decodeURIComponent(key)] = decodeURIComponent(value);
        }
    }

    return result;
}
