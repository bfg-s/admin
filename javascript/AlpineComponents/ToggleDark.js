Alpine.data('toggleDark', (url) => ({
    url,
    init() {
        const $this = $(this.$el);
    },

    async toggle () {
        NProgress.start();
        const token = exec('token');
        axios.post(this.url, {
            _token: token
        }).then(data => exec(data.data)).finally(d => {
            NProgress.done();
        });
    }
}));
