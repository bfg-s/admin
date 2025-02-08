<table
    @attributes($attributes)
    id="{{ $id }}"
    class="min-w-full table-auto border-collapse"
>
    @foreach($contents as $content)
        {!! $content !!}
    @endforeach
</table>

{{--<table--}}
{{--    @class(['table', 'table-sm', 'table-hover'])--}}
{{--    @attributes($attributes)--}}
{{--    id="{{ $id }}"--}}
{{-->--}}
{{--    @foreach($contents as $content)--}}
{{--        {!! $content !!}--}}
{{--    @endforeach--}}
{{--</table>--}}
