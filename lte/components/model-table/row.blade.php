<tr
    @class($classes)
    @foreach ($attributes as $k => $v)
        {{ $k }}='{{ $v }}'
    @endforeach
>
    @foreach($contents as $content)
        {!! $content !!}
    @endforeach
</tr>
