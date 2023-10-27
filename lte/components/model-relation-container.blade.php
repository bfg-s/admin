<div @class(array_merge(['template_container', 'card', 'card-outline'], $classes)) @attributes($attributes)>
    @if($buttons)
        <div class="card-header">

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
