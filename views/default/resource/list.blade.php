@extends('admin.page')

@section('content')

    @card(__('lte.list'))

        @cardbodytable

            @if(gets()->lte->menu->model)

                @foreach(gets()->lte->menu->model->getFillable() as $key)

                    @if(!Str::endsWith($key, '_id'))

                        @if(Str::endsWith($key, '_at'))
                            @column(Str::title($key), 'true_data:'.$key, true)
                        @else
                            @column(Str::title($key), $key, true)
                        @endif

                    @endif

                @endforeach

            @endif

        @endcardbodytable

        @tablefooter

    @endcard

@endsection