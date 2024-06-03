<div @class($classes) @attributes($attributes)>
    @foreach($contents as $content)
        {!! $content !!}
    @endforeach
</div>
