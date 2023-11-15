Alpine.data('toggleDark', (url) => ({
    url,
    init() {
        const $this = $(this.$el);
    },

    toggle () {
        NProgress.start();
        axios.post(this.url, {
            _token: exec('token')
        }).then(data => exec(data.data)).finally(d => {
            NProgress.done();
        });
    }
}));
