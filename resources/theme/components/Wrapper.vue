<template>
    <div class="app-wrapper">
        <div class="app-content pt-3 p-md-3 p-lg-4">
            <div class="container-xl">
                <h1 class="app-page-title">Overview</h1>
                <div :id="app.server.container">
                    <slot></slot>
                </div>
            </div>
        </div>
        <slot name="footer"></slot>
    </div>
</template>

<script>
    export default {
        name: "bfg::wrapper",
        props: {},
        data () {
            return {

            };
        },
        mounted () {

            this.app.initTemplate;
        },
        computed: {},
        watch: {},
        methods: {}
    }
</script>