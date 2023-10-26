<th
    scope="col"
    @class(array_merge(['fit' => $fit], $classes))
    @foreach ($attributes as $k => $v)
        {{ $k }}='{{ $v }}'
    @endforeach
>
    @if(is_string($column['sort']))
        <a href="{{ urlWithGet([
            $model_name => $column['sort'],
            $model_name.'_type' => $now ? ($select === 'desc' ? 'asc' : 'desc') : 'asc',
        ]) }}">
            <i class="fas fa-sort-amount-{{ $type }} d-none d-sm-inline"></i>
            <span @class(['text-bold' => $now, 'text-body'])>{{ $column['label'] }}</span>
        </a>
    @else
        @if($label)
            <span class="fit">{!! $label !!}</span>
        @endif
        @foreach($contents as $content)
            <span class="fit">{!! $content !!}</span>
        @endforeach
    @endif
    @if($column['info'])
        &nbsp; <i class="fas fa-info-circle" title="{{ __($column['info']) }}"></i>
    @endif
</th>
