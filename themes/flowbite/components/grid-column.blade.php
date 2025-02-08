<div
    data-num="{{ $num }}"
    @class([
        'flex' => $displayFlex,
        'flex-1' => ! $num,
        'w-1/12' => $num == 1,
        'w-2/12' => $num == 2,
        'w-3/12' => $num == 3,
        'w-4/12' => $num == 4,
        'w-5/12' => $num == 5,
        'w-6/12' => $num == 6,
        'w-7/12' => $num == 7,
        'w-8/12' => $num == 8,
        'w-9/12' => $num == 9,
        'w-10/12' => $num == 10,
        'w-11/12' => $num == 11,
        'w-full' => $num == 12,
    ])
    @attributes($attributes)
>
    @foreach($contents as $content)
        {!! $content !!}
    @endforeach
</div>

{{--<div--}}
{{--    @class([--}}
{{--        'flex-col',--}}
{{--        'flex' => true,--}}
{{--        'basis-auto grow shrink' => ! $num,--}}
{{--        'basis-1/12' => $num == 1,--}}
{{--        'basis-2/12' => $num == 2,--}}
{{--        'basis-3/12' => $num == 3,--}}
{{--        'basis-4/12' => $num == 4,--}}
{{--        'basis-5/12' => $num == 5,--}}
{{--        'basis-6/12' => $num == 6,--}}
{{--        'basis-7/12' => $num == 7,--}}
{{--        'basis-8/12' => $num == 8,--}}
{{--        'basis-9/12' => $num == 9,--}}
{{--        'basis-10/12' => $num == 10,--}}
{{--        'basis-11/12' => $num == 11,--}}
{{--        'basis-full' => $num == 12,--}}
{{--    ])--}}
{{--    @attributes($attributes)--}}
{{-->--}}
{{--    @foreach($contents as $content)--}}
{{--        {!! $content !!}--}}
{{--    @endforeach--}}
{{--</div>--}}

{{--<div--}}
{{--    @class(['col-md' => ! $num, 'col-md-' . $num => $num, 'd-flex' => $displayFlex])--}}
{{--    @attributes($attributes)--}}
{{-->--}}
{{--    @foreach($contents as $content)--}}
{{--        {!! $content !!}--}}
{{--    @endforeach--}}
{{--</div>--}}
