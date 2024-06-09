<div @class(['tab-pane', 'active show' => $active]) @attributes($attributes) id="{{ $id }}" aria-labelledby="{{ $id }}-label" role="tabpanel">
    @foreach($contents as $content)
        {!! $content !!}
    @endforeach
</div>
