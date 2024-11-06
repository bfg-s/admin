<div
    id="{{ $id }}"
    data-name="{{ $dataName }}"
    data-placeholder="{{ $placeholder }}"
    style="z-index: 1051"
    @class(['m-0'])
    @foreach($datas as $key => $data)
        @if(is_numeric($key))
            data-{{ $data }}
        @else
            data-{{ $key }}='{{ $data }}'
        @endif
    @endforeach
    @foreach($rules as $key => $rule)
        @if(is_numeric($key))
            data-rule-{{ $rule }}
        @else
            data-rule-{{ $key }}='{{ $rule }}'
        @endif
    @endforeach
>{{ $value }}</div>
