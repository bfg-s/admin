<div @class(array_merge(['template_container', 'template_content'], $classes)) @attributes($attributes)>
    @foreach($contents as $content)
        {!! $content !!}
    @endforeach
</div>
