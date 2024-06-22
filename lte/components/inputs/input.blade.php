<input
    type="{{ $type }}"
    id="{{ $id }}"
    name="{{ $name }}"
    placeholder="{{ $placeholder }}"
    @class(['is-invalid' => $has_bug, 'form-control' => $form_control])
    value="{{ is_array($value) ? json_encode($value) : $value }}"
    @attributes($attributes)
    @checked($checked)
    @if($autocomplete)
        autocomplete="{{ $autocomplete }}"
    @endif
    @foreach($rules as $key => $rule)
        @if(is_numeric($key))
            data-rule-{{ $rule }}
        @else
            @php try { @endphp
            data-rule-{{ $key }}='{{ $rule }}'
            @php } catch(Throwable) { dd($key, $rule); } @endphp
        @endif
    @endforeach
    @foreach($datas as $key => $data)
        @if(is_numeric($key))
            data-{{ $data }}
        @else
            data-{{ $key }}='{{ is_array($data) ? json_encode($data) : $data }}'
        @endif
    @endforeach
/>
