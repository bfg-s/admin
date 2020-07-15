<template>
    <div class="form-inline ml-3 d-none d-lg-block d-xl-block" data-load="lte::make_search">
        <div class="input-group input-group-sm">
            <input
                    class="form-control form-control-navbar global_search_input_focus"
                    v-model="q"
                    type="search"
                    placeholder="Search"
                    aria-label="Search"
                    @focus="show=true"
                    @blur="blur"
                    @search="click_cancel"
                    @keyup="key_up_nav"
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
                    :href="item.href" @click="show=true"
                    :key="index"
                    :class="`${i===index?'active':''} select_search_item list-group-item list-group-item-action select_search_item_${index}`"
                    @keyup="key_up_nav"
                >
                    <span v-html="item.inner.replace(/p\>/g, 'span>')"></span>
                    <strong>({{item.total}})</strong>
                </a>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'GlobalSearch',
        data () {
            return {
                q: '',
                show: false,
                items: {},
                links: [],
                timer: null,
                i: null,
                rc: 1
            };
        },
        watch: {
            q () {
                if (this.timer) {
                    clearTimeout(this.timer);
                }

                this.timer = setTimeout(() => {
                    this.run_search();
                }, 500);
            },
            i (val) {
                if (val!==null) {
                    $(`.select_search_item_${val}`).focus();
                } else {
                    $('.global_search_input_focus').focus();
                }
            }
        },
        mounted () {

            document.querySelector('[role="menu"]')
                .querySelectorAll('a[href^="http"]')
                .forEach((obj) => {
                    this.links.push({href: obj.href, inner: obj.innerHTML});
                });

            let q = ljs.help.query_get('q');
            this.q = q ? q : '';
        },
        methods: {
            key_up_nav (e) {
                e.preventDefault();
                let last = this.items.length-1;
                if (e.keyCode === 38) {
                    if (last > 0) {
                        if (this.i === null && this.items[last]) {this.i = last;}
                        else if (this.items[this.i-1]) {this.i--;}
                        else {this.i=null;}
                    }
                }
                if (e.keyCode === 40) {
                    if (this.i === null && this.items[0]) {this.i = 0;}
                    else if (this.items[this.i+1]) {this.i++;}
                    else {this.i=null;}
                }
            },
            click_cancel (e) {
                setTimeout(() => {
                    let cancel_btn = $('#cancel_search_params');
                    if (!this.q.length && cancel_btn[0] !== undefined) {
                        cancel_btn.trigger('click')
                    }
                })
            },
            blur (e) {

                if (e.relatedTarget && e.relatedTarget.classList.contains('select_search_item')) {

                    this.show = true;
                }

                else {

                    this.show = false;
                }
            },
            run_search () {

                this.items = [];
                this.rc = 1;

                if (this.q.length) {

                    this.links.forEach((link, ind) => {

                        $jax.get(link.href, {q: this.q})
                            .then((r) => {
                                if (r.total) {
                                    this.items.push({
                                        inner: link.inner,
                                        href: `${link.href}?q=${this.q}`,
                                        total:  r.total
                                    });
                                }
                                let set_p = (1/this.links.length)*this.rc;
                                ljs.progress.set(set_p);
                                console.log(set_p);
                                this.rc++;
                            })
                            .catch(() => {
                                let set_p = (1/this.links.length)*this.rc;
                                ljs.progress.set(set_p);
                                console.log(set_p);
                                this.rc++;
                            });
                    });
                }
            }
        }
    }
</script>