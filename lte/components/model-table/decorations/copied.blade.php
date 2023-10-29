@if ($value_before)
    {!! $value !!}
@endif
<a
    href='javascript:void(0)'
    class='d-none d-sm-inline'
    title='Copy to clipboard'
    data-click='doc::informed_pbcopy'
    data-params='{!! strip_tags(html_entity_decode($value_for_copy)) !!}'
><i class='fas fa-copy'></i></a>
@if (! $value_before)
    {!! $value !!}
@endif
