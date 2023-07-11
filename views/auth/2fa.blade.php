<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title>Admin</title>
    <link rel="stylesheet" type="text/css" data-turbolinks-track="reload" href="{{ asset('ljs/css/ljs.css') }}"/>
    <link rel="stylesheet" type="text/css" data-turbolinks-track="reload" href="{{ asset('admin-asset/plugins/fontawesome-free/css/all.min.css') }}"/>
    <link rel="stylesheet" type="text/css" data-turbolinks-track="reload" href="{{ asset('admin-asset/plugins/flag-icon-css/css/flag-icon.min.css') }}"/>
    <link rel="stylesheet" type="text/css" data-turbolinks-track="reload" href="{{ asset('admin-asset/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}"/>
    <link rel="stylesheet" type="text/css" data-turbolinks-track="reload" href="{{ asset('admin-asset/css/adminlte.min.css') }}"/>
    <link rel="stylesheet" type="text/css" data-turbolinks-track="reload" href="{{ admin_asset('css/app.css') }}"/>
    <link rel="stylesheet" type="text/css" data-turbolinks-track="reload" href="{{ admin_asset('css/dark.css') }}"/>
    <link rel="stylesheet" type="text/css" data-turbolinks-track="reload" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700"/>

    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <link rel="icon" type="image/png" href="https://wood.dev/admin/img/favicon.png"/>
    <link rel="apple-touch-icon" href="{{ admin_asset('img/favicon.png') }}"/>
    <meta name="lar-token" content="{{ csrf_token() }}"/>
</head>
<body class=" dark-mode hold-transition login-page" id="admin-login-container">

<div class="login-box">
    <div class="login-logo">
        <a href="{{route('admin.home')}}">{!! __('admin.login_title') !!}</a>
    </div>
    <!-- /.login-logo -->
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">{{ __('admin.2fa_auth_msg') }}</p>

            <form action="{{route('admin.2fa.post')}}" method="post" target>
                @csrf
                <div class="input-group mb-3">
                    <input type="text" name="code" autofocus class="form-control" placeholder="{{__('admin.code')}}"/>
                    <input type="hidden" name="login" value="{{ $login }}"/>
                    <input type="hidden" name="password" value="{{ $password }}"/>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-key"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-8">

                    </div>
                    <!-- /.col -->
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block">{{__('admin.sign_in')}}</button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>
        </div>
        <!-- /.login-card-body -->
    </div>
</div>

<script data-exec-on-popstate>
    /**
     * Variables Admin\Layouts\AdminAuthLayout
     */
    var darkMode = true;
    /**
     * Lines Admin\Layouts\AdminAuthLayout
     */
    document.addEventListener("ljs:load", function (e) {
        window.state.admin = "";
    });
    /**
     * Lines Flash
     */
    document.addEventListener("ljs:load", function (e) {
        ljs.exec([]);
    });</script>
<script src="https://wood.dev/admin/plugins/alpine.min.js" data-turbolinks-track="reload"></script>
</body>
</html>
