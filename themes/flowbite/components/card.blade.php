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

<div
    @class(['m-2 p-2 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800 w-full'])
    @attributes($attributes)
>
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-2">
            @if($icon)
                <i class="{{ $icon }} text-primary-600 dark:text-primary-400"></i>
            @endif
            <h3 class="text-lg font-semibold {{ $typeClass }}">
                {!! preg_replace_callback('/\:([a-zA-Z0-9\_\-\.]+)/', static function ($m) use ($model) {
                    return multi_dot_call($model, $m[1]);
                }, __($title)) !!}
            </h3>
        </div>
        <div class="flex items-center">
            @foreach ($groups as $group)
                {!! $group !!}
            @endforeach
{{--            @if($window_controls)--}}
{{--                <button type="button" class="p-2 text-gray-500 bg-gray-100 rounded-lg hover:bg-gray-200 dark:text-gray-400 dark:bg-gray-700 dark:hover:bg-gray-600" data-card-widget="maximize">--}}
{{--                    <i class="fas fa-expand"></i>--}}
{{--                </button>--}}
{{--                <button type="button" class="p-2 text-gray-500 bg-gray-100 rounded-lg hover:bg-gray-200 dark:text-gray-400 dark:bg-gray-700 dark:hover:bg-gray-600" data-card-widget="collapse">--}}
{{--                    <i class="fas fa-minus"></i>--}}
{{--                </button>--}}
{{--                <button type="button" class="p-2 text-gray-500 bg-gray-100 rounded-lg hover:bg-gray-200 dark:text-gray-400 dark:bg-gray-700 dark:hover:bg-gray-600" data-card-widget="remove">--}}
{{--                    <i class="fas fa-times"></i>--}}
{{--                </button>--}}
{{--            @endif--}}
        </div>
    </div>

    <!-- Content -->
    <div class="mb-4 space-y-4">
        @foreach($contents as $content)
            <div class="text-base font-normal text-gray-700 dark:text-gray-400">
                {!! $content !!}
            </div>
        @endforeach
    </div>

    <!-- Footer -->
    @if($footerResult = $footer())
        <div class="pt-4 text-sm text-gray-600 dark:text-gray-400">
            {!! $footerResult !!}
        </div>
    @endif
</div>




{{--<div--}}
{{--    @class(['card', 'card-outline', 'w-100', "card-{$type}"])--}}
{{--    @attributes($attributes)--}}
{{-->--}}
{{--    <div class="card-header">--}}
{{--        @if($headerObj) {!! $headerObj !!} @endif--}}

{{--        <h3 class="card-title">--}}
{{--            @if($icon)--}}
{{--                <i class="{{ $icon }} mr-1"></i>--}}
{{--            @endif--}}
{{--            {!! preg_replace_callback('/\:([a-zA-Z0-9\_\-\.]+)/', static function ($m) use ($model) {--}}
{{--                return multi_dot_call($model, $m[1]);--}}
{{--            }, __($title)) !!}--}}
{{--        </h3>--}}

{{--        <div class="card-tools">--}}

{{--            @foreach ($groups as $group)--}}
{{--                {!! $group !!}--}}
{{--            @endforeach--}}

{{--            @if($window_controls)--}}
{{--                <button type="button" class="btn btn-tool" data-card-widget="maximize">--}}
{{--                    <i class="fas fa-expand"></i>--}}
{{--                </button>--}}
{{--                <button type="button" class="btn btn-tool" data-card-widget="collapse">--}}
{{--                    <i class="fas fa-minus"></i>--}}
{{--                </button>--}}
{{--                <button type="button" class="btn btn-tool" data-card-widget="remove">--}}
{{--                    <i class="fas fa-times"></i>--}}
{{--                </button>--}}
{{--            @endif--}}
{{--        </div>--}}
{{--    </div>--}}
{{--    @foreach($contents as $content)--}}
{{--        {!! $content !!}--}}
{{--    @endforeach--}}

{{--    @if($footerResult = $footer())--}}
{{--        {!! $footerResult !!}--}}
{{--    @endif--}}
{{--</div>--}}
