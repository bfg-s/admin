<template>
    <div class="app-branding">
        <a class="app-logo" :href="app.url()">
            <img v-if="logo" class="logo-icon mr-2" :src="logo" alt="logo">
            <span v-if="project" class="logo-text">{{project}}</span>
        </a>
    </div>
</template>

<script>
    export default {
        name: "bfg::layout.logo",
        props: {},
        data () {
            return {
                logo: null,
                project: null,
            };
        },
        mounted () {},
        computed: {},
        watch: {},
        methods: {}
    }
</script>