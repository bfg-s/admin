@extends('admin.page')

@section('content')

    @card('Добавить администратора')

        @include('lte::auth.users.form')

    @endcard

@endsection