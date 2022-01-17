<template>
    <span>
        <v-loading :active="loading" is-full-screen color="#FFFFFF" background-color="rgba(0, 0, 0, 0.3)" />
        <div
            v-for="(modal, index) in modals"
            :key="modal.key"
            :ref="modal.key"
            class="modal fade"
        ><div
            :class="{
                'modal-dialog': true,
                'modal-xl': modal.options.size === 'extra',
                'modal-lg': modal.options.size === 'big',
                'modal-sm': modal.options.size === 'small',
            }" v-html="modal.content"
        ></div></div>
    </span>
</template>

<script>

import merge from 'lodash/merge';

export default {
        name: 'modal',
        $exec: ['put', 'hide', 'toggle', 'destroy'],
        $remember: ['modals'],
        data () {
            return {
                loading: false,
                modals: [],
                close_events: {},
                refresh_events: {},
            };
        },
        mounted() {
            this.build();
        },
        methods: {
            modal_cmd (obj, action) {
                return $(obj).modal(action);
            },
            put (handle, params = {}, options = {}) {
                let key = ljs.help.md5(JSON.stringify(params) + handle);
                if (this.get_modal(key)) {
                    this.modal_cmd(this.$refs[key], 'toggle');
                } else {
                    this.loading = true;
                    this.modals.push({
                        key: key,
                        handle: handle,
                        options: options,
                        params: params,
                        content: ''
                    });
                }
            },
            get_modal (key) {
                return this.modals.filter((i) => i.key === key)[0];
            },
            close (event) {
                let obj = event.target.closest('.modal');
                this.die(obj);
            },
            refresh (event) {
                let obj = event.target.closest('.modal');
                if (obj.modal) {
                    this.loading = true;
                    obj.querySelector('.close').removeEventListener('click', this.close);
                    obj.querySelector('.refresh_modal').removeEventListener('click', this.refresh);
                    $(obj).off('hidden.bs.modal');
                    this.load(obj);
                }
            },
            die (obj) {
                if (obj.modal) {
                    this.modal_cmd(obj, "hide");
                    obj.querySelector('.close').removeEventListener('click', this.close);
                    obj.querySelector('.refresh_modal').removeEventListener('click', this.refresh);
                    delete this.$refs[obj.modal.key];
                    this.modals = this.modals.filter(i => i.key !== obj.modal.key);
                    $(obj).off('hidden.bs.modal');
                }
            },
            hide (key) {
                if (key in this.$refs) {
                    this.modal_cmd(this.$refs[key], 'hide');
                }
            },
            toggle (key) {
                if (key in this.$refs) {
                    this.modal_cmd(this.$refs[key], 'toggle');
                }
            },
            destroy (key) {
                if (key in this.$refs) {
                    this.die(this.$refs[key][0]);
                }
            },
            load (obj) {
                if (obj.modal) {
                    let modal = obj.modal;
                    let key = modal.key;
                    jax.param('modal', modal.key)
                        .params(typeof modal.params === 'object' && !Array.isArray(modal.params) ? modal.params : {})
                        .lte_admin
                        .load_modal(modal.handle, typeof modal.params === 'object' && Array.isArray(modal.params) ? modal.params : [])
                        .then(data => {

                            modal.content = data[0];
                            this.modal_cmd(obj, 'show');
                            this.loading = false;

                            $(obj).on('hidden.bs.modal', () => {
                                if (obj.querySelector('[data-temporary]')) {
                                    this.die(obj);
                                }
                            });

                            setTimeout(() => {
                                obj.querySelectorAll('[data-load]').forEach((load_obj) => {
                                    new window.HTMLDataEvent('load', {target: load_obj, currentTarget: load_obj});
                                });
                            }, 100);

                        }).catch(({status}) => {
                            if (status) "doc::reload".exec();
                            this.loading = false;
                        }).finally(() => {
                            this.close_events[key] = obj.querySelector('.close');
                            this.refresh_events[key] = obj.querySelector('.refresh_modal');
                            if (this.close_events[key]) {
                                this.close_events[key].addEventListener('click', this.close);
                            }
                            if (this.refresh_events[key]) {
                                this.refresh_events[key].addEventListener('click', this.refresh);
                            }
                        });
                }
            },
            build () {
                this.$nextTick(() => {
                    Object.keys(this.$refs).map(key => {

                        let obj = this.$refs[key][0],
                            modal = this.get_modal(key);

                        if (modal && !('modal' in obj)) {
                            obj.modal = modal;
                            let o = this.modal_cmd(obj, merge({
                                focus: true,
                                show: false,
                                backdrop: 'static'
                            }, modal.options));
                            this.load(obj);
                        }
                    });
                })
            }
        },
        destroyed() {
            Object.keys(this.close_events).map(e => {
                let obj = this.close_events[e];
                if (obj) obj.removeEventListener('click', this.close);
            });
            Object.keys(this.$refs).map(h => {
                let obj = this.$refs[h];
                $(obj).off('hidden.bs.modal');
                if (obj) this.modal_cmd(obj, 'dispose');
            });
        },
        updated () {
            this.build();
        }
    }
</script>
