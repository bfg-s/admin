<div>
    <div class="transition-all duration-300 overflow-hidden table_search_form hidden">
        <div class="p-4">
            {!! $content !!}
        </div>
    </div>

    @if ($hasQ && $searchInfo)
        <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="p-0 table_search_form">
            {!! $searchInfo !!}
        </div>
    @endif
</div>


{{--<div class="table_search_form collapse">--}}
{{--    <div class="card-body">--}}
{{--        {!! $content !!}--}}
{{--    </div>--}}
{{--</div>--}}
{{--@if ($hasQ && $searchInfo)--}}
{{--    <div class="table_search_form collapse show">--}}
{{--        <div class="card-body p-0">--}}
{{--            {!! $searchInfo !!}--}}
{{--        </div>--}}
{{--    </div>--}}
{{--@endif--}}
