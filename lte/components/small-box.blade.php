<div
    @class(array_merge(['info-box'], $classes))
    @attributes($attributes)
>
    <span @class(['info-box-icon elevation-1', 'bg-' . $type])>
        <i class="{{ $icon }}"></i>
    </span>
    <div class="info-box-content">
        <span class="info-box-text">{!! $title !!}</span>
        <span class="info-box-number">
            {!! $body[0] ?? '' !!}
            <small>{!! $body[1] ?? '' !!}</small>
        </span>
    </div>
</div>
