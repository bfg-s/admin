@extends($default_page)

@section('content')
    @alertdanger('lte.error', 'fas fa-exclamation-triangle')
        @lang('lte.access_denied')
    @endalert
@endsection