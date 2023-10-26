<td
    @class($classes)

    @foreach ($attributes as $k => $v)
        {{ $k }}='{{ $v }}'
    @endforeach
>
    {!! $value !!}
</td>
