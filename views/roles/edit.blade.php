@extends('admin.page')

@section('content')

    @card(__('lte::admin.edit_role'))

        @include('lte::roles.form')

    @endcard

@endsection