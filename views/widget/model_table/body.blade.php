<div class="tab-content">
    @foreach($tabs as $tab)
        <div class="{{ (request()->has($p_id) ? request()->get($p_id) == $tab['mode']->name : $loop->first) ? 'active' : ''}} tab-pane" id="{{$tab['name']}}">
            <div class="card-body {{$tab['mode']->body_class}}">
                {!! $tab['mode']->render(); !!}
            </div>
            {!! $tab['mode']->footer()->render(); !!}
        </div>
    @endforeach
</div>
