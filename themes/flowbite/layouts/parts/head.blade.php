<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
@adminSystemMetas()
<link rel="icon" type="image/png" href="{{ asset('admin/img/favicon.png') }}" />
<link rel="apple-touch-icon" type="image/png" href="{{ asset('admin/img/favicon.png') }}" />
<title>{{ $title ?? 'Bfg Admin' }}</title>
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="theme-color" content="#ffffff">
<script>

    if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark')
    }
</script>
