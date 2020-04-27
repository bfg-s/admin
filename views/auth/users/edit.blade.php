@extends('admin.page')

@section('content')

    @card('Редактировать: :login')

        @include('lte::auth.users.form')

    @endcard

@endsection