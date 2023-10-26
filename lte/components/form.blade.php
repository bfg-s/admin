<form
    @class($classes)
    @attributes($attributes)
    action="{{ $action }}"
    method="{{ $method }}"
    enctype="multipart/form-data"
    id="{{ $id }}"
    data-load="valid"
    @if($onSubmit) onsubmit="{!! $onSubmit !!}" @endif
>
    @foreach($contents as $content)
        {!! $content !!}
    @endforeach
</form>
