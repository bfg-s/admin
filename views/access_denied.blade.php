@extends($default_page)

@section('content')
    @alertdanger('lte::admin.error', 'fas fa-exclamation-triangle')
        @lang('lte::admin.access_denied')
    @endalert
@endsection