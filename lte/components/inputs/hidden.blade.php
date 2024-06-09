<input
    type="hidden"
    name="{{ $name }}"
    value="{{ $value }}"
    @if(isset($id) && $id) id="{{ $id }}" @endif
    @disabled($disabled ?? false)
>
