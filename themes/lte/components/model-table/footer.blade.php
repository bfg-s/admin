<div class="card-footer" id="card-footer-{{ $model_name }}" @realtime('card-footer-' . $model_name)>
    <div class="row">
        <div class="col-sm d-none d-lg-block d-xl-block">
            <div class="btn-group justify-content-start" role="group">
                @foreach($per_pages as $per)
                    <a href="{{admin_url_with_get([$per_name => $per], [$page_name])}}"
                       class="btn btn-sm btn-{{$per == $per_page ? 'dark' : 'secondary'}}">{{$per}}</a>
                @endforeach
            </div>
        </div>
        <div class="col-sm">
            <div
                    style="text-align: center">{{__('admin.showing_to_of_entries', ['show' => $from, 'to' => $to, 'of' => $total])}}</div>
        </div>
        <div class="col-sm d-none d-lg-block d-xl-block">
            @if ($hasPages)
                <nav>
                    <ul class="pagination justify-content-end pagination-sm mb-0">
                        {{-- Previous Page Link --}}
                        @if ($onFirstPage)
                            <li class="page-item disabled" aria-disabled="true"
                                aria-label="@lang('pagination.previous')">
                                <span class="page-link" aria-hidden="true">&lsaquo;</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link"
                                   href="{{ admin_url_with_get([$page_name => $currentPage - 1]) }}"
                                   rel="prev" aria-label="@lang('pagination.previous')">&lsaquo;</a>
                            </li>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach ($elements as $element)
                            {{-- "Three Dots" Separator --}}
                            @if (is_string($element))
                                <li class="page-item disabled" aria-disabled="true"><span
                                            class="page-link">{{ $element }}</span></li>
                            @endif

                            {{-- Array Of Links --}}
                            @if (is_array($element))
                                @foreach ($element as $page => $url)
                                    @if ($page == $currentPage)
                                        <li class="page-item active" aria-current="page"><span
                                                    class="page-link">{{ $page }}</span></li>
                                    @else
                                        <li class="page-item"><a class="page-link"
                                                                 href="{{ admin_url_with_get([$page_name => $page]) }}">{{ $page }}</a>
                                        </li>
                                    @endif
                                @endforeach
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if ($hasMorePages)
                            <li class="page-item">
                                <a class="page-link"
                                   href="{{ admin_url_with_get([$page_name => $currentPage + 1]) }}"
                                   rel="next" aria-label="@lang('pagination.next')">&rsaquo;</a>
                            </li>
                        @else
                            <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                                <span class="page-link" aria-hidden="true">&rsaquo;</span>
                            </li>
                        @endif
                    </ul>
                </nav>
            @endif
        </div>
    </div>
</div>
<div class="row d-lg-none d-xl-none">
    <div class="col-sm">
        @if ($hasPages)
            <nav>
                <ul class="pagination justify-content-center pagination-sm">
                    {{-- Previous Page Link --}}
                    @if ($onFirstPage)
                        <li class="page-item disabled" aria-disabled="true">
                            <span class="page-link">@lang('pagination.previous')</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link"
                               href="{{ admin_url_with_get([$page_name => $currentPage - 1]) }}"
                               rel="prev">@lang('pagination.previous')</a>
                        </li>
                    @endif

                    {{-- Next Page Link --}}
                    @if ($hasMorePages)
                        <li class="page-item">
                            <a class="page-link"
                               href="{{ admin_url_with_get([$page_name => $currentPage + 1]) }}"
                               rel="next">@lang('pagination.next')</a>
                        </li>
                    @else
                        <li class="page-item disabled" aria-disabled="true">
                            <span class="page-link">@lang('pagination.next')</span>
                        </li>
                    @endif
                </ul>
            </nav>
        @endif
    </div>
</div>
