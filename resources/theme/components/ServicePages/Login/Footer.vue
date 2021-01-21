<template>
    <footer class="app-auth-footer">
        <div class="container text-center py-3">
            <small class="copyright" v-html="copy"></small>
        </div>
    </footer>
</template>

<script>
    export default {
        name: "aui::servicePages.login.footer",
        props: {},
        data () {
            return {
                copy: null,
                time: null,
            };
        },
        mounted () {},
        computed: {},
        watch: {},
        methods: {}
    }
</script>f