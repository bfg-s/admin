<div
    @class(['flex flex-wrap w-full'])
    @attributes($attributes)
>
    @foreach($contents as $content)
        {!! $content !!}
    @endforeach
</div>

{{--<div--}}
{{--    @class(['row'])--}}
{{--    @attributes($attributes)--}}
{{-->--}}
{{--    @foreach($contents as $content)--}}
{{--        {!! $content !!}--}}
{{--    @endforeach--}}
{{--</div>--}}
