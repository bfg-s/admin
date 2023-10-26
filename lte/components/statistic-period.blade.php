<div
    @class(array_merge(['row'], $classes))
    @attributes($attributes)
>
    @foreach($contents as $content)
        {!! $content !!}
    @endforeach
</div>
