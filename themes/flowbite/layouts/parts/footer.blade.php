{{--<footer class="main-footer text-sm">--}}
{{--    <div class="float-right d-none d-sm-block">--}}
{{--        <b>Version</b> {{Admin::version()}}--}}
{{--    </div>--}}
{{--    <div class="mr-3 float-right d-sm-block">--}}
{{--        <a href="{{ route('admin.update') }}">Update</a>--}}
{{--    </div>--}}
{{--    {!! config('admin.footer.copy') !!}--}}
{{--</footer>--}}

<footer class="bg-gray-100 dark:bg-gray-800 py-4 mt-5">
    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 flex flex-col md:flex-row items-center md:items-start justify-center md:justify-between space-y-4 md:space-y-0">

        <p class="text-sm text-gray-600 dark:text-gray-400 text-center md:text-left">
            {!! config('admin.footer.copy') !!}
        </p>

        <div class="text-xs text-gray-500 dark:text-gray-400 text-center md:text-right">
            <b class="font-semibold text-gray-700 dark:text-gray-300">Version:</b> {{ Admin::version() }}
        </div>

        <div class="text-xs text-center md:text-right">
            <a href="{{ route('admin.update') }}"
               class="text-primary-600 dark:text-primary-400 hover:underline font-medium">
                Update
            </a>
        </div>
    </div>
</footer>

