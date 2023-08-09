<template>
    <div class="form-inline ml-3 d-none d-lg-block d-xl-block">
        <div class="input-group input-group-sm">
            <input
                v-model="q"
                aria-label="Search"
                class="form-control form-control-navbar global_search_input_focus"
                placeholder="Search"
                type="search"
                @blur="blur"
                @mouseup="show_if_has"
                @keyup="key_up_nav"
                @keyup.enter.prevent="got_to_search"
                @search="click_cancel"
            >
            <div class="input-group-append">
                <button class="btn btn-navbar" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>

        <div v-if="q.length && show" class="search_result">
            <div class="list-group">
                <a
                    v-for="(item, index) in items"
                    :key="index" :class="`${i===index?'active':''} select_search_item list-group-item list-group-item-action select_search_item_${index}`"
                    :href="item.href"
                    @click="show=true"
                    @keyup="key_up_nav"
                >
                    <span v-html="item.inner.replace(/p\>/g, 'span>')"></span>
                    <strong>({{ item.total }})</strong>
                </a>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'GlobalSearch',
    data() {
        return {
            q: '',
            show: false,
            items: {},
            links: [],
            timer: null,
            i: null,
            rc: 1,
            created_with_q: false
        };
    },
    watch: {
        q() {
            if (this.timer) {
                clearTimeout(this.timer);
            }

            this.timer = setTimeout(() => {
                this.run_search();
                if (this.q.length && window.location.search.indexOf('?q') === -1) {
                    this.show = true;
                }
            }, 500);
        },
        i(val) {
            if (val !== null) {
                $(`.select_search_item_${val}`).focus();
            } else {
                $('.global_search_input_focus').focus();
            }
        }
    },
    mounted() {

        let menu = document.querySelector('[role="menu"]');

        if (menu) {

            menu.querySelectorAll('a[href^="http"]')
                .forEach((obj) => {
                    if (obj.dataset.ignore !== '1') {
                        this.links.push({href: obj.href, inner: obj.innerHTML});
                    }
                });

            let q = ljs.help.query_get('q');
            if (q) {
                this.q = q;
                this.created_with_q = true;
            }
        }
    },
    methods: {
        got_to_search () {
            "doc::location".exec({q: this.q});
        },
        key_up_nav(e) {
            e.preventDefault();
            let last = this.items.length - 1;
            if (e.keyCode === 38) {
                if (last > 0) {
                    if (this.i === null && this.items[last]) {
                        this.i = last;
                    } else if (this.items[this.i - 1]) {
                        this.i--;
                    } else {
                        this.i = null;
                    }
                }
            }
            if (e.keyCode === 40) {
                if (this.i === null && this.items[0]) {
                    this.i = 0;
                } else if (this.items[this.i + 1]) {
                    this.i++;
                } else {
                    this.i = null;
                }
            }
        },
        click_cancel(e) {
            setTimeout(() => {
                let cancel_btn = $('#cancel_search_params');
                if (!this.q.length && cancel_btn[0] !== undefined) {
                    cancel_btn.trigger('click')
                }
            })
        },
        blur(e) {

            if (e.relatedTarget && e.relatedTarget.classList.contains('select_search_item')) {

                this.show = true;
            } else {

                this.show = false;
            }
        },
        show_if_has () {
            if (this.items.length) {
                this.show = true;
            }
        },
        run_search() {

            this.items = [];
            this.rc = 1;

            if (this.q.length) {

                this.links.forEach((link, ind) => {

                    $jax.get(link.href, {q: this.q, format: 'json'})
                        .then((r) => {
                            if (r.total) {
                                const urlObj = new URL(link.href);
                                this.items.push({
                                    inner: link.inner,
                                    href: `${urlObj.origin}${urlObj.pathname}?q=${this.q}`,
                                    total: r.total
                                });
                            }
                            let set_p = (1 / this.links.length) * this.rc;
                            if (!this.created_with_q) {
                                ljs.progress.set(set_p);
                            }
                            if (this.created_with_q && set_p >= 1) {
                                this.created_with_q = false;
                            }
                            this.rc++;
                        })
                        .catch(() => {
                            let set_p = (1 / this.links.length) * this.rc;
                            if (!this.created_with_q) {
                                ljs.progress.set(set_p);
                            }
                            if (this.created_with_q && set_p >= 1) {
                                this.created_with_q = false;
                            }
                            this.rc++;
                        });
                });
            }
        }
    }
}
</script>
