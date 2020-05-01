@extends('admin.page')

@section('content')

    @card(__('lte::admin.add'))

        @include('admin.resource.form')

    @endcard

@endsection