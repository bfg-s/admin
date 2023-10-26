<input
    type="hidden"
    name="{{ $name }}"
    value="{{ $value }}"
    @if(isset($classes) && $classes) @class($classes) @endif
    @if(isset($id) && $id) id="{{ $id }}" @endif
    @disabled($disabled ?? false)
>
