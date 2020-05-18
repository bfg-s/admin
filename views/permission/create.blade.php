@extends('admin.page')

@section('content')

    @card(__('lte.permissions'))

        @include('lte::permission.form')

    @endcard

@endsection