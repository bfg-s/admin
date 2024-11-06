<a
    @class([$icon => !! $icon])
    @attributes($attributes)
>
@foreach($contents as $content)
    {!! $content !!}
@endforeach
</a>
