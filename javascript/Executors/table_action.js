module.exports = class extends Executor {

    __invoke () {

        let ids = this.selectedIds;

        if ((ids.length || !this.warning) && this.jax) {

            let call_jax = () => {
                jax.path(this.jax, this.object, ids, this.columns, this.order, this.orderType, this.url)
                    .then(() => {
                        $(`.select_${this.table}`).each((i, obj) => {
                            obj.checked = false;
                        });
                        $(`[name="select_${this.table}"]`)[0].checked = false;
                    });
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

    exportToExcel () {
        let ids = this.selectedIds;
        ljs.progress.start();
        "toast::success".exec("Downloading...");
        jax.lte_admin.export_excel(this.object, ids, this.order, this.orderType, this.table)
            .then(() => {
                "toast::success".exec("Saving...");
            })
            .finally(() => {
                ljs.progress.done()
            });
    }

    exportToCsv () {
        let ids = this.selectedIds;
        ljs.progress.start();
        "toast::success".exec("Downloading...");
        jax.lte_admin.export_csv(this.object, ids, this.order, this.orderType, this.table)
            .then(() => {
                "toast::success".exec("Saving...");
            })
            .finally(() => {
                ljs.progress.done()
            });
    }


    get url () {
        return this.target.dataset.url;
    }

    get table () {
        return this.target.dataset.table;
    }

    get object () {
        return this.target.dataset.object;
    }

    get confirm () {
        return this.target.dataset.confirm;
    }

    get jax () {
        return this.target.dataset.jax;
    }

    get warning () {
        return this.target.dataset.warning;
    }

    get columns () {
        return JSON.parse(this.target.dataset.columns);
    }

    get openColumns () {
        return JSON.parse(this.target.dataset.openColumns);
    }

    get order () {
        return this.target.dataset.order;
    }

    get orderType () {
        return this.target.dataset.orderType;
    }

    get selectedIds () {
        let ids = [];
        $(`.select_${this.table}:checked`).each((i, obj) => {
            ids.push(Number(obj.value));
        });
        return ids;
    }

    static __name () {

        return "table_action";
    }
};
