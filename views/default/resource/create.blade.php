@extends('admin.page')

@section('content')

    @card(__('lte.add'))

        @include('admin.resource.form')

    @endcard

@endsection