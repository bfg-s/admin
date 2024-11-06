<input
    type="text"
    name="{{ $name }}"
    value="{{ $value }}"
    @if(isset($id) && $id) id="{{ $id }}" @endif
    @class(['form-control'])
    @disabled(true)
    @foreach($datas as $key => $data)
        @if(is_numeric($key))
            data-{{ $data }}
        @else
            data-{{ $key }}='{{ $data }}'
        @endif
    @endforeach
>
