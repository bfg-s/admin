<div @attributes($attributes)>
    <div class="absolute inset-0 bg-gray-700 bg-opacity-50 flex items-center justify-center overlay">
        <i
            @class([
                'fas fa-sync-alt text-white text-2xl',
                'animate-spin' => true
            ])
        ></i>
    </div>
    @foreach($contents as $content)
        {!! $content !!}
    @endforeach
</div>

{{--<div @attributes($attributes)>--}}
{{--    <div class="overlay">--}}
{{--        <i @class(['fas fa-2x fa-sync-alt', 'fa-spin' => true])></i>--}}
{{--    </div>--}}
{{--    @foreach($contents as $content)--}}
{{--        {!! $content !!}--}}
{{--    @endforeach--}}
{{--</div>--}}
