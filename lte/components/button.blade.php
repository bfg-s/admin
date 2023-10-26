<button
    @class(array_merge(['btn btn-xs', 'btn-outline-' . $type], $classes))
    @attributes($attributes)
    type="{{ $typeAttribute }}"
>
    @if ($icon)
        <i class="{{ $icon }}"></i> {!! $title ? '&nbsp;' : ''  !!}
    @endif
    @if (($title || $contents) && $icon)
        <span class='d-none d-sm-inline'>
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
