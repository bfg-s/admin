<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['dark' => admin_repo()->isDarkMode])>
<head>
    @include(admin_template('layouts.parts.head'))
    @adminSystemStyles()
    @adminSystemCss()
    @adminSystemJsVariables()
</head>
<body @class(['bg-gray-50 dark:bg-gray-800'])>

    @include(admin_template('layouts.parts.nav'))

    <div class="flex pt-16 overflow-hidden bg-gray-50 dark:bg-gray-900 loadedContent">
        @include(admin_template('layouts.parts.side-bar'))
        @include(admin_template('layouts.parts.modals'))
        <div class="fixed inset-0 z-10 hidden bg-gray-900/50 dark:bg-gray-900/90" id="sidebarBackdrop"></div>
        <div id="main-content" class="relative w-full h-full overflow-y-auto bg-gray-50 lg:ml-64 dark:bg-gray-900">
            <main class="content" id="admin-content">
                @yield('content')
            </main>
            @include(admin_template('layouts.parts.footer'))
        </div>

        @adminSystemScripts()
        @adminSystemJs()
    </div>
</body>
</html>
