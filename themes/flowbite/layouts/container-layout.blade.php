<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head @class(['dark' => admin_repo()->isDarkMode])>
    @include(admin_template('layouts.parts.head'))
    @adminSystemStyles()
    @adminSystemCss()
    @adminSystemJsVariables()
</head>
<body @class(['bg-gray-50 dark:bg-gray-800'])>

    @include(admin_template('layouts.parts.nav'))

    <div class="flex pt-16 overflow-hidden bg-gray-50 dark:bg-gray-900">
        @include(admin_template('layouts.parts.side-bar'))
        @include(admin_template('layouts.parts.modals'))
        <div class="content-wrapper">
            <section class="content" id="admin-content">
                @yield('content')
            </section>
        </div>
        @include(admin_template('layouts.parts.footer'))
        @include(admin_template('layouts.parts.control-sidebar'))

        @adminSystemScripts()
        @adminSystemJs()
    </div>
</body>
</html>
