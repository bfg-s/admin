window.initedVueForPjsxMoveDestroy = [];
window.initedVue = [];

window.libs['vueInit'] = function () {
    const $el = this.target;
    $el.removeAttribute('data-load');
    if ($el.__vue) {
        return ;
    }
    let destroyable = false;
    if ($el.closest('[data-update-with-pjax]')) {
        destroyable = true;
    }
    let pjax = '#admin-content';
    let parents = pjax ? $el.closest(pjax) : 0;

    let name = $el.tagName.toLowerCase();

    $el.setAttribute('data-vue-num', window.initedVue.length);

    if ($el.hasAttribute('id')) {

        name = $el.getAttribute('id')
    }

    try {

        const component = new Vue({
            el: $el,
            data() {

                return {
                    name: name,
                    pjax: !!(pjax && parents)
                }
            },
            methods: {},
            mounted() {

                if (process.env.NODE_ENV === 'development') {
                    console.log(`Vue component [${this.name}] mounted!`);
                }
            },
            destroyed() {
                if (process.env.NODE_ENV === 'development') {
                    console.log(`Vue component [${this.name}] destroyed!`);
                }
            },
            errorCaptured(err, vm, info) {

                if (err) {

                    console.error(err);
                } else {

                    console.log(info);
                }
            }
        });

        if (destroyable) {
            window.initedVueForPjsxMoveDestroy.push(component);
        }

        window.initedVue.push(component);

        $el.__vue = component;
    } catch (e) {
        console.error(e)
    }
};
