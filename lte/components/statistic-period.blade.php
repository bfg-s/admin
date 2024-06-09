<div
    @class(['row'])
    @attributes($attributes)
    style="flex: 1;"
>
    @foreach($contents as $content)
        {!! $content !!}
    @endforeach
</div>
