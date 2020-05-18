@extends('admin.page')

@section('content')

    @card()
        @cardhead(__('lte.edit_function'))
            @cardheadtools()
                @buttongroup()
                    @bgroupreload()
                    @bgrouprlist()
                    @if(lte_user()->isRoot())
                        @bgrouprdestroy()
                    @endif
                    @bgroupradd()
                @endbuttongroup
            @endcardheadtools
        @endcardhead

        @include('lte::functions.form')

    @endcard

@endsection