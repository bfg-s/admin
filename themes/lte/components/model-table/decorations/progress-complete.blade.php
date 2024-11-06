<div class="progress progress-sm">
    <div
        class="progress-bar bg-green"
        role="progressbar"
        aria-valuenow="{{ $value }}"
        aria-valuemin="0"
        aria-valuemax="100"
        style="width: {{ $value }}%"
    ></div>
</div>

@if($text)
    <small>{{ explode('.', round($value))[0] . '% ' . $text }}</small>
@endif
