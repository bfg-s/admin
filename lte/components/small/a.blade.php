<a
    @class(['editable editable-click' => $editable])
    @attributes($attributes)
>
@foreach($contents as $content)
    {!! $content !!}
@endforeach
</a>
