<!-- Navbar -->
<nav @class(['main-header loadedContent', 'navbar', 'navbar-expand', 'navbar-dark' => admin_repo()->isDarkMode, 'navbar-white navbar-light' => !admin_repo()->isDarkMode])>
    <!-- Left navbar links -->
    <ul class="navbar-nav" @updateWithPjax>
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-click="back" title="@lang('admin.back')" href="javascript:void(0)"><i class="fas fa-arrow-left"></i></a>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-click="reload" title="@lang('admin.refresh')" href="javascript:void(0)">
                <i class="fas fa-redo-alt"></i>
            </a>
        </li>
        @if(admin_repo()->getCurrentQuery && count(admin_repo()->getCurrentQuery))
            <li class="nav-item">
                <a class="nav-link" data-click="location" data-param="{{url()->current()}}"
                   title="@lang('admin.reset_page')" href="javascript:void(0)"><i class="fas fa-retweet"></i></a>
            </li>
        @endif
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

        @if(\Admin\Facades\Admin::getServers())
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="fas fa-server"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-left">
                    <span class="dropdown-item dropdown-header">{{__('admin.servers')}}</span>
                    <div class="dropdown-divider"></div>
                    @foreach(\Admin\Facades\Admin::getServers() as $server)
                        <a href="{{\Admin\Facades\Admin::serverUrl($server)}}" class="dropdown-item">
                            <i class="fas fa-server mr-2"></i> {{$server['name']}}
                        </a>
                    @endforeach
                </div>
            </li>
        @endif
    </ul>

    <!-- SEARCH FORM -->
    @include(admin_template('layouts.parts.global-search'))
    @include(admin_template('layouts.parts.live-reload'))

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto" @updateWithPjax>
        @foreach(admin_repo()->menuList->where('badge')->where('link') as $menu)
            @php
                $badge = $menu->getBadge();
                $counter = isset($badge['instructions']) && $badge['instructions'] ?
                    eloquent_instruction($badge['text'], $badge['instructions'])->count() :
                    $badge['text'];
                $link = $menu->getLink() ? (isset($badge['params']) ? admin_make_url_with_params($menu->getLink(), $badge['params']) : $menu->getLink()) : 'javascript:void(0)';
            @endphp
            @if($counter)
                <li class="nav-item">
                    <a class="nav-link" href="{{$link}}" title="{{__($badge['title'] ?? $menu->getTitle())}}">
                        <i class="{{$menu->getIcon()}}"></i>
                        <span class="badge badge-{{$badge['type'] ?? 'info'}} navbar-badge">{{$counter}}</span>
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

        @foreach(admin_repo()->menuList->where('nav_bar_vue')->where('prepend', false) as $menu)
            {!! (new ($menu->getNavBarVue()))->attr($menu->getParams()); !!}
        @endforeach

        @if(admin()->isRoot())
            <li class="nav-item">
                <a class="nav-link" target="_blank" href="https://bfg-s.github.io/admin-documentation" title="{{__('admin.documentation')}}" role="button">
                    <i class="fas fa-book"></i>
                </a>
            </li>
        @endif

        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>

        <li>
            <a class="nav-link" target="_blank" href="{{url('/')}}" title="{{__('admin.open_homepage_in_new_tab')}}"><i
                    class="fas fa-external-link-square-alt"></i></a>
        </li>

        @foreach(admin_repo()->menuList->where('nav_bar_view')->where('prepend', true) as $menu)
            @if(View::exists($menu->getNavBarView()))
                @include($menu->getNavBarView(), $menu->getParams())
            @else
                {!! new ($menu->getNavBarView())(...$menu->getParams()); !!}
            @endif
        @endforeach

        @foreach(admin_repo()->menuList->where('nav_bar_vue')->where('prepend', true) as $menu)
            {!! (new ($menu->getNavBarVue()))->attr($menu->getParams()); !!}
        @endforeach

        @if(config('admin.lang_mode', true))
            <!-- Messages Dropdown Menu -->
            <li class="nav-item dropdown language_dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="flag-icon flag-icon-{{ App::getLocale() === 'en' ? 'us' : (App::getLocale() == 'uk' ? 'ua' : App::getLocale()) }}"></i>
                    <div class="d-none d-lg-inline d-xl-inline">{{strtoupper(App::getLocale())}}</div>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    @foreach(config('admin.languages', ['en', 'uk', 'ru']) as $lang)
                        <a class="dropdown-item {{App::getLocale() == $lang ? 'active' : ''}}"
                           href="{{remake_lang_url($lang)}}">
                            <i class="flag-icon flag-icon-{{$lang === 'en' ? 'us' : ($lang == 'uk' ? 'ua' : $lang)}}"></i> {{strtoupper($lang)}}
                        </a>
                    @endforeach
                </div>
            </li>
        @endif
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
