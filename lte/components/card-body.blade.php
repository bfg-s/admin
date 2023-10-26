<div
    @class(array_merge(['card-body', 'p-0' => $foolSpace, 'table-responsive' => $tableResponsive], $classes))
    @attributes($attributes)
>
    @foreach($contents as $content)
        {!! $content !!}
    @endforeach
</div>
