<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include(admin_template('layouts.parts.head'))
    @adminSystemStyles()
    @adminSystemCss()
    @adminSystemJsVariables()
</head>
<body @class(['dark-mode' => admin_repo()->isDarkMode, 'hold-transition login-page']) id="admin-content">
    @yield('content')
    @adminSystemScripts()
    @adminSystemJs()
</body>
</html>
