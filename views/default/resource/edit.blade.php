@extends('admin.page')

@section('content')

    @card(__('lte.id_edit'))

        @include('admin.resource.form')

    @endcard

@endsection