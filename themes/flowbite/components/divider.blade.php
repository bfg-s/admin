<div
    class="flex items-center space-x-4 m-2"
    @attributes($attributes)
>
    @if($left_title)
        <div class="flex-shrink-0">
            <h4 class="text-gray-500">{!! $left_title !!}</h4>
        </div>
    @endif
    @if($center_title)
        <div class="flex-1">
            <hr>
        </div>
        <div class="flex-shrink-0">
            <h4 class="text-gray-500">{!! $center_title !!}</h4>
        </div>
        <div class="flex-1">
            <hr @class(['mt-0' => !$anyTitle])>
        </div>
    @else
        <div class="flex-1">
            <hr @class(['mt-0' => !$anyTitle])>
        </div>
    @endif
    @if($right_title)
        <div class="flex-shrink-0">
            <h4 class="text-gray-500">{!! $right_title !!}</h4>
        </div>
    @endif
</div>


{{--<div--}}
{{--    @class(['row'])--}}
{{--    @attributes($attributes)--}}
{{-->--}}
{{--    @if($left_title)--}}
{{--        <div class="col-auto">--}}
{{--            <h4 class="text-secondary">{!! $left_title !!}</h4>--}}
{{--        </div>--}}
{{--    @endif--}}
{{--    @if($center_title)--}}
{{--        <div class="col">--}}
{{--            <hr>--}}
{{--        </div>--}}
{{--        <div class="col-auto">--}}
{{--            <h4 class="text-secondary">{!! $center_title !!}</h4>--}}
{{--        </div>--}}
{{--        <div class="col">--}}
{{--            <hr @class(['mt-0' => ! $anyTitle])>--}}
{{--        </div>--}}
{{--    @else--}}
{{--        <div class="col">--}}
{{--            <hr @class(['mt-0' => ! $anyTitle])>--}}
{{--        </div>--}}
{{--    @endif--}}
{{--    @if($right_title)--}}
{{--        <div class="col-auto">--}}
{{--            <h4 class="text-secondary">{!! $right_title !!}</h4>--}}
{{--        </div>--}}
{{--    @endif--}}
{{--</div>--}}
