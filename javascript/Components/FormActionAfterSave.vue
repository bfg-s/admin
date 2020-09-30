<template>
    <div class="col">
        <div class="mb-0 clearfix">

            <div class="icheck-primary float-left mr-2">
                <input v-model="chose" name="_after" type="radio" id="_after_select_index" value="index" :checked="select==='index'" />
                <label for="_after_select_index">{{lang.to_the_list}}</label>
            </div>

            <div class="icheck-primary float-left mr-2" v-if="type==='create'">
                <input v-model="chose" name="_after" type="radio" id="_after_select_stay" value="stay" :checked="select==='stay'" />
                <label for="_after_select_stay">{{lang.add_more}}</label>
            </div>

            <div class="icheck-primary float-left mr-2" v-if="type==='edit'">
                <input v-model="chose" name="_after" type="radio" id="_after_select_edit" value="stay" :checked="select==='stay'" />
                <label for="_after_select_edit">{{lang.edit_further}}</label>
            </div>

        </div>
    </div>
</template>

<script>
    export default {
        name: 'form_action_after_save',
        props: ['select', 'type', 'lang'],
        data () {
            return  {
                chose: this.select
            };
        },
        watch: {
            chose (val) {
                $("[type='hidden'][name='_after']").val(val);
                $("[type='radio'][name='_after']").attr('checked', false);
                $(`[type='radio'][name='_after'][value='${val}']`).attr('checked', true);
            }
        },
        mounted() {

        },
        methods: {

        }
    }
</script>