module.exports = class extends Executor {

    get url() {
        return this.target.dataset.url;
    }

    get table() {
        return this.target.dataset.table;
    }

    get object() {
        return this.target.dataset.object;
    }

    get confirm() {
        return this.target.dataset.confirm;
    }

    get jax() {
        return this.target.dataset.jax;
    }

    get commandJson() {
        return this.target.dataset.commandJson;
    }

    get warning() {
        return this.target.dataset.warning;
    }

    get columns() {
        return JSON.parse(this.target.dataset.columns);
    }

    get openColumns() {
        return JSON.parse(this.target.dataset.openColumns);
    }

    get order() {
        return this.target.dataset.order;
    }

    get orderType() {
        return this.target.dataset.orderType;
    }

    get selectedIds() {
        let ids = [];
        $(`.select_${this.table}:checked`).each((i, obj) => {
            ids.push(Number(obj.value));
        });
        return ids;
    }

    static __name() {

        return "table_action";
    }

    __invoke() {

        let ids = this.selectedIds;
        let commandJson = this.commandJson ? JSON.parse(this.commandJson) : null;

        if ((ids.length || !this.warning) && (this.jax || commandJson)) {

            let call_jax = () => {
                if (commandJson) {
                    let key = Object.keys(commandJson)[0];
                    if (Array.isArray(commandJson[key][1])) {
                        commandJson[key][1] = {};
                    }
                    if (typeof commandJson[key][1] === 'object') {
                        commandJson[key][1]['object'] = this.object;
                        commandJson[key][1]['ids'] = ids;
                        commandJson[key][1]['columns'] = this.columns;
                        commandJson[key][1]['order'] = this.order;
                        commandJson[key][1]['orderType'] = this.orderType;
                    }
                    setTimeout(() => {
                        ljs.exec(commandJson);
                    }, 100);
                } else {
                    jax.path(this.jax, this.object, ids, this.columns, this.order, this.orderType, this.url);
                }
                setTimeout(() => {
                    $(this.target).parents('.card').find('.action-selector').each((i, o) => {
                        o.checked = false;
                    });
                    $(`[name="select_${this.table}"]`)[0].checked = false;
                }, 200);
            };

            if (this.confirm) {

                "alert::confirm".exec(this.confirm.replace(":num", ids.length), call_jax);

            } else {

                call_jax();
            }
        } else if (this.warning) {
            "alert::warning".exec(this.warning);
        }
    }

    exportToExcel() {
        let ids = this.selectedIds;
        ljs.progress.start();
        "toast::success".exec("Downloading...");
        jax.admin.export_excel(this.object, ids, this.order, this.orderType, this.table)
            .then(() => {
                "toast::success".exec("Saving...");
            })
            .finally(() => {
                ljs.progress.done()
            });
    }

    exportToCsv() {
        let ids = this.selectedIds;
        ljs.progress.start();
        "toast::success".exec("Downloading...");
        jax.admin.export_csv(this.object, ids, this.order, this.orderType, this.table)
            .then(() => {
                "toast::success".exec("Saving...");
            })
            .finally(() => {
                ljs.progress.done()
            });
    }
};
