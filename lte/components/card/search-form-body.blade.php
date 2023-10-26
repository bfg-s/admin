<div class="table_search_form collapse">
    <div class="card-body">
        {!! $content !!}
    </div>
</div>
@if ($hasQ && $searchInfo)
    <div class="table_search_form collapse show">
        <div class="card-body p-0">
            {!! $searchInfo !!}
        </div>
    </div>
@endif
