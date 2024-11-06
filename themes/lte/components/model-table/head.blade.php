<thead
    @foreach ($attributes as $k => $v)
        {{ $k }}='{{ $v }}'
    @endforeach
>
    <tr>
        @foreach($contents as $content)
            {!! $content !!}
        @endforeach
    </tr>
</thead>
