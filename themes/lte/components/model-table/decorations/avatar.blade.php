@if($value)
    <img
        src="{{ $value }}"
        data-click='fancy::img'
        data-params='{{ $value }}'
        style="width:auto;height:auto;max-width:{{ $size }}px;max-height:{{ $size }}px;cursor:pointer"
        alt='avatar'
    />
@else
    <span class="badge badge-dark">none</span>
@endif
