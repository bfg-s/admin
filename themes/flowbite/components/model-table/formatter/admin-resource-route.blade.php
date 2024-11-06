@if($urlEdit)
    <a class="ml-1 link text-sm" href="{{ $urlEdit }}">
        <i class="fas fa-pen" style="font-size: 12px;"></i>
    </a>
@endif

{!! $value !!}

@if($urlShow)
    <a class="ml-1 link text-sm" href="{{ $urlShow }}">
        <i class="fas fa-info-circle" style="font-size: 12px;"></i>
    </a>
@endif

@if($urlIndex)
    <a class="ml-1 link text-sm" href="{{ $urlIndex }}">
        <i class="fas fa-list-alt" style="font-size: 12px;"></i>
    </a>
@endif
