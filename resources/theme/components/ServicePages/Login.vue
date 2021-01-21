<template>
    <div class="app app-login p-0">
        <div class="row g-0 app-auth-wrapper">
            <div class="col-12 auth-main-col text-center p-5">
                <div class="d-flex flex-column align-content-end">
                    <div class="app-auth-body mx-auto">
                        <h2 class="auth-heading text-center mb-5">Admin Panel</h2>
                        <div class="auth-form-container text-left">
                            <slot></slot>
                        </div>
                    </div>
                    <slot name="footer"></slot>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "aui::servicePages.login",
        props: {},
        save: ['time'],
        share: ['time'],
        data () {
            return {
                time: 0
            };
        },
        mounted () {
            setInterval(() => {
                this.time = +new Date;
            }, 1000);
        },
        computed: {},
        watch: {},
        methods: {}
    }
</script>f