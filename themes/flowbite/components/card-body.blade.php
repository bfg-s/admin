<div
    @class(['p-2' => !$foolSpace, 'p-0' => $foolSpace, 'overflow-x-auto' => $tableResponsive])
    @attributes($attributes)
>
    @foreach($contents as $content)
        {!! $content !!}
    @endforeach
</div>

{{--<div--}}
{{--    @class(['card-body', 'p-0' => $foolSpace, 'table-responsive' => $tableResponsive])--}}
{{--    @attributes($attributes)--}}
{{-->--}}
{{--    @foreach($contents as $content)--}}
{{--        {!! $content !!}--}}
{{--    @endforeach--}}
{{--</div>--}}
