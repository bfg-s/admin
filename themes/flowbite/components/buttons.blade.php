
<div
    @class(['flex space-x-2 float-right', 'ml-1' => !! $contents, 'control_relation' => $controlRelation, 'return_relation' => $returnRelation])
    @attributes($attributes)
>
    @foreach($contents as $content)
        {!! $content !!}
    @endforeach
</div>

{{--<div--}}
{{--    @class(['btn-group btn-group-sm ml-1', 'control_relation' => $controlRelation, 'return_relation' => $returnRelation])--}}
{{--    @attributes($attributes)--}}
{{-->--}}
{{--    @foreach($contents as $content)--}}
{{--        {!! $content !!}--}}
{{--    @endforeach--}}
{{--</div>--}}
