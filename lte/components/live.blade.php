<div
    id="{{ $id }}"
    @class(array_merge(['__live__'], $classes))
    @attributes($attributes)
>
    @foreach($contents as $content)
        {!! $content !!}
    @endforeach
</div>
