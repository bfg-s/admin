@extends(admin_template('layouts.container-layout'))

@section('content')
    @include(admin_template('layouts.parts.container-header'))
    <div class="container-fluid">
        <div @class(['row', 'pl-3 pr-3'])>
            @foreach($contents as $content)
                {!! $content !!}
            @endforeach
        </div>
    </div>
@endsection
