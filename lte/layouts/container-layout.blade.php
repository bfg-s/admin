<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include(admin_template('layouts.parts.head'))
    @adminSystemStyles()
    @adminSystemCss()
    @adminSystemJsVariables()
</head>
<body @class(['dark-mode' => admin_repo()->isDarkMode, 'hold-transition sidebar-mini text-sm layout-fixed layout-navbar-fixed'])>

    <div class="wrapper">
        @include(admin_template('layouts.parts.nav'))
        @include(admin_template('layouts.parts.side-bar'))
        @include(admin_template('layouts.parts.modals'))
        <div class="content-wrapper">
            <section class="content" id="admin-content">
                @yield('content')
            </section>
        </div>
        @include(admin_template('layouts.parts.footer'))
        @include(admin_template('layouts.parts.control-sidebar'))
    </div>

    @adminSystemScripts()
    @adminSystemJs()
</body>
</html>
