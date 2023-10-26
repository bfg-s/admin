<div @class(array_merge(['small-box', 'bg-' . $type], $classes)) @attributes($attributes)>
    <div class="inner">
        <h3>
            {!! $body[0] ?? '' !!}
            <sup style="font-size: 20px">
                {!! $body[1] ?? '' !!}
            </sup>
        </h3>
        @if($title)
            <p>{!! $title !!}</p>
        @endif
    </div>
    @if($icon)
        <div class="icon"><i class="{{ $icon }}"></i></div>
    @endif
    @if($link)
        <a
            @if(isset($link[0])) href="{{ $link[0] }}" @endif
            class="small-box-footer"
        >
            {!! $link[1] ?? __('admin.more_info') !!}
            &nbsp;
            <i class="{{ $link[2] ?? 'fas fa-arrow-circle-right' }}"></i>
        </a>
    @endif
</div>
