<section class="content">
    <div class="error-page">
        <h2 class="headline text-warning"> 404</h2>

        <div class="error-content">
            <h3><i class="fas fa-exclamation-triangle text-warning"></i> @lang('admin.oops_page_not_found')</h3>

            <p>
                <br>
                @lang('admin.we_could_not_find_the_page', ['url' => route('admin.dashboard')])
            </p>
        </div>
        <!-- /.error-content -->
    </div>
    <!-- /.error-page -->
</section>
