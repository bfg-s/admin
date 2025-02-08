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
    x-data="{ open: {{ $show ? 'true' : 'false' }} }"
    :class="['m-2 p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800 w-full']"
    @attributes($attributes)
>
    <a
        href="javascript:void(0)"
        class="block w-full"
        @click="open = !open"
    >
        <div class="p-4 bg-gray-100 dark:bg-gray-800">
            <h4 class="font-semibold text-lg {{ $typeClass }}">
                {!! $title !!}
            </h4>
        </div>
    </a>
    <div
        x-show="open"
        x-transition
        x-collapse
        id="{{ $id }}"
        class="p-4 bg-white border-t border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300"
    >
        <div>
            @foreach($contents as $content)
                {!! $content !!}
            @endforeach
        </div>
    </div>
</div>


{{--<div class="card card-{{ $type }} card-outline" @attributes($attributes)>--}}
{{--    <a class="d-block w-100" data-toggle="collapse" href="#{{ $id }}">--}}
{{--        <div class="card-header">--}}
{{--            <h4 class="card-title w-100">--}}
{{--                {!! $title !!}--}}
{{--            </h4>--}}
{{--        </div>--}}
{{--    </a>--}}
{{--    <div id="{{ $id }}" @class(['collapse', 'show' => $show]) data-parent="#{{ $parentId }}">--}}
{{--        <div class="card-body">--}}
{{--            @foreach($contents as $content)--}}
{{--                {!! $content !!}--}}
{{--            @endforeach--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
