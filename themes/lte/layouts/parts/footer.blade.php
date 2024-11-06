<footer class="main-footer text-sm">
    <div class="float-right d-none d-sm-block">
        <b>Version</b> {{Admin::version()}}
    </div>
    <div class="mr-3 float-right d-sm-block">
        <a href="{{ route('admin.update') }}">Update</a>
    </div>
    {!! config('admin.footer.copy') !!}
</footer>
