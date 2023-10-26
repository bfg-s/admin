<textarea
    id="{{ $id }}"
    name="{{ $name }}"
    placeholder="{{ $placeholder }}"
    @class(['is-invalid' => $has_bug, 'form-control' => $form_control])
    @attributes($attributes)
    @if(isset($rows))
        rows="{{ $rows }}"
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
            data-{{ $key }}='{{ $data }}'
        @endif
    @endforeach
>{!! $value !!}</textarea>
