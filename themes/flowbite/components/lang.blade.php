<div
    x-data="{ currentTab: '{{ $current_lang }}' }"
    @class(['flex flex-wrap items-start'])
    @attributes($attributes)
    data-label-width="{{ $label_width }}"
    data-vertical="@json($vertical)"
>
    @if(!$reversed)
        <label
            for="input_updated_at"
            @class([
                'w-full' => $vertical,
                'w-' . $label_width . '/12' => !$vertical
            ])
        >
            {{ $title }}
        </label>
    @endif

    <div
        @class([
            'flex-1' => !$vertical,
            'w-full pl-0' => $vertical
        ])
    >
        <ul class="flex hidden sm:flex text-sm font-medium text-center text-gray-500 divide-x divide-gray-200 rounded-lg dark:divide-gray-600 dark:text-gray-400" id="{{ $id }}-tab">
            @foreach($inside_inputs as $lang => $input)
                <li class="w-full">
                    <button
                        @click="currentTab = '{{ $lang }}'"
                        type="button"
{{--                        :class="{--}}
{{--                            'px-4 py-2 text-sm font-medium rounded-t-lg': true,--}}
{{--                            'bg-gray-50 dark:bg-gray-800 dark:text-white text-gray-900': currentTab === '{{ $lang }}'--}}
{{--                            'bg-gray-100 dark:bg-gray-100 dark:text-white text-gray-700': currentTab !== '{{ $lang }}',--}}
{{--                        }"--}}
                        :class="{
                            'bg-white text-gray-900 dark:bg-gray-800 dark:text-white': currentTab === '{{ $lang }}',
                            'bg-gray-50 text-gray-500 hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-400 dark:hover:bg-gray-600': currentTab !== '{{ $lang }}'
                        }"
                        class="inline-block w-full p-4 focus:outline-none {{ $loop->first ? 'rounded-tl-lg' : '' }} {{ $loop->last ? 'rounded-tr-lg' : '' }}"
                    >
                        {{ strtoupper($lang) }}
                    </button>
                </li>
            @endforeach
        </ul>

        <div class="">
            @foreach($inside_inputs as $lang => $input)
                <div
                    x-show="currentTab === '{{ $lang }}'"
                    class="space-y-4 tab-pane"
                    id="{{ $id }}-{{ $lang }}-content"
                    role="tabpanel"
                    aria-labelledby="{{ $id }}-{{ $lang }}-tab"
                >
                    <div>
                        {!! $input !!}
                    </div>
                    <button
                        type="button"
                        class="text-blue-600 hover:underline"
                        data-click="admin::translate" data-params="{{ $lang }}"
                    >
                        <i class="fas fa-language"></i> @lang('admin.translate_field_to_lang', ['name' => $title, 'lang' => strtoupper($lang)])
                    </button>
                </div>
            @endforeach
        </div>
    </div>

    @if($reversed)
        <label
            for="input_updated_at"
            @class([
                'w-full' => $vertical,
                'w-' . $label_width . '/12' => !$vertical
            ])
        >
            {{ $title }}
        </label>
    @endif
</div>


{{--<div--}}
{{--    @class(['form-group row'])--}}
{{--    @attributes($attributes)--}}
{{--    data-label-width="{{ $label_width }}"--}}
{{--    data-vertical="@json($vertical)"--}}
{{-->--}}
{{--    @if(!$reversed)--}}
{{--        <label for="input_updated_at" @class(['col-sm-' . $label_width => ! $vertical])>{{ $title }}</label>--}}
{{--    @endif--}}
{{--    <div @class(['col-sm-' . (12 - $label_width) => !$vertical, 'col-sm-12 pl-0' => $vertical])>--}}

{{--        <ul class="nav nav-tabs" id="{{ $id }}-tab" role="tablist">--}}
{{--            @foreach($inside_inputs as $lang => $input)--}}
{{--                <li class="nav-item">--}}
{{--                    <a @class(['nav-link', 'active' => $lang === $current_lang]) id="{{ $id }}-{{ $lang }}-tab" data-toggle="pill" href="#{{ $id }}-{{ $lang }}-content" role="tab" aria-controls="#{{ $id }}-{{ $lang }}-content" aria-selected="true">{{ strtoupper($lang) }}</a>--}}
{{--                </li>--}}
{{--            @endforeach--}}
{{--        </ul>--}}
{{--        <div class="tab-content" id="{{ $id }}-tabContent">--}}
{{--            @foreach($inside_inputs as $lang => $input)--}}
{{--                <div @class(['tab-pane fade show', 'active' => $lang === $current_lang]) id="{{ $id }}-{{ $lang }}-content" role="tabpanel" aria-labelledby="{{ $id }}-{{ $lang }}-tab">--}}
{{--                    <div class="">--}}
{{--                        {!! $input !!}--}}
{{--                    </div>--}}
{{--                    <button type="button" class="btn btn-link" data-click="admin::translate" data-params="{{ $lang }}"><i class="fas fa-language"></i> @lang('admin.translate_field_to_lang', ['name' => $title, 'lang' => strtoupper($lang)])</button>--}}
{{--                </div>--}}
{{--            @endforeach--}}
{{--        </div>--}}
{{--    </div>--}}
{{--    @if($reversed)--}}
{{--        <label for="input_updated_at" @class(['col-sm-' . $label_width => ! $vertical])>{{ $title }}</label>--}}
{{--    @endif--}}
{{--</div>--}}
