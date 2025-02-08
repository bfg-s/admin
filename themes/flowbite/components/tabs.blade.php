<div
    x-data="{
        activeTab: (window.location.hash && window.location.hash.substring(1)) || '{{ collect($contents)->firstWhere(fn($tab) => $tab->isActive())->getId() }}',
        init() {
            window.addEventListener('hashchange', () => {
                this.activeTab = window.location.hash.substring(1);
            });
        },
        setTab(tabId) {
            this.activeTab = tabId;
            window.location.hash = tabId;
        }
    }"
{{--    class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800"--}}
    class="mt-2"
>
    <div class="sm:hidden">
        <label for="tabs" class="sr-only">Select tab</label>
        <select id="tabs" x-model="activeTab" x-on:change="window.location.hash = $event.target.value; setTab($event.target.value)" class="block w-full p-2.5 bg-gray-50 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            @foreach($contents as $tab)
                <option value="{{ $tab->getId() }}">@lang($tab->getTitle())</option>
            @endforeach
        </select>
    </div>
    <ul class="mx-2 hidden sm:flex text-sm font-medium text-center text-gray-500 divide-x divide-gray-200 rounded-lg dark:divide-gray-600 dark:text-gray-400" role="tablist">
        @foreach($contents as $tab)
            <li class="w-full">
                <a href="#{{ $tab->getId() }}" x-on:click.prevent="setTab('{{ $tab->getId() }}')" :class="{'bg-white text-gray-900 dark:bg-gray-800 dark:text-white': activeTab === '{{ $tab->getId() }}', 'bg-gray-50 text-gray-500 hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-400 dark:hover:bg-gray-600': activeTab !== '{{ $tab->getId() }}'}"
                   class="inline-block w-full p-4 focus:outline-none {{ $loop->first ? 'rounded-tl-lg' : '' }} {{ $loop->last ? 'rounded-tr-lg' : '' }}">
                    @if($tab->getIcon())
                        <i class="{{ $tab->getIcon() }} mr-1"></i>
                    @endif
                    @lang($tab->getTitle())
                </a>
            </li>
        @endforeach
    </ul>
    <div id="fullWidthTabContent" class="">
        @foreach($contents as $tab)
            <div x-show="activeTab === '{{ $tab->getId() }}'" class="pt-2" id="{{ $tab->getId() }}" role="tabpanel">
                @include(admin_template('components.tab-content'), array_merge([
                    'contents' => $tab->getContents(),
                    'attributes' => $tab->getAttributes(),
                ], $tab->getViewDate()))
            </div>
        @endforeach
    </div>
</div>

{{--<div--}}
{{--    @class(['row'])--}}
{{--    @attributes($attributes)--}}
{{-->--}}
{{--    @if($left)--}}
{{--        @if($vertical) <div class="col-md-2"> @endif--}}
{{--            <ul--}}
{{--                @class(['nav nav-tabs', 'flex-column h-100' => $vertical, 'w-100' => ! $vertical])--}}
{{--                role="tablist"--}}
{{--                @if($vertical) aria-orientation="vertical" @endif--}}
{{--            >--}}
{{--                @foreach($contents as $tab)--}}
{{--                    <li class="nav-item">--}}
{{--                        <a--}}
{{--                            href="#{{ $tab->getId() }}"--}}
{{--                            @class(['nav-link', 'active' => $tab->isActive()])--}}
{{--                            id="{{ $tab->getId() }}-label"--}}
{{--                            data-toggle="pill"--}}
{{--                            role="tab"--}}

{{--                            aria-selected="{{ $tab->isActive() ? 'true' : 'false' }}"--}}
{{--                        >--}}
{{--                            @if($tab->getIcon())--}}
{{--                                <i class="{{ $tab->getIcon() }} mr-1"></i>--}}
{{--                            @endif--}}
{{--                            @lang($tab->getTitle())--}}
{{--                        </a>--}}
{{--                    </li>--}}
{{--                @endforeach--}}
{{--            </ul>--}}
{{--        @if($vertical) </div> @endif--}}
{{--        @if($vertical) <div class="col-md-10"> @endif--}}
{{--            <div @class(['tab-content', 'w-100' => ! $vertical])>--}}
{{--                @foreach($contents as $tab)--}}
{{--                    @foreach($tab->getContents() as $content)--}}
{{--                        {!! $content !!}--}}
{{--                    @endforeach--}}
{{--                    @include(admin_template('components.tab-content'), array_merge([--}}
{{--                        'contents' => $tab->getContents(),--}}
{{--                        'attributes' => $tab->getAttributes(),--}}
{{--                    ], $tab->getViewDate()))--}}
{{--                @endforeach--}}
{{--            </div>--}}
{{--        @if($vertical) </div> @endif--}}
{{--    @else--}}
{{--        @if($vertical) <div class="col-md-10"> @endif--}}
{{--            <div @class(['tab-content', 'w-100' => ! $vertical])>--}}
{{--                @foreach($contents as $tab)--}}
{{--                    @include(admin_template('components.tab-content'), array_merge([--}}
{{--                        'contents' => $tab->getContents(),--}}
{{--                        'attributes' => $tab->getAttributes(),--}}
{{--                    ], $tab->getViewDate()))--}}
{{--                @endforeach--}}
{{--            </div>--}}
{{--        @if($vertical) </div> @endif--}}
{{--        @if($vertical) <div class="col-md-2"> @endif--}}
{{--            <ul--}}
{{--                @class(['nav nav-tabs nav-tabs-right', 'flex-column h-100' => $vertical, 'w-100' => ! $vertical])--}}
{{--                role="tablist"--}}
{{--                @if($vertical) aria-orientation="vertical" @endif--}}
{{--            >--}}
{{--                @foreach($contents as $tab)--}}
{{--                    <li class="nav-item">--}}
{{--                        <a--}}
{{--                            href="#{{ $tab->getId() }}"--}}
{{--                            @class(['nav-link', 'active' => $tab->isActive()])--}}
{{--                            id="{{ $tab->getId() }}-label"--}}
{{--                            data-toggle="pill"--}}
{{--                            role="tab"--}}

{{--                            aria-selected="{{ $tab->isActive() ? 'true' : 'false' }}"--}}
{{--                        >--}}
{{--                            @if($tab->getIcon())--}}
{{--                                <i class="{{ $tab->getIcon() }} mr-1"></i>--}}
{{--                            @endif--}}
{{--                            @lang($tab->getTitle())--}}
{{--                        </a>--}}
{{--                    </li>--}}
{{--                @endforeach--}}
{{--            </ul>--}}
{{--        @if($vertical) </div> @endif--}}
{{--    @endif--}}
{{--</div>--}}
