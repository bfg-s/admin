@extends('admin.page')

@section('content')

    @card(__('lte.id_information'))

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