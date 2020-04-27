@extends('admin.page')

@section('content')

    @card('Редактировать ID: :id')

        @include('admin.resource.form')

    @endcard

@endsection