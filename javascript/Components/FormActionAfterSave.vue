<template>
    <div class="col">
        <div class="mb-0 clearfix">

            <div class="icheck-primary float-left mr-2">
                <input id="_after_select_index" v-model="chose" :checked="select==='index'" name="_after" type="radio"
                       value="index"/>
                <label for="_after_select_index">{{ lang.to_the_list }}</label>
            </div>

            <div v-if="type==='create'" class="icheck-primary float-left mr-2">
                <input id="_after_select_stay" v-model="chose" :checked="select==='stay'" name="_after" type="radio"
                       value="stay"/>
                <label for="_after_select_stay">{{ lang.add_more }}</label>
            </div>

            <div v-if="type==='edit'" class="icheck-primary float-left mr-2">
                <input id="_after_select_edit" v-model="chose" :checked="select==='stay'" name="_after" type="radio"
                       value="stay"/>
                <label for="_after_select_edit">{{ lang.edit_further }}</label>
            </div>

        </div>
    </div>
</template>

<script>
export default {
    name: 'form_action_after_save',
    props: ['select', 'type', 'lang'],
    data() {
        return {
            chose: this.select
        };
    },
    watch: {
        chose(val) {
            $("[type='hidden'][name='_after']").val(val);
            $("[type='radio'][name='_after']").attr('checked', false);
            $(`[type='radio'][name='_after'][value='${val}']`).attr('checked', true);
        }
    },
    mounted() {

    },
    methods: {}
}
</script>
