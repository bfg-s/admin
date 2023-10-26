Alpine.data('liveReloader', () => ({
    show: false,
    play: false,
    interval: 2,
    intervals: [2, 5, 10, 30, 60, 120],
    timer: null,
    script_timer: null,
    init() {
        const $this = $(this.$el);

        this.$watch('play', (value) => this.run_script(value))
        this.$watch('interval', () => {
            if (this.play) {
                if (this.script_timer) clearInterval(this.script_timer);
                this.run_script(true)
            }
        })
    },
    run_script(state = this.play) {
        if (state) {
            this.script_timer = setInterval(() => {
                exec('reload');
            }, this.interval * 1000);
        } else {
            if (this.script_timer) clearInterval(this.script_timer);
        }
    },
    pp() {
        this.play = !this.play;
    },
    si(interval) {
        this.interval = interval;
        this.show = false;
    },
    mouse_down() {
        this.clearTimer();
        this.timer = setTimeout(() => {
            this.show = !this.show;
        }, 300);
    },
    mouse_up() {
        this.clearTimer();
    },
    clearTimer() {
        if (this.timer) {
            clearTimeout(this.timer);
        }
    }
}));
