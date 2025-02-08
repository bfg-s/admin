<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['dark' => admin_repo()->isDarkMode])>
<head>
    @include(admin_template('layouts.parts.head'))
    @adminSystemStyles()
    @adminSystemCss()
    @adminSystemJsVariables()
</head>
<body @class(['bg-gray-50 dark:bg-gray-800 hold-transition login-page']) id="admin-content">
    @yield('content')
    @adminSystemScripts()
    @adminSystemJs()
</body>
</html>
