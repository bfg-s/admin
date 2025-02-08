@php
    $typeClasses = [
        'success' => 'text-green-900 dark:text-green-300',
        'warning' => 'text-yellow-900 dark:text-yellow-300',
        'info' => 'text-blue-900 dark:text-blue-300',
        'danger' => 'text-red-900 dark:text-red-300',
        'primary' => 'text-blue-900 dark:text-blue-300',
        'secondary' => 'text-gray-900 dark:text-gray-300',
        'default' => 'text-gray-900 dark:text-gray-300',
    ];
    $typeClass = $typeClasses[$type] ?? $typeClasses['default'];
@endphp

<div class="m-2 p-4 bg-white border border-gray-200 rounded-lg shadow-sm sm:flex dark:border-gray-700 sm:p-6 dark:bg-gray-800">
    <div class="flex-1">
        <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">
            {!! $title !!}
        </h3>
        <div class="flex items-center space-x-2">
            <span class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white">
                {!! $body[0] ?? '0' !!}
                @if(isset($body[1]))
                    <sup class="text-base font-normal text-gray-500 dark:text-gray-400">
                        {!! $body[1] !!}
                    </sup>
                @endif
            </span>
        </div>
    </div>

    @if($link)
        <div class="sm:ml-4 sm:mt-0 mt-4 flex items-center">
            <a
                @if(isset($link[0])) href="{{ $link[0] }}" @endif
            class="text-blue-500 hover:underline flex items-center space-x-1"
            >
                <span>{!! $link[1] ?? __('admin.more_info') !!}</span>
                <i class="{{ $link[2] ?? 'fas fa-arrow-circle-right' }}"></i>
            </a>
        </div>
    @endif

    <!-- Иконка справа, растягивающаяся по высоте -->
    <div class="flex items-stretch ml-4">
        <span class="flex items-center h-full text-6xl {{ $typeClass }}">
            <i class="{{ $icon }}"></i>
        </span>
    </div>
</div>



{{--<div @class(['small-box', 'bg-' . $type]) @attributes($attributes)>--}}
{{--    <div class="inner">--}}
{{--        <h3>--}}
{{--            {!! $body[0] ?? '' !!}--}}
{{--            <sup style="font-size: 20px">--}}
{{--                {!! $body[1] ?? '' !!}--}}
{{--            </sup>--}}
{{--        </h3>--}}
{{--        @if($title)--}}
{{--            <p>{!! $title !!}</p>--}}
{{--        @endif--}}
{{--    </div>--}}
{{--    @if($icon)--}}
{{--        <div class="icon"><i class="{{ $icon }}"></i></div>--}}
{{--    @endif--}}
{{--    @if($link)--}}
{{--        <a--}}
{{--            @if(isset($link[0])) href="{{ $link[0] }}" @endif--}}
{{--            class="small-box-footer"--}}
{{--        >--}}
{{--            {!! $link[1] ?? __('admin.more_info') !!}--}}
{{--            &nbsp;--}}
{{--            <i class="{{ $link[2] ?? 'fas fa-arrow-circle-right' }}"></i>--}}
{{--        </a>--}}
{{--    @endif--}}
{{--</div>--}}
