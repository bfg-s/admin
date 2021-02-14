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
        name: "bfg::servicePages.login",
        props: {},
        data () {
            return {
                //time: null
            };
        },
        mounted () {
            // this.time = setInterval(() => {
            //     console.log(1);
            // }, 1000);
        },
        beforeUnmount() {
            //clearInterval(this.time);
        },
        computed: {},
        watch: {},
        methods: {}
    }
</script>