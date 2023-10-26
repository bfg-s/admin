
function url(target) {
    return target.dataset.url;
}

function table(target) {
    return target.dataset.table;
}

function object(target) {
    return target.dataset.object;
}

function confirm(target) {
    return target.dataset.confirm;
}

function jax(target) {
    return target.dataset.jax;
}

function commandJson(target) {
    return target.dataset.commandJson;
}

function warning(target) {
    return target.dataset.warning;
}

function columns(target) {
    return JSON.parse(target.dataset.columns);
}

function openColumns(target) {
    return JSON.parse(target.dataset.openColumns);
}

function order(target) {
    return target.dataset.order;
}

function orderType(target) {
    return target.dataset.orderType;
}

function selectedIds(target) {
    let ids = [];
    $(`.select_${table(target)}:checked`).each((i, obj) => {
        ids.push(Number(obj.value));
    });
    return ids;
}


window.libs['table_action'] = function () {
    let ids = selectedIds(this.target);
    let commandJson = commandJson(this.target) ? JSON.parse(commandJson(this.target)) : null;

    if ((ids.length || !warning(this.target)) && (jax(this.target) || commandJson)) {

        let call_jax = () => {
            if (commandJson) {
                let key = Object.keys(commandJson)[0];
                if (Array.isArray(commandJson[key][1])) {
                    commandJson[key][1] = {};
                }
                if (typeof commandJson[key][1] === 'object') {
                    commandJson[key][1]['object'] = object(this.target);
                    commandJson[key][1]['ids'] = ids;
                    commandJson[key][1]['columns'] = columns(this.target);
                    commandJson[key][1]['order'] = order(this.target);
                    commandJson[key][1]['orderType'] = orderType(this.target);
                }
                setTimeout(() => {
                    ljs.exec(commandJson);
                }, 100);
            } else {
                jax.path(jax(this.target), object(this.target), ids, columns(this.target), order(this.target), orderType(this.target), url(this.target));
            }
            setTimeout(() => {
                $(this.target).parents('.card').find('.action-selector').each((i, o) => {
                    o.checked = false;
                });
                $(`[name="select_${table(this.target)}"]`)[0].checked = false;
            }, 200);
        };

        if (confirm(this.target)) {

            exec("alert::confirm", confirm(this.target).replace(":num", ids.length), call_jax);

        } else {

            call_jax();
        }
    } else if (warning()) {
        exec('alert::warning', warning());
    }
};

function downloadBlob(blob, name = 'file.txt') {
    if (
        window.navigator &&
        window.navigator.msSaveOrOpenBlob
    ) return window.navigator.msSaveOrOpenBlob(blob);

    // For other browsers:
    // Create a link pointing to the ObjectURL containing the blob.
    const data = window.URL.createObjectURL(
        //blob
        new Blob(blob)
    );

    const link = document.createElement('a');
    link.href = data;
    link.download = name;

    // this is necessary as link.click() does not work on the latest firefox
    link.dispatchEvent(
        new MouseEvent('click', {
            bubbles: true,
            cancelable: true,
            view: window
        })
    );

    setTimeout(() => {
        // For Firefox it is necessary to delay revoking the ObjectURL
        window.URL.revokeObjectURL(data);
        link.remove();
    }, 100);
}


window.libs['table_action::exportToExcel'] = function () {
    let ids = selectedIds(this.target);
    NProgress.start();
    exec("toast::success", "Downloading...");
    axios.post(window.export_excel, {
        _token: exec('token'),
        model: object(this.target),
        ids: ids,
        order: order(this.target),
        order_type: orderType(this.target),
        table: table(this.target),
    }).then((data) => {

        exec("toast::success", "Saving...");

        let contentDispo = data.headers.get('content-disposition');
        if (contentDispo) {
            let fileName = contentDispo.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/)[1];
            let blob = data.data;
            if (window.navigator.msSaveOrOpenBlob) {
                window.navigator.msSaveBlob(blob, fileName);
            } else {
                let downloadLink = window.document.createElement('a');
                let contentTypeHeader = data.headers.get('content-type');
                downloadLink.href = window.URL.createObjectURL(new Blob([blob], {type: contentTypeHeader}));
                downloadLink.download = fileName;
                document.body.appendChild(downloadLink);
                downloadLink.click();
                document.body.removeChild(downloadLink);
            }
        }

    })
    .finally(() => {
        NProgress.done()
    });
};

window.libs['table_action::exportToCsv'] = function () {
    let ids = selectedIds(this.target);
    NProgress.start();
    exec("toast::success", "Downloading...");
    axios.post(window.export_csv, {
        _token: exec('token'),
        model: object(this.target),
        ids: ids,
        order: order(this.target),
        order_type: orderType(this.target),
        table: table(this.target),
    }).then((data) => {
        exec("toast::success", "Saving...");

        let contentDispo = data.headers.get('content-disposition');
        if (contentDispo) {
            let fileName = contentDispo.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/)[1];
            let blob = data.data;
            if (window.navigator.msSaveOrOpenBlob) {
                window.navigator.msSaveBlob(blob, fileName);
            } else {
                let downloadLink = window.document.createElement('a');
                let contentTypeHeader = data.headers.get('content-type');
                downloadLink.href = window.URL.createObjectURL(new Blob([blob], {type: contentTypeHeader}));
                downloadLink.download = fileName;
                document.body.appendChild(downloadLink);
                downloadLink.click();
                document.body.removeChild(downloadLink);
            }
        }
    })
    .finally(() => {
        NProgress.done()
    });
};
