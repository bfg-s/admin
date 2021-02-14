<template>
    <nav id="app-nav-main" class="app-nav app-nav-main flex-grow-1">
        <ul class="app-menu list-unstyled accordion" id="menu-accordion">
            <template v-for="(item, index) in items" :key="`item_${item}`">
                <v-menu-item v-if="!item.parent_id" :item="item" :items="items" :pid="`${pid}-${item.id}`" :ppid="pid" />
            </template>
        </ul>
    </nav>
</template>

<script>
    export default {
        name: "bfg::layout.menu",
        props: {},
        share: {selected_item: 'menu'},
        compute: ['is_page', 'page_title', 'page_icon'],
        data () {
            return {
                pid: 'menu-accordion',
                items: [],
                selected_item: null
            };
        },
        mounted () {},
        computed: {
            is_page () {
                return !!this.selected_item;
            },
            page_title () {
                return this.selected_item && 'title' in this.selected_item ? this.selected_item.title : null;
            },
            page_icon () {
                return this.selected_item && 'icon' in this.selected_item ? this.selected_item.icon : null;
            }
        },
        watch: {},
        methods: {}
    }
</script>