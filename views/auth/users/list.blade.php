@extends('admin.page')

@section('content')

    @card('Список администраторов')

        @cardbodytable

            @column('Avatar', 'avatar')
            @column('Login', 'login', true)
            @column('Name', 'name', true)

        @endcardbodytable

        @tablefooter

    @endcard

@endsection