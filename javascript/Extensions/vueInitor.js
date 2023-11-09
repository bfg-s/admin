window.libs['vueInit'] = function () {
    const $el = this.target;

    let pjax = '#admin-content';
    let parents = pjax ? $el.closest(pjax) : 0;

    let name = $el.tagName.toLowerCase();

    if ($el.hasAttribute('id')) {

        name = $el.getAttribute('id')
    }

    try {

        new Vue({
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
                Vue.drop(this.name, 'components');
            },
            errorCaptured(err, vm, info) {

                if (err) {

                    console.error(err);
                } else {

                    console.log(info);
                }
            }
        });

    } catch (e) {
        console.error(e)
    }
};
