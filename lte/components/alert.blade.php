<div
    @class(['alert', 'alert-' . $type])
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
        {!! is_callable($body) ? call_user_func($body) : $body !!}
    @endif
</div>
