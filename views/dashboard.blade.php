@extends($default_page)

@section('content')
    <div class="row">
        <div class="col-md-6">

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{__('lte::dashboard.environment')}}</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body p-0">
                    <table class="table table-sm">
                        <tbody class="d-none d-lg-block d-xl-block">
                            @foreach($environment as $title => $value)
                                <tr>
                                    @if(!is_array($value))
                                        <td>{{$title}}</td>
                                        <td width="50%">{!! $value !!}</td>
                                    @else
                                        <td><h4>{!! $value[0] !!}</h4></td>
                                        <td></td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                        <tbody class="d-lg-none d-xl-none">
                            @foreach($environment as $title => $value)
                                @if(!is_array($value))
                                    <tr><td><strong>{{$title}}:</strong></td></tr>
                                    <tr><td>{!! $value !!}</td></tr>
                                @else
                                    <tr><td><h4>{!! $value[0] !!}</h4></td></tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Composer</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body p-0">
                    <table class="table table-sm">
                        <tbody>
                        @foreach($composer as $title => $value)
                            <tr>
                                @if(!is_array($value))
                                    <td>{{$title}}</td>
                                    <td width="50%">{!! $value !!}</td>
                                @else
                                    <td><h4>{!! $value[0] !!}</h4></td>
                                    <td></td>
                                @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
        </div>

        <div class="col-md-6">

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Laravel</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body p-0">
                    <table class="table table-sm">
                        <tbody>
                            @foreach($laravel as $title => $value)
                                <tr>
                                    @if(!is_array($value))
                                        <td>{{$title}}</td>
                                        <td width="50%">{!! $value !!}</td>
                                    @else
                                        <td><h4>{!! $value[0] !!}</h4></td>
                                        <td></td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Database</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body p-0">
                    <table class="table table-sm">
                        <tbody class="d-none d-lg-block d-xl-block">
                            @foreach($database as $title => $value)
                                <tr>
                                    @if(!is_array($value))
                                        <td>{{$title}}</td>
                                        <td width="50%">{!! $value !!}</td>
                                    @else
                                        <td><h4>{!! $value[0] !!}</h4></td>
                                        <td></td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                        <tbody class="d-lg-none d-xl-none">
                            @foreach($database as $title => $value)
                                @if(!is_array($value))
                                    <tr><td><strong>{{$title}}:</strong></td></tr>
                                    <tr><td>{!! $value !!}</td></tr>
                                @else
                                    <tr><td><h4>{!! $value[0] !!}</h4></td></tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
        </div>

    </div>
@endsection