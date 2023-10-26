<div @class(array_merge(['template_container'], $classes)) @attributes($attributes)>
    @foreach($contents as $content)
        {!! $content !!}
    @endforeach
</div>
