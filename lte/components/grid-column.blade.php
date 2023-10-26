<div
    @class(array_merge(['pl-0', 'col-md' => ! $num, 'col-md-' . $num => $num], $classes))
    @attributes($attributes)
>
    @foreach($contents as $content)
        {!! $content !!}
    @endforeach
</div>
