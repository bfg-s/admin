<table
    @class(array_merge(['table', 'table-sm', 'table-hover'], $classes))
    @attributes($attributes)
    id="{{ $id }}"
>
    @foreach($contents as $content)
        {!! $content !!}
    @endforeach
</table>
