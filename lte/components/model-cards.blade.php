<div
    @attributes($attributes)
    id="{{ $id }}"
>
    <div class="btn-group mb-3">
        @foreach($rows as $row)
            @php
                $now = request()->get($model_name, $order_field) == $row['sort'];
                $type = $now ? ($select === 'desc' ? 'down' : 'up-alt') : 'down';
            @endphp
            @if($row['sort'])
                <a href="{{ admin_url_with_get([
                    $model_name => $row['sort'],
                    $model_name.'_type' => $now ? ($select === 'desc' ? 'asc' : 'desc') : 'asc',
                ]) }}" data-sort="{{ $row['sort'] }}" @class(['btn', 'btn-secondary' => ! $now, 'btn-dark' => $now])>
                    <i class="fas fa-sort-amount-{{ $type }} d-none d-sm-inline"></i> {{ $row['label'] }}
                </a>
            @endif
        @endforeach
    </div>
    <div class="row">
        @foreach($contents as $content)
            {!! $content !!}
        @endforeach
    </div>
</div>
