<div
    @class(array_merge(['btn-group btn-group-sm ml-1'], $classes))
    @attributes($attributes)
>
    @foreach($contents as $content)
        {!! $content !!}
    @endforeach
</div>
