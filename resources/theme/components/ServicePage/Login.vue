<template>
    <div class="app app-login p-0">
        <div class="row g-0 app-auth-wrapper">
            <div class="col-12 auth-main-col text-center p-5">
                <div class="d-flex flex-column align-content-end">
                    <div class="app-auth-body mx-auto">
                        <div class="app-auth-branding mb-4">
                            <a class="app-logo" :href="app.url()">
                                <img class="logo-icon mr-2" :src="app.asset('images/bfg-logo.png')" alt="logo">
                            </a>
                        </div>
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
        data () {
            return {

            };
        },
        mounted () {},
        computed: {},
        watch: {},
        methods: {}
    }
</script>f