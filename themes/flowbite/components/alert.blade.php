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

<div class="m-2 p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
    <div class="mb-4">
        @if($title)
            <h4 class="text-lg font-semibold {{ $typeClass }}">
                @if($icon)
                    <i class="{{ $icon }} mr-2"></i>
                @endif
                {{ $title }}
            </h4>
        @endif
    </div>
    <div class="text-gray-700 dark:text-gray-300">
        {!! is_callable($body) ? call_user_func($body) : $body !!}
    </div>
</div>


{{--<div--}}
{{--    @class(['alert', 'alert-' . $type])--}}
{{--    @attributes($attributes)--}}
{{--    role="alert"--}}
{{-->--}}
{{--    @if($title)--}}
{{--        <h4 class="alert-heading">--}}
{{--            @if($icon)--}}
{{--                <i class="{{ $icon }}"></i>&nbsp;--}}
{{--            @endif--}}
{{--            @if($title)--}}
{{--                {{ $title }}--}}
{{--            @endif--}}
{{--        </h4>--}}
{{--        {!! is_callable($body) ? call_user_func($body) : $body !!}--}}
{{--    @endif--}}
{{--</div>--}}
