<div
{{--    class="w-full sm:w-1/2 md:w-1/3 flex flex-col"--}}
    @class(['m-2 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 p-4 dark:bg-gray-800 w-full'])
>
    <div class="bg-light flex-1 rounded-lg shadow-sm relative">
        @if (isset($ribbon) && $ribbon)
            <div class="absolute top-0 right-0 space-y-2 bg-white dark:bg-gray-800 dark:text-gray-400 text-gray-500 text-sm rounded-lg">
                @if (is_string($ribbon))
                    <div class="bg-yellow-400 text-lg text-white py-1 px-3 rounded-md">
                        {{ $ribbon }}
                    </div>
                @elseif(is_array($ribbon) && isset($ribbon['color']) && isset($ribbon['text']))
                    <div class="bg-{{ $ribbon['color'] }} text-lg py-1 px-3 rounded-md">
                        {{ $ribbon['text'] }}
                    </div>
                @elseif(is_array($ribbon) && isset($ribbon[0]) && isset($ribbon[1]))
                    <div class="bg-{{ $ribbon[0] }} text-lg py-1 px-3 rounded-md">
                        {{ $ribbon[1] }}
                    </div>
                @elseif(is_array($ribbon) && isset($ribbon[0]))
                    <div class="bg-yellow-400 text-lg py-1 px-3 rounded-md">
                        {{ $ribbon[0] }}
                    </div>
                @endif
            </div>
        @endif
        <div class="border-b-0"></div>
        <div class="pt-0">
            <div class="flex">
                <div @class(['w-9/12' => $avatarField, 'w-full' => !$avatarField])>
                    @if($titleField)
                        <h2 class="font-semibold text-2xl text-gray-500 dark:text-gray-400">
                            <b>{!! is_string($titleField) ? multi_dot_call($model, $titleField) : (is_callable($titleField) ? call_user_func($titleField, $model) : $titleField) !!}</b>
                        </h2>
                    @endif
                    @if($subtitleField)
                        <p class="text-xl text-gray-600">{!! is_string($subtitleField) ? multi_dot_call($model, $subtitleField) : (is_callable($subtitleField) ? call_user_func($subtitleField, $model) : $subtitleField) !!}</p>
                    @endif
                    <ul @class(['mb-0 text-sm text-gray-600'])>
                        @foreach($rows as $row)
                            @if(! $row['hide'] && $row['field'] !== $avatarField && $row['field'] !== $titleField && $row['field'] !== $subtitleField)
                                <li class="mb-1">
                                    @if($row['icon'])
                                        <span class="mr-2">
                                            <i class="fa-lg {{ $row['icon'] }}"></i>
                                        </span>
                                    @endif
                                    @if($row['label']) {{ $row['label'] }}: @endif {!! $row['value'] !!}
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
                @if($avatarField)
                    <div class="w-3/12 text-center">
                        @php $image = is_string($avatarField) ? multi_dot_call($model, $avatarField) : (is_callable($avatarField) ? call_user_func($avatarField, $model) : $avatarField); @endphp
                        <img
                            src="{{ asset($image) }}"
                            alt="user-avatar"
                            class="rounded-full mx-auto object-cover"
                            data-click='fancy::img'
                            data-params='{{ asset($image) }}'
                        />
                    </div>
                @endif
            </div>
        </div>
        <div class="pl-4 pt-3">
            <div class="flex justify-between">
                <div class="text-left">
                    @if($checkBox)
                        {!! call_user_func($checkBox, $model) !!}
                    @endif
                </div>
                <div class="text-right">
                    @foreach($buttons as $button)
                        {!! is_callable($button) ? call_user_func($button, $model) : $button !!}
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>



{{--<div class="col-12 col-sm-6 col-md-4 d-flex align-items-stretch flex-column">--}}
{{--    <div class="card bg-light d-flex flex-fill">--}}
{{--        @if (isset($ribbon) && $ribbon)--}}
{{--            @if (is_string($ribbon))--}}
{{--                <div class="ribbon-wrapper ribbon-xl">--}}
{{--                    <div class="ribbon bg-warning text-lg">--}}
{{--                        {{ $ribbon }}--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            @elseif(is_array($ribbon) && isset($ribbon['color']) && isset($ribbon['text']))--}}
{{--                <div class="ribbon-wrapper ribbon-xl">--}}
{{--                    <div class="ribbon bg-{{ $ribbon['color'] }} text-lg">--}}
{{--                        {{ $ribbon['text'] }}--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            @elseif(is_array($ribbon) && isset($ribbon[0]) && isset($ribbon[1]))--}}
{{--                <div class="ribbon-wrapper ribbon-xl">--}}
{{--                    <div class="ribbon bg-{{ $ribbon[0] }} text-lg">--}}
{{--                        {{ $ribbon[1] }}--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            @elseif(is_array($ribbon) && isset($ribbon[0]))--}}
{{--                <div class="ribbon-wrapper ribbon-xl">--}}
{{--                    <div class="ribbon bg-warning text-lg">--}}
{{--                        {{ $ribbon[0] }}--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            @endif--}}
{{--        @endif--}}
{{--        <div class="card-header text-muted border-bottom-0"></div>--}}
{{--        <div class="card-body pt-0">--}}
{{--            <div class="row">--}}
{{--                <div @class(['col-7' => $avatarField, 'col-12' => !$avatarField])>--}}
{{--                    @if($titleField)--}}
{{--                        <h2 class="lead">--}}
{{--                            <b>{!! is_string($titleField) ? multi_dot_call($model, $titleField) : (is_callable($titleField) ? call_user_func($titleField, $model) : $titleField) !!}</b>--}}
{{--                        </h2>--}}
{{--                    @endif--}}
{{--                    @if($subtitleField)--}}
{{--                        <p class="text-muted text-sm">{!! is_string($subtitleField) ? multi_dot_call($model, $subtitleField) : (is_callable($subtitleField) ? call_user_func($subtitleField, $model) : $subtitleField) !!}</p>--}}
{{--                    @endif--}}
{{--                    <ul @class(['mb-0 fa-ul text-muted', 'ml-4'])>--}}
{{--                        @foreach($rows as $row)--}}
{{--                            @if(! $row['hide'] && $row['field'] !== $avatarField && $row['field'] !== $titleField && $row['field'] !== $subtitleField)--}}
{{--                                <li class="small">--}}
{{--                                    @if($row['icon'])--}}
{{--                                        <span class="fa-li">--}}
{{--                                            <i class="fa-lg {{ $row['icon'] }}"></i>--}}
{{--                                        </span>--}}
{{--                                    @endif--}}
{{--                                    @if($row['label']) {{ $row['label'] }}: @endif {!! $row['value'] !!}--}}
{{--                                </li>--}}
{{--                            @endif--}}
{{--                        @endforeach--}}
{{--                    </ul>--}}
{{--                </div>--}}
{{--                @if($avatarField)--}}
{{--                    <div class="col-5 text-center">--}}
{{--                        @php $image = is_string($avatarField) ? multi_dot_call($model, $avatarField) : (is_callable($avatarField) ? call_user_func($avatarField, $model) : $avatarField); @endphp--}}
{{--                        <img--}}
{{--                            src="{{ asset($image) }}"--}}
{{--                            alt="user-avatar"--}}
{{--                            class="img-circle img-fluid"--}}
{{--                            data-click='fancy::img'--}}
{{--                            data-params='{{ asset($image) }}'--}}
{{--                        />--}}
{{--                    </div>--}}
{{--                @endif--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        <div class="card-footer">--}}
{{--            <div class="row">--}}
{{--                <div class="col-6 text-left pl-3 pt-1">--}}
{{--                    @if($checkBox)--}}
{{--                        {!! call_user_func($checkBox, $model) !!}--}}
{{--                    @endif--}}
{{--                </div>--}}
{{--                <div class="col-6 text-right">--}}
{{--                    @foreach($buttons as $button)--}}
{{--                        {!! is_callable($button) ? call_user_func($button, $model) : $button !!}--}}
{{--                    @endforeach--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
