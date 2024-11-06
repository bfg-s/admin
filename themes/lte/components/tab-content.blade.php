<div @class(['tab-pane', 'active show' => $active, 'p-' . $padding => $padding, 'pr-' . $paddingRight => $paddingRight]) @attributes($attributes) id="{{ $id }}" aria-labelledby="{{ $id }}-label" role="tabpanel">
    @foreach($contents as $content)
        {!! $content !!}
    @endforeach
</div>
