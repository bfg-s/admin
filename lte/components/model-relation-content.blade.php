<div @class(['template_content']) @attributes($attributes)>
    @foreach($contents as $content)
        {!! $content !!}
    @endforeach
</div>
