<div
    @class(array_merge(['card', 'card-outline', 'w-100', "card-{$type}"], $classes))
    @attributes($attributes)
>
    <div class="card-header">
        @if($headerObj) {!! $headerObj !!} @endif

        <h3 class="card-title">
            @if($icon)
                <i class="{{ $icon }} mr-1"></i>
            @endif
            {!! preg_replace_callback('/\:([a-zA-Z0-9\_\-\.]+)/', static function ($m) use ($model) {
                return multi_dot_call($model, $m[1]);
            }, __($title)) !!}
        </h3>

        <div class="card-tools">

            @foreach ($groups as $group)
                {!! $group !!}
            @endforeach

            @if($window_controls)
                <button type="button" class="btn btn-tool" data-card-widget="maximize">
                    <i class="fas fa-expand"></i>
                </button>
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
                <button type="button" class="btn btn-tool" data-card-widget="remove">
                    <i class="fas fa-times"></i>
                </button>
            @endif
        </div>
    </div>
    @foreach($contents as $content)
        {!! $content !!}
    @endforeach
</div>
