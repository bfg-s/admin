<!DOCTYPE html>
<html lang="en">
<head>
    <title>BFG Admin | 404</title>

    <!-- Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="description" content="Portal - Bootstrap 5 Admin Dashboard Template For Developers">
    <meta name="author" content="Xiaoying Riley at 3rd Wave Media">
    <link rel="shortcut icon" href="{{admin_asset('images/bfg-logo.png')}}">

    <!-- FontAwesome JS-->
    <script defer src="{{admin_asset('theme/default/plugins/fontawesome/js/all.min.js')}}"></script>

    <!-- App CSS -->
    <link id="theme-style" rel="stylesheet" href="{{admin_asset('theme/default/theme.css')}}">

</head>

<body class="app app-404-page">

<div class="container mb-5">
    <div class="row">
        <div class="col-12 col-md-11 col-lg-7 col-xl-6 mx-auto">
            <div class="app-card p-5 text-center shadow-sm">
                <h1 class="page-title mb-4">404<br><span class="font-weight-light">Page Not Found</span></h1>
                <div class="mb-4">
                    Sorry, we can't find the page you're looking for.
                </div>
                <a class="btn app-btn-primary" href="{{route('admin.home')}}">Go to admin</a>
            </div>
        </div><!--//col-->
    </div><!--//row-->
</div><!--//container-->


<footer class="app-footer">
    <div class="container text-center py-3">
        <small class="copyright">{!! config('admin-ui.footer.copy') !!}</small>
    </div>
</footer><!--//app-footer-->

<!-- Javascript -->
<script src="{{admin_asset('theme/default/plugins/bootstrap/js/bootstrap.min.js')}}"></script>

</body>
</html>
