<template>
    <li :class="{'nav-item': true, 'has-submenu': has_sub_menu, 'submenu-item': has_parent}">
        <a @click="click" :class="{'nav-link': true, 'submenu-toggle': has_sub_menu, 'submenu-link': has_parent, 'active': active}" :href="href" v-bind="collapse_attributes">
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
                <template v-for="(child, index) in item.childs" :key="`item_${item}`">
                    <v-menu-item :item="child" :ppid="pid" v-model:select="ch" :pid="pid+'-'+child.id" />
                </template>
            </ul>
        </div>
    </li>
</template>

<script>
    export default {
        name: 'v-menu-item',
        props: {
            item: {required: true},
            pid: {required: true},
            ppid: {default: ''},
            root: {default: false},
        },
        share: {selected: 'selected_menu_id'},
        save: {selected: 'selected_menu_id'},
        data () {
            return {
                selected: null,
                ch: false,
            };
        },
        mounted () {
            if (this.active) {
                this.$emit('update:select', true);
            }
        },
        computed: {
            active () {
                return this.$root.app.server.route === this.item.route;
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
                return !!this.item.childs.length;
            },
            has_parent () {
                return !!this.item.parent;
            }
        },
        watch: {
            ch (val) {
                if (val) {
                    this.$emit('update:select', true);
                }
            }
        },
        methods: {
            click (e) {
                if (!this.has_sub_menu) {
                    e.preventDefault();
                    if (this.item.route) {
                        this.$root.app.doc.location(this.item.action);
                    }
                }
            }
        }
    }
</script>