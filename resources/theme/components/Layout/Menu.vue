<template>
    <nav id="app-nav-main" class="app-nav app-nav-main flex-grow-1">
        <ul class="app-menu list-unstyled accordion" id="menu-accordion">
            <template v-for="(item, index) in items" :key="`item_${item}`">
                <v-menu-item :item="item" :pid="`${pid}-${item.id}`" :ppid="pid" root />
            </template>
        </ul>
    </nav>
</template>

<script>
    export default {
        name: "bfg::layout.menu",
        props: {},
        data () {
            return {
                pid: 'menu-accordion',
                items: []
            };
        },
        mounted () {},
        computed: {},
        watch: {},
        methods: {}
    }
</script>