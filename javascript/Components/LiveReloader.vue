<template>
    <ul class="navbar-nav">
        <li :class="{'nav-item': true}">
            <button class="nav-link btn btn-link" title="Live reloader" type="button" @click="pp">
                <i :class="{fas: true, 'fa-play': !play, 'fa-pause': play}"></i>
            </button>
        </li>
        <li :class="{'nav-item': true, dropdown: true}" style="margin-left: -10px;">
            <button aria-expanded="false" aria-haspopup="true" class="btn btn-link nav-item"
                    data-reference="parent" data-toggle="dropdown" style="padding-left: 0; padding-bottom: 0" type="button">
                {{ interval }} sec
            </button>
            <div :class="{'dropdown-menu': true}">
                <a v-for="(i,k) in intervals" :key="k" :class="{'dropdown-item': true, active: interval===i}"
                   href="javascript:void(0)" @click="si(i)">
                    {{ i }} sec
                </a>
            </div>
        </li>
    </ul>
</template>

<script>
export default {
    name: 'live_reloader',
    $remember: ['interval', 'play'],
    data() {
        return {
            show: false,
            play: false,
            interval: 2,
            intervals: [2, 5, 10, 30, 60],
            timer: null,
            script_timer: null,
        };
    },
    watch: {
        play(val) {
            this.run_script(val);
        }
    },
    mounted() {
        this.run_script();
    },
    methods: {
        run_script(state = this.play) {
            if (state) {
                this.script_timer = setTimeout(() => {
                    clearTimeout(this.script_timer);
                    this.script_timer = null;
                    "doc::reload".exec();
                }, this.interval * 1000);
            } else {
                if (this.script_timer) clearTimeout(this.script_timer);
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
    }
}
</script>
