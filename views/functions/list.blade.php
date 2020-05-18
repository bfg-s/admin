@extends('admin.page')

@section('content')

    @card(__('lte.list_of_functions'))
        @cardbodytable
            @if(!lte_user()->isRoot())
                @tableinstruction(['active' => 1])
            @endif
            @column(__('lte.role'), $roles)
            @column(__('lte.slug'), 'copied:slug', true)
            @column(__('lte.description'), 'str_limit(50):__lang:description', true)
            @if(!lte_user()->isRoot())
                @disabledelete()
            @else
                @column(__('lte.active'), 'input_switcher:active', 'active')
            @endif
            @disableinfo()
        @endcardbodytable

        @tablefooter

    @endcard

@endsection