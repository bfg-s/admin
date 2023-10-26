<div
    @class(array_merge(['modal-body'], $classes))
    @attributes($attributes)
>
    @foreach($contents as $content)
        {!! $content !!}
    @endforeach
</div>
