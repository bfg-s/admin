<div
    @class(array_merge(['timeline'], $classes))
    @attributes($attributes)
>
    {!! $prepend !!}
    @foreach ($paginate as $model)
        <div>
            @if($model[$order_field] instanceof Carbon && $model[$order_field]->day == 1)
                <div class="time-label"><span class="bg-green">{{ $model[$order_field]->toDateTimeString() }}</span></div>
            @endif
            @if($icon)
                <i class="{!! is_callable($icon) ? call_user_func($icon, $model) : $icon !!}"></i>
            @endif
            <div class="timeline-item">
                <span class="time">
                    <i class="fas fa-clock"></i>
                    {{ $model[$order_field] ? butty_date_time($model[$order_field]) : '' }}
                </span>
                @if($title)
                    <h3 class="timeline-header">{!! is_callable($title) ? call_user_func($title, $model) : $title !!}</h3>
                @endif
                @if($body)
                    <div @class(['timeline-body', 'p-0' => $full_body])>
                        {!! is_callable($body) ? call_user_func($body, $model) : $body !!}
                        @foreach($contents as $content)
                            {!! $content !!}
                        @endforeach
                    </div>
                @endif
                @if($footer)
                    <div class="timeline-footer">{!! is_callable($footer) ? call_user_func($footer, $model) : $footer !!}</div>
                @endif
            </div>
        </div>
    @endforeach
    @if ($paginate->lastPage() == $paginate->currentPage())
        <div><i class="fas fa-clock bg-gray"></i></div>
    @endif
    {!! $append !!}
    @if ($paginateFooter)
        {!! $paginateFooter !!}
    @endif
</div>
