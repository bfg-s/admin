<!-- Navbar -->
<nav @class(['fixed z-30 w-full bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700'])>

    <div class="px-3 py-3 lg:px-5 lg:pl-3" @updateWithPjax>
        <div class="flex items-center justify-between">
            <div class="flex items-center justify-start">
                <button id="toggleSidebarMobile" aria-expanded="true" aria-controls="sidebar" class="p-2 text-gray-600 rounded cursor-pointer lg:hidden hover:text-gray-900 hover:bg-gray-100 focus:bg-gray-100 dark:focus:bg-gray-700 focus:ring-2 focus:ring-gray-100 dark:focus:ring-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                    <svg id="toggleSidebarMobileHamburger" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg>
                    <svg id="toggleSidebarMobileClose" class="hidden w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </button>
                <a href="{{ route('admin.index') }}" class="flex ml-2 md:mr-24">
                    <img src="{{ asset('admin/img/favicon.png') }}" class="h-8 mr-3" alt="Bfg admin logo" />
                    <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap dark:text-white">{!! __('admin.login_title') !!}</span>
                </a>
                @include(admin_template('layouts.parts.global-search'))

                <div class="flex items-center">
                    @foreach(admin_repo()->menuList->where('left_nav_bar_view') as $menu)
                        @if(View::exists($menu->getLeftNavBarView()))
                            @include($menu->getLeftNavBarView(), $menu->getParams())
                        @else
                            {!! new ($menu->getLeftNavBarView())(...$menu->getParams()); !!}
                        @endif
                    @endforeach
                    @foreach(admin_repo()->menuList->where('left_nav_bar_vue') as $menu)
                        {!! (new ($menu->getLeftNavBarVue()))->attr($menu->getParams()); !!}
                    @endforeach
                </div>

                @if(\Admin\Facades\Admin::getServers())
                    <div class="relative">
                        <a
                            href="#"
                            class="text-gray-600 hover:text-gray-800"
                            x-data="{ open: false }"
                            @click.prevent="open = !open"
                        >
                            <i class="fas fa-server"></i>
                        </a>
                        <div
                            x-show="open"
                            @click.away="open = false"
                            class="absolute left-0 mt-2 w-48 bg-white shadow-md rounded-lg border border-gray-200 z-50"
                        >
                            <span class="block px-4 py-2 text-sm text-gray-700 font-semibold border-b">{{ __('admin.servers') }}</span>
                            @foreach(\Admin\Facades\Admin::getServers() as $server)
                                <a
                                    href="{{ \Admin\Facades\Admin::serverUrl($server) }}"
                                    class="block px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-800"
                                >
                                    <i class="fas fa-server mr-2"></i> {{ $server['name'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
            <div class="flex items-center">

                <button class="p-2 text-gray-500 rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700" data-click="back" title="@lang('admin.back')" href="javascript:void(0)">
                    <i class="fas fa-arrow-left"></i>
                </button>

                <button class="p-2 text-gray-500 rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700" data-click="reload" title="@lang('admin.refresh')" href="javascript:void(0)">
                    <i class="fas fa-redo-alt"></i>
                </button>

                @if(admin_repo()->getCurrentQuery && count(admin_repo()->getCurrentQuery))
                    <button class="p-2 text-gray-500 rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700" data-click="location" data-param="{{url()->current()}}" title="@lang('admin.reset_page')" href="javascript:void(0)">
                        <i class="fas fa-retweet"></i>
                    </button>
                @endif

                <button id="toggleSidebarMobileSearch" type="button" class="p-2 text-gray-500 rounded-lg lg:hidden hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                    <span class="sr-only">@lang('admin.search')</span>
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path></svg>
                </button>

                @include(admin_template('layouts.parts.notifications'))

                @include(admin_template('layouts.parts.apps'))

                <button x-data="toggleDark('{{ route('admin.toggle_dark') }}')" x-on:click="toggle" data-tooltip-target="tooltip-toggle" type="button" class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5">
                    @if(admin_repo()->isDarkMode)
                        <svg id="theme-toggle-light-icon" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
                    @else
                        <svg id="theme-toggle-dark-icon" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                    @endif
                </button>
                <div id="tooltip-toggle" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip">
                    Toggle dark mode
                    <div class="tooltip-arrow" data-popper-arrow></div>
                </div>

                <div class="flex items-center ml-3">
                    <div>
                        <button type="button" class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600" id="user-menu-button-2" aria-expanded="false" data-dropdown-toggle="dropdown-2">
                            <span class="sr-only">Open user menu</span>
                            <img src="{{asset(admin_user()->avatar)}}" class="w-8 h-8 rounded-full" alt="{{admin_user()->name}}">
                        </button>
                    </div>

                    <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded shadow dark:bg-gray-700 dark:divide-gray-600" id="dropdown-2">
                        <div class="px-4 py-3" role="none">
                            <p class="text-sm text-gray-900 dark:text-white" role="none">
                                {{admin_user()->name}}
                            </p>
                            <p class="text-sm font-medium text-gray-900 truncate dark:text-gray-300" role="none">
                                {{admin_user()->email}}
                            </p>
                        </div>
                        <ul class="py-1" role="none">
                            @if (config('admin.home-route'))
                                <li>
                                    <a href="{{ route(config('admin.home-route')) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white" role="menuitem">@lang('admin.dashboard')</a>
                                </li>
                            @endif
{{--                            <li>--}}
{{--                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white" role="menuitem">Settings</a>--}}
{{--                            </li>--}}

                            <li>
                                <a href="{{route('admin.profile')}}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white" role="menuitem">@lang('admin.profile')</a>
                            </li>
                            <li>
                                <a
                                    href="javascript:void(0)"
                                    data-click="alert::confirm"
                                    data-turbolinks="false"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white"
                                    role="menuitem"
                                    data-params='{{__('admin.logout')}}, {{admin()->name}}?&&@json(['redirect' => route('admin.profile.logout')])'
                                >{{__('admin.logout')}}</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
<!-- /.navbar -->
