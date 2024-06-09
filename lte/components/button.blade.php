<button
    @class(['btn btn-xs', 'btn-outline-' . $type, 'd-none' => $displayNone])
    @attributes($attributes)
    type="{{ $typeAttribute }}"
>
    @if ($icon)
        <i class="{{ $icon }}"></i> {!! $title ? '&nbsp;' : ''  !!}
    @endif
    @if (($title || $contents) && $icon)
        <span class='d-none d-sm-inline'>
            {{ __($title) }}
            @foreach($contents as $content)
                {!! $content !!}
            @endforeach
        </span>
    @elseif ($title)
        {{ __($title) }}
        @foreach($contents as $content)
            {!! $content !!}
        @endforeach
    @else
        @foreach($contents as $content)
            {!! $content !!}
        @endforeach
    @endif
</button>
