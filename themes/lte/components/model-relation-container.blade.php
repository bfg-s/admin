<div
    @class(['template_container', 'card', 'card-outline'])
    @attributes($attributes)
    @if($ordered) data-oder-by="{{ $ordered }}" @endif
>
    @if($buttons)
        <div class="card-header">
            @if($model_id)
                <span>
                    <string>ID:</string> {{ $model_id }}
                    @if($created_at) 🔹 <string>Created at:</string> {{ beautiful_date_time($created_at) }} @endif
                    @if($updated_at) 🔹 <string>Updated at:</string> {{ beautiful_date_time($updated_at) }} @endif
                </span>
            @endif
            <h3 class="card-title return_relation" style="display: none; margin-right: 5px">
                Deleting
            </h3>
            <div class="card-tools">
                @if($ordered)
                    <div class="btn-group btn-group-sm ml-1 control_relation">
                        <a type="button" class="btn btn-default handle" style="cursor: move;">
                            <i class="fas fa-arrows-alt"></i>
                        </a>
                    </div>
                @endif
                @foreach($buttons as $button)
                    {!! $button !!}
                @endforeach
            </div>
        </div>
    @endif
    <div class="card-body template_content">
        @foreach($contents as $content)
            {!! $content !!}
        @endforeach
    </div>
</div>
