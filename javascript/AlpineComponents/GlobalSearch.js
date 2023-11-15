Alpine.data('globalSearch', () => ({
    q: '',
    show: false,
    items: {},
    links: [],
    timer: null,
    i: null,
    rc: 1,
    created_with_q: false,
    init () {

        let menu = document.querySelector('[role="menu"]');

        if (menu) {

            menu.querySelectorAll('a[href^="http"]')
                .forEach((obj) => {
                    if (obj.dataset.ignore !== '1') {

                        this.links.push({
                            href: obj.href,
                            inner: this.strip_tags(obj.innerHTML).trim(),
                            icon: obj.querySelector('i').getAttribute('class')
                        });
                    }
                });

            let q = this.query_get('q');
            if (q) {
                this.q = q;
                this.created_with_q = true;
                this.run_search();
            }
        }

        this.$watch('q', (val) => {
            if (this.timer) {
                clearTimeout(this.timer);
            }

            this.timer = setTimeout(() => {
                this.run_search();
                if (val.length && window.location.search.indexOf('?q') === -1) {
                    this.show = true;
                }
            }, 500);
        });

        this.$watch('i', (val) => {
            if (val !== null) {
                $(`.select_search_item_${val}`).focus();
            } else {
                $('.global_search_input_focus').focus();
            }
        });

        document.addEventListener('click', this.hideResult);
    },
    strip_tags( str ){
        return str.replace(/<\/?[^>]+>/gi, '');
    },
    destroy() {
        document.removeEventListener('click', this.hideResult);
    },
    hideResult () {
        this.show = false;
    },
    got_to_search () {
        exec("location", {q: this.q});
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
                axios.get(String(link.href), {
                    params: {q: this.q, format: 'json'}
                }).then((r) => {

                        r = r.data;

                        if (r.total) {
                            const urlObj = new URL(link.href);
                            this.items.push({
                                icon: link.icon,
                                inner: link.inner,
                                href: `${urlObj.origin}${urlObj.pathname}?q=${this.q}`,
                                total: r.total
                            });
                        }
                        let set_p = (1 / this.links.length) * this.rc;
                        if (!this.created_with_q) {
                            NProgress.set(set_p);
                        }
                        if (this.created_with_q && set_p >= 1) {
                            this.created_with_q = false;
                        }
                        this.rc++;
                    })
                    .catch(() => {
                        let set_p = (1 / this.links.length) * this.rc;
                        if (!this.created_with_q) {
                            NProgress.set(set_p);
                        }
                        if (this.created_with_q && set_p >= 1) {
                            this.created_with_q = false;
                        }
                        this.rc++;
                    });
            });
        }
    },
    query_get(name = null) {

        let match,
            pl = /\+/g,
            search = /([^&=]+)=?([^&]*)/g,
            decode = (s) => decodeURIComponent(s.replace(pl, " ")),
            query = window.location.search.substring(1);

        let urlParams= {};

        while (match = search.exec(query)) {

            urlParams[decode(match[1])] = decode(match[2]);
        }

        if (name) {

            return urlParams[name];
        } else {

            return urlParams;
        }
    }
}));
