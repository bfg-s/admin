@extends('admin.page')

@section('content')

    @card(__('lte.edit_permission'))

        @include('lte::permission.form')

    @endcard

@endsection