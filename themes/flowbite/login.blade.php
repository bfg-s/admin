@extends(admin_template('layouts.auth-layout'))

@section('content')
    <div class="login-box">
        <div class="login-logo">
            <a href="{{route('admin.home')}}">{!! __('admin.login_title') !!}</a>
        </div>
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">
                <div class="login-box-msg">{{__('admin.login_message')}}</div>

                @if($session_message = session('message'))
                    <div class="login-box-msg text-red">
                        {{ $session_message }}
                    </div>
                @endif

                <form method="post" action="{{ route('admin.2fa') }}" target>
                    @csrf
                    <div class="input-group mb-3">
                        <input type="text" name="login" autofocus class="form-control @error('login') is-invalid @enderror" placeholder="{{__('admin.email')}}"/>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                        @error('login')
                            <span id="exampleInputEmail1-error" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="{{__('admin.password')}}"/>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                        @error('password')
                        <span id="exampleInputEmail1-error" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-8">
                            <div class="icheck-primary">
                                <input type="checkbox" id="remember" name="remember">
                                <label for="remember">
                                    {{__('admin.remember_me')}}
                                </label>
{{--                                @error('remember')--}}
{{--                                <span id="exampleInputEmail1-error" class="error invalid-feedback">{{ $message }}</span>--}}
{{--                                @enderror--}}
                            </div>
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
