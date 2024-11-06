<div
    id="{{ $id }}"
    @class(['__live__'])
    @attributes($attributes)
>
    @foreach($contents as $content)
        {!! $content !!}
    @endforeach
</div>
