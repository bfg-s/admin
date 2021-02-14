<template>
    <div class="app-wrapper">
        <div class="app-content pt-3 p-md-3 p-lg-4">
            <div class="container-xl">
                <h1 v-if="app.is_page" class="app-page-title">
                    <i :class="app.page_icon"></i>
                    {{app.page_title}}
                </h1>
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
        share: ['menu'],
        data () {
            return {
                menu: {}
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