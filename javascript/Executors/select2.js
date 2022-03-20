const merge = require('lodash/merge');
const get = require('lodash/get');

module.exports = class Select2 extends Executor {

        constructor(ljs) {
            super(ljs);

            this.jquery = true;
        }

        static __name() {

            return "select2";
        }

        /**
         * Create simple select2 alias
         * @private
         */
        __invoke(args = {}) {

            return this.init(args);
        }

        /**
         * Create simple select2
         * @param args
         */
        init(args = {}) {

            if (this.target) {

                let target = this.target;

                return $(this.target).select2(merge({
                    theme: target.dataset.theme ? target.dataset.theme : 'default',
                }, args));
            }

            return undefined;
        }

        ajax() {

            let target = this.target;

            return this.init(merge({
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
                                    if (i.name !== '_method')
                                        form[i.name] = i.value;
                                })
                            }
                            new_params[`${name}_form`] = form;
                        }

                        let data = $(":input").serializeArray();

                        data.map(({name, value}) => {
                            if (String(name)[0] !== '_') {
                                if (name.indexOf("q[") !== 0 && name !== '_method')
                                    new_params[name] = value;
                            }
                        });

                        window.$jax.head(window.location.href, new_params)
                            .then((data) => {
                                success(data);
                            }).catch(() => failure());
                    }
                }
            }));
        }

        /**
         * Create jaxible select
         * @param jax_path
         * @param $args
         * @param $ja_args
         */
        jax(jax_path, $args = [], $ja_args = null) {

            if (jax_path !== '') {

                let target = this.target;

                return this.init(merge({
                    ajax: {
                        transport: function (params, success, failure) {

                            let withs = target.dataset.with !== undefined ? target.dataset.with.split(',') : [];

                            let ljs_params = Object.assign({}, params.data);

                            if (params.data.page) {
                                ljs_params['_page'] = params.data.page;
                                delete ljs_params.page;
                            }

                            if (params.data.q) {
                                ljs_params['_q'] = params.data.q;
                                delete ljs_params.q;
                            }

                            let ja = window.jax.with(withs).params(ljs_params);

                            ja = get(ja, jax_path);

                            if (typeof $ja_args === 'function') {

                                $ja_args = $ja_args();
                            }

                            return ja($ja_args).then((data) => {

                                if (data !== undefined) {

                                    success(data);
                                } else {

                                    //failure()
                                    success({results: []});
                                }

                                return data;

                            }).catch(() => failure());
                        }
                    }
                }, $args));
            }

            return undefined;
        }

        /**
         * Create if not exists
         * @param id
         * @param text
         * @param selected
         */
        set(id, text, selected = true) {

            let select2 = $(this.target);

            if (id === select2.val()) {

                return select2;
            }

            if (select2.find("option[value='" + id + "']").length) {

                if (selected) {

                    select2.val(id).trigger('change');
                }
            } else {

                select2.append(
                    new Option(text, id, selected, selected)
                );

                if (selected) {

                    select2.trigger('change');
                }
            }

            return select2;
        }
    }

