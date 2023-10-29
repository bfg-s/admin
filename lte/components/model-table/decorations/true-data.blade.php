<span
    @class([
        'badge',
        'badge-dark' => is_null($value) || $value === '',
        'badge-success' => $value === true,
        'badge-danger' => $value === false,
        'badge-info' => is_array($value),
    ])
>
    @if(is_null($value) || $value === '')
        NULL
    @elseif($value === true)
        TRUE
    @elseif($value === false)
        FALSE
    @elseif(is_array($value))
        Array({{ count($value) }})
    @elseif($value instanceof \Carbon\Carbon)
        {{ $value->format('Y-m-d H:i:s') }}
    @else
        {!! $value !!}
    @endif
</span>
