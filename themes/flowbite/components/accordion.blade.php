<div class="flex flex-wrap" @attributes($attributes)>
    <div class="w-full" id="{{ $id }}">
        @foreach($contents as $content)
            {!! $content !!}
        @endforeach
    </div>
</div>

{{--<div class="row" @attributes($attributes)>--}}
{{--    <div class="col-12" id="{{ $id }}">--}}
{{--        @foreach($contents as $content)--}}
{{--            {!! $content !!}--}}
{{--        @endforeach--}}
{{--    </div>--}}
{{--</div>--}}
