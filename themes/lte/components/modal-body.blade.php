<div
    @class(['modal-body', 'p-0' => $withOutPadding])
    @attributes($attributes)
>
    @foreach($contents as $content)
        {!! $content !!}
    @endforeach
</div>
