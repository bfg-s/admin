import merge from "lodash/merge";
import find from "lodash/find";
import findKey from "lodash/findKey";

const md5 = require('md5');

Alpine.data('adminModals', (load_modal) => ({
    load_modal: load_modal,
    loading: false,
    modals: [],
    close_events: {},
    refresh_events: {},
    init() {
        const $this = $(this.$el);

        // this.$watch('chose', (val) => {
        //
        // });

        window.libs['modal:hide'] = this.hide.bind(this);
        window.libs['modal:put'] = this.put.bind(this);
        window.libs['modal:die'] = this.die.bind(this);
        window.libs['modal:load'] = this.load.bind(this);
        window.libs['modal:toggle'] = this.toggle.bind(this);
        window.libs['modal:destroy'] = this.destroy.bind(this);
        window.libs['modal:submit'] = this.submit.bind(this);

        this.build();
    },
    modal_cmd(obj, action) {
        return $(obj).modal(action);
    },
    put(handle, params = {}, options = {}) {
        let key = md5(JSON.stringify(params) + handle);
        if (this.get_modal(key)) {
            this.modal_cmd(document.querySelector(`[data-modal-key="${key}"]`), 'toggle');
        } else {
            this.loading = true;
            this.modals.push({
                key: key,
                handle: handle,
                options: options,
                params: params,
                content: ''
            });
            setTimeout(() => this.build(), 500);
        }
    },
    get_modal(key) {
        return this.modals.filter((i) => i.key === key)[0];
    },
    close(event) {
        let obj = event.target.closest('.modal');
        //exec('modal:die', obj);
        this.die(obj);
    },
    refresh(event) {
        let obj = event.target.closest('.modal');
        if (obj.modal) {
            this.loading = true;
            obj.querySelector('.close').removeEventListener('click', this.close);
            obj.querySelector('.refresh_modal').removeEventListener('click', this.refresh);
            $(obj).off('hidden.bs.modal');
            this.load(obj);
            //exec('modal:load', obj);
        }
    },
    die(obj) {
        if (obj.modal) {
            this.modal_cmd(obj, "hide");
            obj.querySelector('.close').removeEventListener('click', this.close);
            obj.querySelector('.refresh_modal').removeEventListener('click', this.refresh);
            delete document.querySelector(`[data-modal-key="${obj.modal.key}"]`);
            document.querySelectorAll('[data-modal]')
            this.modals = this.modals.filter(i => i.key !== obj.modal.key);
            $(obj).off('hidden.bs.modal');
            document.body.style.overflow = '';
        }
    },
    hide(key) {
        let el = document.querySelector(`[data-modal-key="${key}"]`);
        if (el) {
            this.modal_cmd(el, 'hide');
        }
    },
    toggle(key) {
        let el = document.querySelector(`[data-modal-key="${key}"]`);
        if (el) {
            this.modal_cmd(el, 'toggle');
        }
    },
    destroy(key) {
        let el = document.querySelector(`[data-modal-key="${key}"]`);
        if (el) {
            this.die(el);
        }
    },
    submit(key, after = "destroy") {
        let el = document.querySelector(`[data-modal-key="${key}"]`);
        if (el) {
            const form = $(el).find("form[method=get]");
            const data = $(el).find(".modal-content :input").serializeArray();
            const params = {};
            data.map((i) => {
                if (i.name.indexOf('_') !== 0 && i.name !== '_method') {
                    params[i.name] = i.value;
                }
            })

            if (form[0]) {
                exec('location', params);
                this[after](key);
            } else {
                this.loading = true;
                let modal = el.modal;
                axios.post(this.load_modal,  {
                    _modal: modal.handle,
                    _modal_id: modal.key,
                    _modal_submit: 'true',
                    ...modal.params,
                    ...params,
                }).then(data => {
                    data = data.data;
                    exec(data);
                    this.loading = false;
                    this[after](key);
                });
            }
        }
    },
    load(obj) {
        if (obj.modal) {
            let modal = obj.modal;
            let key = modal.key;
            if (! modal.params) {
                modal.params = {};
            }
            this.loading = true;

            axios.post(this.load_modal,  {
                _modal: modal.handle,
                _modal_id: modal.key,
                ...modal.params
            }).then(data => {

                data = data.data;
                modal.content = data.content;
                modal.options.size = data.size;
                this.modal_cmd(obj, 'show');
                $(obj).data('bs.modal')._config.backdrop = !data.backdrop ? 'static' : true;
                $(obj).data('bs.modal')._config.keyboard = data.backdrop;

                let key12 = findKey(this.modals, ['key', modal.key])

                this.modals[key12] = modal;

                this.loading = false;

                $(obj).on('hidden.bs.modal', () => {
                    if (data.temporary) {
                        this.die(obj);
                    }
                });

                setTimeout(() => {
                    window.updateInits();
                }, 100);

            }).catch(({status}) => {
                if (status) exec('reload');
                this.loading = false;
            }).finally(() => {
                this.close_events[key] = obj.querySelector('.close');
                this.refresh_events[key] = obj.querySelector('.refresh_modal');
                if (this.close_events[key]) {
                    this.close_events[key].addEventListener('click', this.close.bind(this));
                }
                if (this.refresh_events[key]) {
                    this.refresh_events[key].addEventListener('click', this.refresh.bind(this));
                }
            });
        }
    },
    build() {
        Object.values(document.querySelectorAll('[data-modal]')).map(d => {

            let key = d.dataset.modalKey;
            let obj = find(this.modals, ['key', key]),
                modal = this.get_modal(key);

            if (modal && !('modal' in obj)) {
                obj.modal = modal;
                let o = this.modal_cmd(d, merge({
                    focus: true,
                    show: false,
                }, modal.options));
                d.modal = obj;
                this.load(d);
            }
        });
    }
}));
