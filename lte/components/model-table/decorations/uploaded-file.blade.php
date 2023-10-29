@if($value)
    <span class="badge badge-info" title='{{ $value }}'>{{ basename($value) }}</span>
@else
    <span class="badge badge-dark">none</span>
@endif
