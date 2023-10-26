<div @class(array_merge(['tab-pane'], $classes)) @attributes($attributes) role="tabpanel">
    @foreach($contents as $content)
        {!! $content !!}
    @endforeach
</div>
