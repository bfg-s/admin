@extends($layout)

@section($yield)
    {!! $component !!}
    @foreach(app(\Admin\Page::class)->storeList as $name => $store)
        @alpineStore($name, $store)
    @endforeach
@endsection
