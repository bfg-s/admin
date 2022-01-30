@extends($layout)

@section($yield)
    {!! $component !!}
    @foreach(app(\LteAdmin\Page::class)->storeList as $name => $store)
        @alpineStore($name, $store)
    @endforeach
@endsection
