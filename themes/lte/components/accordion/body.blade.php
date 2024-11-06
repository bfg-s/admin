<div class="card card-{{ $type }} card-outline" @attributes($attributes)>
    <a class="d-block w-100" data-toggle="collapse" href="#{{ $id }}">
        <div class="card-header">
            <h4 class="card-title w-100">
                {!! $title !!}
            </h4>
        </div>
    </a>
    <div id="{{ $id }}" @class(['collapse', 'show' => $show]) data-parent="#{{ $parentId }}">
        <div class="card-body">
            @foreach($contents as $content)
                {!! $content !!}
            @endforeach
        </div>
    </div>
</div>
