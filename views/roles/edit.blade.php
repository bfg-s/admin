@extends('admin.page')

@section('content')

    @card(__('lte.edit_role'))

        @include('lte::roles.form')

    @endcard

@endsection