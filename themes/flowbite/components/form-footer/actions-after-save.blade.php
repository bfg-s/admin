<div class="col" x-data="actionAfterSave('{{ $select }}', '{{ $type }}', '{{ $lang_to_the_list }}', '{{ $lang_add_more }}', '{{ $lang_edit_further }}')">
    <div class="mb-0 clearfix">

        <div class="icheck-primary float-left mr-2">
            <input id="_after_select_index" x-model="chose" :checked="select==='index'" name="_after" type="radio"
                   value="index"/>
            <label for="_after_select_index" x-text="to_the_list"></label>
        </div>

        <template x-if="type==='create'">
            <div class="icheck-primary float-left mr-2">
                <input id="_after_select_stay" x-model="chose" :checked="select==='stay'" name="_after" type="radio"
                       value="stay"/>
                <label for="_after_select_stay" x-text="add_more"></label>
            </div>
        </template>

        <template x-if="type==='edit'">
            <div class="icheck-primary float-left mr-2">
                <input id="_after_select_edit" x-model="chose" :checked="select==='stay'" name="_after" type="radio"
                       value="stay"/>
                <label for="_after_select_edit" x-text="edit_further"></label>
            </div>
        </template>
    </div>
</div>
