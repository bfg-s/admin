<div
    @class(array_merge(['card'], $classes))
    @attributes($attributes)
>
    @if($title)
        <div class="card-header">

            <h3 class="card-title">
                {{ $title }}
            </h3>

            <div class="card-tools">

                <button type="button" class="btn btn-tool" data-card-widget="maximize">
                    <i class="fas fa-expand"></i>
                </button>
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
                <button type="button" class="btn btn-tool" data-card-widget="remove">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif
    <div class="card-body" @if($ordered) data-load="admin::model_relation_ordered" data-params="{{ $ordered }}" @endif @if($tpl) data-tpl="{{ $tpl }}" @endif>
        @foreach($contents as $content)
            {!! $content !!}
        @endforeach
    </div>
    @if ($buttons)
        <div class="card-footer">
            <div class="row">
                <div class="col text-right">
                    {!! $buttons !!}
                </div>
            </div>
        </div>
    @endif
</div>
