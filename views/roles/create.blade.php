@extends('admin.page')

@section('content')

    @card(__('lte::admin.add_role'))

        @include('lte::roles.form')

    @endcard

@endsection