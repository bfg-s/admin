const get = require('lodash/get');

module.exports = class extends Executor {

    __invoke () {

        let ids = [];

        $(`.select_${this.table}:checked`).each((i, obj) => {
            ids.push(Number(obj.value));
        });

        if (ids.length && this.jax) {

            let call_jax = () => {
                jax.path(this.jax, this.object, ids, this.columns)
                    .then(() => {
                        $(`.select_${this.table}:checked`).each((i, obj) => {
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
        }
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

    get columns () {
        return JSON.parse(this.target.dataset.columns);
    }

    static __name () {
    
        return "table_action";
    }
};