<input
    type="{{ $type }}"
    id="{{ $id }}"
    name="{{ $name }}"
    placeholder="{{ $placeholder }}"
    @class(['is-invalid' => $has_bug, 'form-control' => $form_control])
    value="{{ $value }}"
    @attributes($attributes)
    @checked($checked)
    @if($autocomplete)
        autocomplete="{{ $autocomplete }}"
    @endif
    @foreach($rules as $key => $rule)
        @if(is_numeric($key))
            data-rule-{{ $rule }}
        @else
            data-rule-{{ $key }}='{{ $rule }}'
        @endif
    @endforeach
    @foreach($datas as $key => $data)
        @if(is_numeric($key))
            data-{{ $data }}
        @else
{{--            @if (is_array($data))--}}
{{--            @dd($data)--}}
{{--            @endif--}}
            data-{{ $key }}='{{ is_array($data) ? json_encode($data) : $data }}'
        @endif
    @endforeach
/>
