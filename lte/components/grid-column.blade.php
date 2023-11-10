<div
    @class(array_merge(['col-md' => ! $num, 'col-md-' . $num => $num], $classes))
    @attributes($attributes)
>
    @foreach($contents as $content)
        {!! $content !!}
    @endforeach
</div>
