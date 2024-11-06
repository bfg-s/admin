@if(! $value)
    <span class="badge badge-dark">NULL</span>
@else
    {!! $value !!}
    <a href="{{ $link }}" @if($title) title="{{ $title }}" @endif>
        <i class="{{ $icon }}"></i>
    </a>
@endif
