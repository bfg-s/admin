@extends(admin_template('layouts.container-layout'))

@section('content')
    @include(admin_template('layouts.parts.container-header'))
    <div class="w-full">
        <div @class(['flex flex-wrap w-full px-3'])>
            @foreach($contents as $content)
                {!! $content !!}
            @endforeach
        </div>
    </div>
@endsection
