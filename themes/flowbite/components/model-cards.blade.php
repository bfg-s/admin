<div
    @attributes($attributes)
    id="{{ $id }}"
>
    <div class="mb-3 flex space-x-2">
        @foreach($rows as $row)
            @php
                $now = request()->get($model_name, $order_field) == $row['sort'];
                $type = $now ? ($select === 'desc' ? 'down' : 'up-alt') : 'down';
            @endphp
            @if($row['sort'])
                <a
                    href="{{ admin_url_with_get([
                        $model_name => $row['sort'],
                        $model_name.'_type' => $now ? ($select === 'desc' ? 'asc' : 'desc') : 'asc',
                    ]) }}"
                    data-sort="{{ $row['sort'] }}"
                    @class([
                        'px-4 py-2 rounded-md text-sm font-medium',
                        'bg-gray-300 text-gray-700' => ! $now,
                        'bg-gray-800 text-white' => $now
                    ])
                >
                    <i class="fas fa-sort-amount-{{ $type }} hidden sm:inline"></i>
                    {{ $row['label'] }}
                </a>
            @endif
        @endforeach
    </div>
    <div class="flex flex-col xl:!flex-row">
        @foreach($contents as $content)
            {!! $content !!}
        @endforeach
    </div>
</div>




{{--<div--}}
{{--    @attributes($attributes)--}}
{{--    id="{{ $id }}"--}}
{{-->--}}
{{--    <div class="btn-group mb-3">--}}
{{--        @foreach($rows as $row)--}}
{{--            @php--}}
{{--                $now = request()->get($model_name, $order_field) == $row['sort'];--}}
{{--                $type = $now ? ($select === 'desc' ? 'down' : 'up-alt') : 'down';--}}
{{--            @endphp--}}
{{--            @if($row['sort'])--}}
{{--                <a href="{{ admin_url_with_get([--}}
{{--                    $model_name => $row['sort'],--}}
{{--                    $model_name.'_type' => $now ? ($select === 'desc' ? 'asc' : 'desc') : 'asc',--}}
{{--                ]) }}" data-sort="{{ $row['sort'] }}" @class(['btn', 'btn-secondary' => ! $now, 'btn-dark' => $now])>--}}
{{--                    <i class="fas fa-sort-amount-{{ $type }} d-none d-sm-inline"></i> {{ $row['label'] }}--}}
{{--                </a>--}}
{{--            @endif--}}
{{--        @endforeach--}}
{{--    </div>--}}
{{--    <div class="row">--}}
{{--        @foreach($contents as $content)--}}
{{--            {!! $content !!}--}}
{{--        @endforeach--}}
{{--    </div>--}}
{{--</div>--}}
