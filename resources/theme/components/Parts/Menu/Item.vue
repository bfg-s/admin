<template>
    <li :class="{'nav-item': true, 'has-submenu': has_sub_menu, 'submenu-item': has_parent}">
        <a @click="click" :class="{'nav-link': !has_parent, 'submenu-toggle': has_sub_menu, 'submenu-link': has_parent, 'active': active}" :href="href" v-bind="collapse_attributes">
            <span v-if="item.icon && !has_parent" class="nav-icon">
                <i :class="item.icon"></i>
            </span>
            <span class="nav-link-text">{{item.title}}</span>
            <span v-if="has_sub_menu" class="submenu-arrow">
                <i class="bi bi-chevron-down"></i>
            </span>
        </a>
        <div v-if="has_sub_menu" :id="pid" :class="{'collapse submenu submenu-2': true, show: ch}" :data-parent="`#${ppid}`">
            <ul class="submenu-list list-unstyled">
                <template v-for="(child, index) in child" :key="`item_${item}`">
                    <v-menu-item :item="child" :items="items" :ppid="pid" :pid="pid+'-'+child.id" :select-parent="selectThis" />
                </template>
            </ul>
        </div>
    </li>
</template>

<script>
    export default {
        name: 'v-menu-item',
        emits: ['update:select'],
        props: {
            item: {required: true},
            items: {required: true},
            pid: {required: true},
            ppid: {default: ''},
            selectParent: {},
        },
        mixins: [],
        share: {selected_item: 'menu'},
        data () {
            return {
                selected_item: null,
                ch: false
            };
        },
        mounted () {
            if (this.active) {
                this.$emit('update:select', true);
                this.selected_item = this.item;
                if (this.selectParent) this.selectParent();
            }
        },
        computed: {
            // ch () {
            //     return this.selected_item ? this.item.id === this.selected_item.id : false;
            // },
            active () {
                return this.selected_item ? this.item.id === this.selected_item.id : this.$root.app.server.route === this.item.route;
            },
            href () {
                if (this.item.type === 'link') {
                    if (
                        this.item.action.indexOf("://") >= 0 ||
                        /javascript\:.*/.test(this.item.action)
                    ) {
                        return this.item.action;
                    } else {
                        return this.app.url(this.item.action);
                    }
                }
                return 'javascript:;';
            },
            collapse_attributes () {

                if (!this.has_sub_menu) { return {}; }

                return {
                    'data-toggle': 'collapse',
                    'data-target': '#' + this.pid,
                    'aria-expanded': 'false',
                    'aria-controls': this.pid
                };
            },
            has_sub_menu () {
                return !!this.app.obj.find(this.items, ['parent_id', this.item.id]);
            },
            has_parent () {
                return !!this.item.parent_id;
            },
            child () {
                return this.app.obj.filter(this.items, ['parent_id', this.item.id]);
            }
        },
        watch: {},
        methods: {
            click (e) {
                if (!this.has_sub_menu) {
                    e.preventDefault();
                    if (this.item.route) {
                        this.$root.app.doc.location(this.item.action);
                    }

                    this.selected_item = this.item;
                } else {
                    this.ch = !this.ch;
                }
            },
            selectThis (ch = true) {
                this.ch = ch;
                if (this.selectParent) this.selectParent(this.ch);
            }
        }
    }
</script>