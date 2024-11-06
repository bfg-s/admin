@extends(admin_template('layouts.auth-layout'))

@section('content')
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
                        <input type="hidden" name="remember" value="{{ $remember }}"/>
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
@endsection
