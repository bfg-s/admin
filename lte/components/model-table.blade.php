<table
    @class(['table', 'table-sm', 'table-hover'])
    @attributes($attributes)
    id="{{ $id }}"
>
    @foreach($contents as $content)
        {!! $content !!}
    @endforeach
</table>
