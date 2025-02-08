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
                    {{__('admin.2fa_auth_msg')}}
                </h2>

                @if($session_message = session('message'))
                    @include(admin_template('parts.alerts.error'), ['title' => $session_message])
                @endif

                <form class="mt-8 space-y-6" method="post" action="{{route('admin.2fa.post')}}" target>
                    @csrf
                    <input type="hidden" name="login" value="{{ $login }}"/>
                    <input type="hidden" name="password" value="{{ $password }}"/>
                    <input type="hidden" name="remember" value="{{ $remember }}"/>
                    <div>
                        <label for="code" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{__('admin.code')}}</label>
                        <input type="text" name="code" id="code" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="000000" required>
                        @error('code')
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
{{--                <p class="login-box-msg">{{ __('admin.2fa_auth_msg') }}</p>--}}

{{--                <form action="{{route('admin.2fa.post')}}" method="post" target>--}}
{{--                    @csrf--}}
{{--                    <div class="input-group mb-3">--}}
{{--                        <input type="text" name="code" autofocus class="form-control" placeholder="{{__('admin.code')}}"/>--}}
{{--                        <input type="hidden" name="login" value="{{ $login }}"/>--}}
{{--                        <input type="hidden" name="password" value="{{ $password }}"/>--}}
{{--                        <input type="hidden" name="remember" value="{{ $remember }}"/>--}}
{{--                        <div class="input-group-append">--}}
{{--                            <div class="input-group-text">--}}
{{--                                <span class="fas fa-key"></span>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="row">--}}
{{--                        <div class="col-8">--}}

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
