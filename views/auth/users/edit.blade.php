@extends('admin.page')

@section('content')

    @card()
        @cardhead(__('lte.edit_admin'))
            @cardheadtools()
                @buttongroup()
                    @bgroupreload()
                    @bgrouprlist()
                    @bgrouprinfo()
                    @if(lte_model()->id !== 1)
                        @bgrouprdestroy()
                    @endif
                    @bgroupradd()
                @endbuttongroup
            @endcardheadtools
        @endcardhead

        @include('lte::auth.users.form')

    @endcard

@endsection