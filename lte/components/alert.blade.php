<div
    @class(array_merge(['alert', 'alert-' . $type], $classes))
    @attributes($attributes)
    role="alert"
>
    @if($title)
        <h4 class="alert-heading">
            @if($icon)
                <i class="{{ $icon }}"></i>&nbsp;
            @endif
            @if($title)
                {{ $title }}
            @endif
        </h4>
        {!! $body !!}
    @endif
</div>
