<div
    @class(['card-body', 'p-0' => $foolSpace, 'table-responsive' => $tableResponsive])
    @attributes($attributes)
>
    @foreach($contents as $content)
        {!! $content !!}
    @endforeach
</div>
