<div
    @class(['col-md' => ! $num, 'col-md-' . $num => $num, 'd-flex' => $displayFlex])
    @attributes($attributes)
>
    @foreach($contents as $content)
        {!! $content !!}
    @endforeach
</div>
