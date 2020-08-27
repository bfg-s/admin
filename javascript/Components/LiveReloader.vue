<template>
    <ul class="navbar-nav">
        <li :class="{'nav-item': true}">
            <button type="button" @click="pp" class="nav-link btn btn-link" title="Live reloader">
                <i :class="{fas: true, 'fa-play': !play, 'fa-pause': play}"></i>
            </button>
        </li>
        <li :class="{'nav-item': true, dropdown: true}" style="margin-left: -10px;">
            <button style="padding-left: 0; padding-bottom: 0" type="button" class="btn btn-link nav-item" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-reference="parent">
                {{interval}} sec
            </button>
            <div :class="{'dropdown-menu': true}">
                <a v-for="(i,k) in intervals" :key="k" @click="si(i)" :class="{'dropdown-item': true, active: interval===i}" href="javascript:void(0)">
                    {{i}} sec
                </a>
            </div>
        </li>
    </ul>
</template>

<script>
    export default {
        name: 'live_reloader',
        $remember: ['interval', 'play'],
        data () {
            return  {
                show: false,
                play: false,
                interval: 2,
                intervals: [2,5,10,30,60],
                timer: null,
                script_timer: null,
            };
        },
        watch: {
            play (val) {
                this.run_script(val);
            }
        },
        mounted() {
            this.run_script();
        },
        methods: {
            run_script (state = this.play) {
                if (state) {
                    this.script_timer = setTimeout(() => {
                        clearTimeout(this.script_timer);
                        this.script_timer = null;
                        "doc::reload".exec();
                    }, this.interval*1000);
                } else {
                    if (this.script_timer) clearTimeout(this.script_timer);
                }
            },
            pp () {
                this.play=!this.play;
            },
            si (interval) {
                this.interval = interval;
                this.show = false;
            },
            mouse_down () {
                this.clearTimer();
                this.timer = setTimeout(() => {
                    this.show = !this.show;
                }, 300);
            },
            mouse_up () {
                this.clearTimer();
            },
            clearTimer () {
                if (this.timer) {
                    clearTimeout(this.timer);
                }
            }
        }
    }
</script>