<!-- Navbar -->
{{--<nav class="main-header navbar navbar-expand navbar-white navbar-light text-sm" @watch>--}}
<nav @class(['main-header', 'navbar', 'navbar-expand', 'navbar-dark' => admin_repo()->isDarkMode, 'navbar-white navbar-light' => !admin_repo()->isDarkMode]) @watch>
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-click="doc::back" title="@lang('lte.back')" href="javascript:void(0)"><i
                    class="fas fa-arrow-left"></i></a>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-click="doc::reload" title="@lang('lte.refresh')" href="javascript:void(0)"><i
                    class="fas fa-redo-alt"></i></a>
        </li>
        @if(admin_repo()->getCurrentQuery && count(admin_repo()->getCurrentQuery))
            <li class="nav-item">
                <a class="nav-link" data-click="doc::location" data-param="{{url()->current()}}"
                   title="@lang('lte.reset_page')" href="javascript:void(0)"><i class="fas fa-retweet"></i></a>
            </li>
        @endif
        @foreach(admin_repo()->menuList->where('left_nav_bar_view') as $menu)
            @if(View::exists($menu->getLeftNavBarView()))
                @include($menu->getLeftNavBarView(), $menu->getParams())
            @else
                {!! new ($menu->getLeftNavBarView())(...$menu->getParams()); !!}
            @endif
        @endforeach
    </ul>

    <!-- SEARCH FORM -->
{!! \LteAdmin\Components\Vue\GlobalSearch::create() !!}
{!! \LteAdmin\Components\Vue\LiveReloader::create() !!}

<!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        @foreach(admin_repo()->menuList->where('badge')->where('link') as $menu)
            @php
                $badge = $menu->getBadge();
                $counter = isset($badge['instructions']) && $badge['instructions'] ?
                    eloquent_instruction($badge['text'], $badge['instructions'])->count() :
                    $badge['text'];
                $link = $menu->getLink() ? (isset($badge['params']) ? makeUrlWithParams($menu->getLink(), $badge['params']) : $menu->getLink()) : 'javascript:void(0)';
            @endphp
            @if($counter)
                <li class="nav-item">
                    <a class="nav-link" href="{{$link}}" title="{{__($badge['title'] ?? $menu->getTitle())}}">
                        <i class="{{$menu->getIcon()}}"></i>
                        <span
                            class="badge badge-{{$badge['type'] ?? 'info'}} navbar-badge">{{$counter}}</span>
                    </a>
                </li>
            @endif
        @endforeach

        @foreach(admin_repo()->menuList->where('nav_bar_view')->where('prepend', false) as $menu)
            @if(View::exists($menu->getNavBarView()))
                @include($menu->getNavBarView(), $menu->getParams())
            @else
                {!! new ($menu->getNavBarView())(...$menu->getParams()); !!}
            @endif
        @endforeach
        <li class="nav-item">
            <a class="nav-link" href="javascript:void(0)" role="button" data-turbolinks="false"
               data-click="jax.lte_admin.toggle_dark">
                @if(admin_repo()->isDarkMode)
                    <i class="fas fa-sun"></i>
                @else
                    <i class="fas fa-adjust"></i>
                @endif
            </a>
        </li>
        <li>
            <a class="nav-link" target="_blank" href="{{url('/')}}" title="{{__('lte.open_homepage_in_new_tab')}}"><i
                    class="fas fa-external-link-square-alt"></i></a>
        </li>
        <li>
            <a class="nav-link" href="javascript:void(0)" data-click="alert::confirm"
               data-params="{{__('lte.logout')}}, {{admin()->name}}? && {{route('lte.profile.logout')}} >> $jax.get"
               title="{{__('lte.logout')}}"><i class="fas fa-sign-out-alt"></i></a>
        </li>
        @if(config('layout.lang_mode'))
        <!-- Messages Dropdown Menu -->
            <li class="nav-item dropdown language_dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="{{isset(config('lte.lang_flags')[App::getLocale()]) ? config('lte.lang_flags')[App::getLocale()] : ''}}"></i> {{strtoupper(App::getLocale())}}
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    @foreach(config('layout.languages') as $lang)
                        <a class="dropdown-item {{App::getLocale() == $lang ? 'active' : ''}}"
                           href="{{remake_lang_url($lang)}}">
                            <i class="{{isset(config('lte.lang_flags')[$lang]) ? config('lte.lang_flags')[$lang] : ''}}"></i> {{strtoupper($lang)}}
                        </a>
                    @endforeach
                </div>
            </li>
        @endif

        @foreach(admin_repo()->menuList->where('nav_bar_view')->where('prepend', true) as $menu)
            @if(View::exists($menu->getNavBarView()))
                @include($menu->getNavBarView(), $menu->getParams())
            @else
                {!! new ($menu->getNavBarView())(...$menu->getParams()); !!}
            @endif
        @endforeach

        {{--
        <!-- Notifications Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-bell"></i>
                <span class="badge badge-warning navbar-badge">15</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header">15 Notifications</span>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-envelope mr-2"></i> 4 new messages
                    <span class="float-right text-muted text-sm">3 mins</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-users mr-2"></i> 8 friend requests
                    <span class="float-right text-muted text-sm">12 hours</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-file mr-2"></i> 3 new reports
                    <span class="float-right text-muted text-sm">2 days</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
            </div>
        </li>
        --}}
        {{--
        <li class="nav-item">
            <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#">
                <i class="fas fa-th-large"></i>
            </a>
        </li>
        --}}
    </ul>
</nav>
<!-- /.navbar -->
