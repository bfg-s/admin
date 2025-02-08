@if (!$id)
    <div class="icheck-primary d-inline" style="margin-left: -3px;">
        <input type="checkbox"
               class="global_select_{{$table_id}}"
               name="select_{{$table_id}}"
               id="{{$nId = "select_".$table_id . "_all"}}"
               data-change="table_list::invertByCheckChildCheckboxes">
        <label for="{{$nId}}">
        </label>
    </div>
@else
    <div class="icheck-primary d-inline">
        <input type="checkbox"
               class="select_{{$table_id}} action-selector"
               name="select_{{$table_id}}[{{$id}}]"
               value="{{$id}}"
               data-table="{{$table_id}}"
               @if($disabled) disabled="true" @endif
               id="{{$nId = $table_id . $id}}"
               data-change="table_list::primaryChange">
        <label for="{{$nId}}">
        </label>
    </div>
@endif
