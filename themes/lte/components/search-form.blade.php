<form method="get" action="{{ $action }}" @attributes($attributes)>
    @foreach($chunks as $chunk)
        <div class="row">
            @foreach($chunk as $field)
                <div class="pl-0 col-md pl-3 pr-3">{!! $field['class'] !!}</div>
            @endforeach
        </div>
    @endforeach
    <div class="text-right">
        {!! $group !!}
    </div>
</form>
