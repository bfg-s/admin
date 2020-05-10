@extends('admin.page')

@section('content')

    @card()
        @cardhead(__('lte::admin.id_information'))
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

        @cardbody(['p-0'])

            <table class="table">

                <tbody>

                @if(gets()->lte->menu->model)

                    @foreach(gets()->lte->menu->model->toArray() as $key => $value)

                        <tr>
                            <th scope="row">{{Str::title(str_replace('_', ' ', $key))}}</th>
                            <td>{{is_array($value) ? json_encode($value) : $value}}</td>
                        </tr>

                    @endforeach

                @endif

                </tbody>

            </table>

        @endcardbody

    @endcard

@endsection