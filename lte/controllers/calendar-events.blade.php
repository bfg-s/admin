<div class="card-body">

    <div id="external-events" data-load="calendar::events">
        @foreach(admin()->eventsTemplates()->orderByDesc('id')->get() as $eventTemplate)
            <div class="external-event {{ $eventTemplate->color }}">
                {{ $eventTemplate->name }}
                <div class="float-right">
                    <a href="javascript:void(0)" data-click="calendar::events_template_delete" data-params="{{ $eventTemplate->id }}" class="btn btn-link btn-sm p-0"><i class="fas fa-trash"></i></a>
                </div>
            </div>
        @endforeach
        <div class="checkbox">
            <label for="drop-remove">
                <input type="checkbox" id="drop-remove">
                remove after drop
            </label>
        </div>
    </div>
</div>
