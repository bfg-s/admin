<template>
    <div class="sidebar">
        <div class="sidebar-inner">
            <div class="user-panel mt-3 pb-3 mb-3 d-flex" :data-href="ljs.route('lte.profile')">
                <div class="image">
                    <img :src="`/${user_admin.avatar}`" class="img-circle elevation-2" :alt="user_admin.name">
                </div>
                <div class="info">
                    <a class="d-block">{{user_admin.name}}</a>
                    <small class="d-block">
                        <span class="badge badge-success" v-for="r in user_admin.roles">{{r.name}}</span>
                    </small>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import filter from 'lodash/filter';

export default {
        name: 'v-navigator',
        props: ['items'],
        data () {
            return {

            };
        },
        computed: {
            roots () { return filter(this.items, {'parent_id': 0}); }
        },
        mounted() {
            console.log(this.items);
            console.log(this.roots);
        }
    }
</script>
