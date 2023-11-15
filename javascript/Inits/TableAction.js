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


window.libs['table_action'] = function () {
    const target = this.target;
    let ids = selectedIds(this.target);
    let commandJson = target.dataset.commandJson ? JSON.parse(target.dataset.commandJson) : null;

    if ((ids.length || !target.dataset.warning) && (target.dataset.route || commandJson)) {

        let call_jax = () => {
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
                NProgress.start();
                axios.post(target.dataset.route, {
                    _token: exec('token'),
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
        model: this.target.dataset.object,
        ids: ids,
        order: this.target.dataset.order,
        order_type: this.target.dataset.orderType,
        table: this.target.dataset.table,
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
        model: thid.target.dataset.object,
        ids: ids,
        order: this.target.dataset.order,
        order_type: this.target.dataset.orderType,
        table: this.target.dataset.table,
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
