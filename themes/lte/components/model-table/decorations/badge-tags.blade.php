<span class="badge badge-info">
    {!! $collect->take($limit)->implode('</span> <span class="badge badge-info">') !!}
</span>

@if($collect->count() > $limit)
    ... <span class="badge badge-warning" title="{{ $collect->skip($limit)->implode(', ') }}">{{ $collect->count() - $limit }}x</span>
@endif
