<button
    type="{{ $typeAttribute }}"
    @class([
        'inline-flex items-center px-2 py-1 text-sm font-medium text-center rounded-lg focus:ring-4 focus:outline-none',
        'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-300' => $type === 'primary',
        'bg-gray-600 text-white hover:bg-gray-700 focus:ring-gray-300' => $type === 'secondary',
        'bg-transparent border border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white focus:ring-blue-300' => $type === 'outline-primary',
        'bg-transparent border border-gray-600 text-gray-600 hover:bg-gray-600 hover:text-white focus:ring-gray-300' => $type === 'outline-secondary',
        'bg-green-600 text-white hover:bg-green-700 focus:ring-green-300' => $type === 'success',
        'bg-red-600 text-white hover:bg-red-700 focus:ring-red-300' => $type === 'danger',
        'bg-yellow-600 text-white hover:bg-yellow-700 focus:ring-yellow-300' => $type === 'warning',
        'bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-indigo-300' => $type === 'info',
        'bg-gray-200 text-gray-700 hover:bg-gray-300 focus:ring-gray-300' => $type === 'light',
        'bg-black text-white hover:bg-gray-800 focus:ring-gray-500' => $type === 'dark',
        'd-none' => $displayNone
    ])
    @attributes($attributes)
>
    @if ($icon)
        <div><i class="{{ $icon }} w-4 h-4"></i></div> {!! $title ? '&nbsp;' : '' !!}
    @endif
    @if (($title || $contents) && $icon)
        <span class="d-none sm:inline">
            {{ $title }}
            @foreach($contents as $content)
                {!! $content !!}
            @endforeach
        </span>
    @elseif ($title)
        {{ $title }}
        @foreach($contents as $content)
            {!! $content !!}
        @endforeach
    @else
        @foreach($contents as $content)
            {!! $content !!}
        @endforeach
    @endif
</button>

{{--<button--}}
{{--    @class(['btn btn-xs', 'btn-outline-' . $type, 'd-none' => $displayNone])--}}
{{--    @attributes($attributes)--}}
{{--    type="{{ $typeAttribute }}"--}}
{{-->--}}
{{--    @if ($icon)--}}
{{--        <i class="{{ $icon }}"></i> {!! $title ? '&nbsp;' : ''  !!}--}}
{{--    @endif--}}
{{--    @if (($title || $contents) && $icon)--}}
{{--        <span class='d-none d-sm-inline'>--}}
{{--            {{ $title }}--}}
{{--            @foreach($contents as $content)--}}
{{--                {!! $content !!}--}}
{{--            @endforeach--}}
{{--        </span>--}}
{{--    @elseif ($title)--}}
{{--        {{ $title }}--}}
{{--        @foreach($contents as $content)--}}
{{--            {!! $content !!}--}}
{{--        @endforeach--}}
{{--    @else--}}
{{--        @foreach($contents as $content)--}}
{{--            {!! $content !!}--}}
{{--        @endforeach--}}
{{--    @endif--}}
{{--</button>--}}
