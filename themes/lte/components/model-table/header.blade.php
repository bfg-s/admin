<th
    scope="col"
    @class(['fit' => $fit, 'd-none d-sm-table-cell' => $hideOnMobile])
    @attributes($attributes)
>
@if(is_string($column['sort']))
    <a href="{{ admin_url_with_get([
            $model_name => $column['sort'],
            $model_name.'_type' => $now ? ($select === 'desc' ? 'asc' : 'desc') : 'asc',
        ]) }}" data-sort="{{ $column['sort'] }}">
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
        <i class="fas fa-info-circle" title="{{ __($column['info']) }}"></i>
    @endif
</th>
