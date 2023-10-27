<div
    @class(array_merge(['template_container', 'card', 'card-outline'], $classes))
    @attributes($attributes)
    @if($ordered) data-oder-by="{{ $ordered }}" @endif
>
    @if($buttons)
        <div class="card-header">
            @if($ordered)
                <div class="btn-group btn-group-sm ml-1 control_relation">
                    <a type="button" class="btn btn-default handle" style="cursor: move;">
                        <i class="fas fa-arrows-alt"></i>
                    </a>
                </div>
            @endif
            <h3 class="card-title return_relation" style="display: none">
                Deleted
            </h3>

            <div class="card-tools">
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
