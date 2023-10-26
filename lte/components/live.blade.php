<div
    id="{{ $id }}"
    class="__live__"
    @class($classes)
    @attributes($attributes)
>
    @foreach($contents as $content)
        {!! $content !!}
    @endforeach
</div>
