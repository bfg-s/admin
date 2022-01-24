@extends($layout)

@section($yield)
    {!! $component !!}
    @foreach(app(\Lar\LteAdmin\Page::class)->storeList as $name => $store)
        @alpineStore($name, $store)
    @endforeach
@endsection
