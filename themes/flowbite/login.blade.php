@extends(admin_template('layouts.auth-layout'))

@section('content')

    <main class="bg-gray-50 dark:bg-gray-900">
        <div class="flex flex-col items-center justify-center px-6 pt-8 mx-auto md:h-screen pt:mt-0 dark:bg-gray-900">
            <a href="https://flowbite-admin-dashboard.vercel.app/" class="flex items-center justify-center mb-8 text-2xl font-semibold lg:mb-10 dark:text-white">
                <img src="{{ asset('admin/img/favicon.png') }}" class="mr-4 h-11" alt="FlowBite Logo">
                <span>{!! __('admin.login_title') !!}</span>
            </a>
            <!-- Card -->
            <div class="w-full max-w-xl p-6 space-y-8 sm:p-8 bg-white rounded-lg shadow dark:bg-gray-800">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{__('admin.login_message')}}
                </h2>

                @if($session_message = session('message'))
                    @include(admin_template('parts.alerts.error'), ['title' => $session_message])
                @endif

                <form class="mt-8 space-y-6" method="post" action="{{ route('admin.2fa') }}" target>
                    @csrf
                    <div>
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{__('admin.email')}}</label>
                        <input type="text" name="login" id="email" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="name@company.com" required>
                        @error('login')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    <div>
                        <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{__('admin.password')}}</label>
                        <input type="password" name="password" id="password" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" required>
                        @error('password')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="remember" aria-describedby="remember" name="remember" type="checkbox" class="w-4 h-4 border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-primary-300 dark:focus:ring-primary-600 dark:ring-offset-gray-800 dark:bg-gray-700 dark:border-gray-600">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="remember" class="font-medium text-gray-900 dark:text-white">{{__('admin.remember_me')}}</label>
                        </div>
                        @error('remember')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    <button type="submit" class="w-full px-5 py-3 text-base font-medium text-center text-white bg-primary-700 rounded-lg hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 sm:w-auto dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">{{__('admin.sign_in')}}</button>
                </form>
            </div>
        </div>

    </main>


{{--    <div class="login-box">--}}
{{--        <div class="login-logo">--}}
{{--            <a href="{{route('admin.home')}}">{!! __('admin.login_title') !!}</a>--}}
{{--        </div>--}}
{{--        <!-- /.login-logo -->--}}
{{--        <div class="card">--}}
{{--            <div class="card-body login-card-body">--}}
{{--                <div class="login-box-msg">{{__('admin.login_message')}}</div>--}}

{{--                @if($session_message = session('message'))--}}
{{--                    <div class="login-box-msg text-red">--}}
{{--                        {{ $session_message }}--}}
{{--                    </div>--}}
{{--                @endif--}}

{{--                <form method="post" action="{{ route('admin.2fa') }}" target>--}}
{{--                    @csrf--}}
{{--                    <div class="input-group mb-3">--}}
{{--                        <input type="text" name="login" autofocus class="form-control @error('login') is-invalid @enderror" placeholder="{{__('admin.email')}}"/>--}}
{{--                        <div class="input-group-append">--}}
{{--                            <div class="input-group-text">--}}
{{--                                <span class="fas fa-envelope"></span>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        @error('login')--}}
{{--                            <span id="exampleInputEmail1-error" class="error invalid-feedback">{{ $message }}</span>--}}
{{--                        @enderror--}}
{{--                    </div>--}}
{{--                    <div class="input-group mb-3">--}}
{{--                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="{{__('admin.password')}}"/>--}}
{{--                        <div class="input-group-append">--}}
{{--                            <div class="input-group-text">--}}
{{--                                <span class="fas fa-lock"></span>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        @error('password')--}}
{{--                        <span id="exampleInputEmail1-error" class="error invalid-feedback">{{ $message }}</span>--}}
{{--                        @enderror--}}
{{--                    </div>--}}
{{--                    <div class="row">--}}
{{--                        <div class="col-8">--}}
{{--                            <div class="icheck-primary">--}}
{{--                                <input type="checkbox" id="remember" name="remember">--}}
{{--                                <label for="remember">--}}
{{--                                    {{__('admin.remember_me')}}--}}
{{--                                </label>--}}
{{--                                @error('remember')--}}
{{--                                <span id="exampleInputEmail1-error" class="error invalid-feedback">{{ $message }}</span>--}}
{{--                                @enderror--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <!-- /.col -->--}}
{{--                        <div class="col-4">--}}
{{--                            <button type="submit" class="btn btn-primary btn-block">{{__('admin.sign_in')}}</button>--}}
{{--                        </div>--}}
{{--                        <!-- /.col -->--}}
{{--                    </div>--}}
{{--                </form>--}}
{{--            </div>--}}
{{--            <!-- /.login-card-body -->--}}
{{--        </div>--}}
{{--    </div>--}}
@endsection
