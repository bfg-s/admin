Alpine.data('toggleDark', (url) => ({
    url,
    init() {
        const $this = $(this.$el);
    },

    toggle () {
        axios.post(this.url, {
            _token: exec('token')
        }).then(data => exec(data.data));
    }
}));
