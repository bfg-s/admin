@if (!$id)
    <div class="btn-group" style="margin: -7px;">
        <button class="btn btn-link  p-0 dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="margin-left: -8px;margin-right: 5px;"></button>
        <button type="button" class="btn btn-link p-0">
            <div class="icheck-primary">
                <input type="checkbox"
                       name="select_{{$table_id}}"
                       id="{{$nId = "select_".$table_id . "_all"}}"
                       data-change="table_list::checkChildCheckboxes">
                <label for="{{$nId}}">
                </label>
            </div>
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
{{--            <button class="dropdown-item" type="button">Action</button>--}}
{{--            <button class="dropdown-item" type="button">Another action</button>--}}
            @foreach($actions as $action)
                <button class="dropdown-item"
                        data-click="table_action"
                        data-table="{{$table_id}}"
                        data-object="{{$object}}"
                        data-columns="{{json_encode($columns, JSON_UNESCAPED_UNICODE)}}"
                        @if(isset($action['confirm']) && $action['confirm']) data-confirm="@lang($action['confirm'])" @endif
                        data-jax="{{$action['jax']}}"
                        type="button"
                >
                    @if(isset($action['icon']) && $action['icon']) <i class="{{$action['icon']}}"></i> @endif
                    @if(isset($action['title']) && $action['title']) @lang($action['title']) @endif
                </button>
            @endforeach
            <button class="dropdown-item"
                    data-click="table_action"
                    data-table="{{$table_id}}"
                    data-object="{{$object}}"
                    data-columns="{{json_encode($columns, JSON_UNESCAPED_UNICODE)}}"
                    data-confirm="@lang('lte.delete_selected_rows')"
                    data-jax="lte_admin.mass_delete"
                    type="button"
            ><i class="fas fa-trash"></i> @lang('lte.delete')</button>
        </div>
    </div>
@else
    <div class="icheck-primary d-inline">
        <input type="checkbox"
               class="select_{{$table_id}}"
               name="select_{{$table_id}}[{{$id}}]"
               value="{{$id}}"
               @if($disabled) disabled="true" @endif
               id="{{$nId = $table_id . $id}}">
        <label for="{{$nId}}">
        </label>
    </div>
@endif
