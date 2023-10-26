<select
    name="{{ $name }}"
    id="{{ $id }}"
    @class(array_merge(['form-control', 'is-invalid' => $hasBug], $classes))
    @attributes($attributes)
    @if($multiple) multiple="multiple" @endif
    @foreach(($datas ?? []) as $key => $data)
        @if(is_numeric($key))
            data-{{ $data }}
        @else
            data-{{ $key }}='{{ $data }}'
        @endif
    @endforeach
>
    @foreach($options as $key => $option)
        <option value="{{ $key }}" @selected(is_array($option))>{{ is_array($option) ? $option[0] : $option }}</option>
    @endforeach
</select>
