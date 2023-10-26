<{{$element}}
    @class($classes)
    @attributes($attributes)
>
    @foreach($contents as $content)
        {!! $content !!}
    @endforeach
</{{$element}}>
