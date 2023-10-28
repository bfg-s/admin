<div @class(array_merge(['template_content'], $classes)) @attributes($attributes)>
    @foreach($contents as $content)
        {!! $content !!}
    @endforeach
</div>
