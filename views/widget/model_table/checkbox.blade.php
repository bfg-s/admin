<div class="icheck-primary d-inline">
    <input type="checkbox"
        @if($id)
            name="select_{{$table_id}}[{{$id}}]"
            value="{{$id}}"
            id="{{$nId = $table_id . $id}}"
        @else
            name="select_{{$table_id}}"
            id="{{$nId = "select_".$table_id . "_all"}}"
            data-change="table_list::checkChildCheckboxes"
        @endif>
        <label for="{{$nId}}">
    </label>
</div>
