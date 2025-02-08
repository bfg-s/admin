<div class="">
    <div class="grid grid-cols-1 xl:!grid-cols-3 gap-4">
        <!-- Первая колонка (кнопки пагинации) -->
        <div class="hidden xl:block">
            <div class="flex space-x-2">
                @foreach($per_pages as $per)
                    <a href="{{ admin_url_with_get([$per_name => $per], [$page_name]) }}"
                       class="px-3 py-2 text-sm {{ $per == $per_page ? 'bg-gray-800 text-white' : 'bg-gray-200 text-gray-800' }} rounded-md hover:bg-gray-300">
                        {{$per}}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Вторая колонка (информация о текущей странице) -->
        <div class="text-center">
            <div class="text-sm text-gray-700 dark:text-gray-400">
                {{ __('admin.showing_to_of_entries', ['show' => $from, 'to' => $to, 'of' => $total]) }}
            </div>
        </div>

        <!-- Третья колонка (кнопки для навигации по страницам) -->
        <div class="hidden xl:block">
            @if ($hasPages)
                <nav>
                    <ul class="flex space-x-2 justify-end">
                        <!-- Previous Page Link -->
                        @if ($onFirstPage)
                            <li class="disabled">
                                <span class="px-3 py-2 bg-gray-200 text-gray-500 cursor-not-allowed rounded-md">&lsaquo;</span>
                            </li>
                        @else
                            <li>
                                <a class="px-3 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700"
                                   href="{{ admin_url_with_get([$page_name => $currentPage - 1]) }}" rel="prev" aria-label="@lang('pagination.previous')">&lsaquo;</a>
                            </li>
                        @endif

                        <!-- Pagination Elements -->
                        @foreach ($elements as $element)
                            @if (is_string($element))
                                <li class="disabled">
                                    <span class="px-3 py-2 bg-gray-200 text-gray-500 cursor-not-allowed">{{ $element }}</span>
                                </li>
                            @endif

                            @if (is_array($element))
                                @foreach ($element as $page => $url)
                                    @if ($page == $currentPage)
                                        <li>
                                            <span class="px-3 py-2 bg-gray-600 text-white rounded-md">{{ $page }}</span>
                                        </li>
                                    @else
                                        <li>
                                            <a class="px-3 py-2 text-gray-600 rounded-md hover:bg-gray-100" href="{{ admin_url_with_get([$page_name => $page]) }}">{{ $page }}</a>
                                        </li>
                                    @endif
                                @endforeach
                            @endif
                        @endforeach

                        <!-- Next Page Link -->
                        @if ($hasMorePages)
                            <li>
                                <a class="px-3 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700"
                                   href="{{ admin_url_with_get([$page_name => $currentPage + 1]) }}" rel="next" aria-label="@lang('pagination.next')">&rsaquo;</a>
                            </li>
                        @else
                            <li class="disabled">
                                <span class="px-3 py-2 bg-gray-200 text-gray-500 rounded-md cursor-not-allowed">&rsaquo;</span>
                            </li>
                        @endif
                    </ul>
                </nav>
            @endif
        </div>
    </div>
</div>

<!-- Мобильная версия -->
<div class="xl:hidden py-4 px-6 mt-4">
    @if ($hasPages)
        <nav>
            <ul class="flex space-x-2 justify-center">
                <!-- Previous Page Link -->
                @if ($onFirstPage)
                    <li class="disabled">
                        <span class="px-3 py-2 bg-gray-200 text-gray-500 cursor-not-allowed">@lang('pagination.previous')</span>
                    </li>
                @else
                    <li>
                        <a class="px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                           href="{{ admin_url_with_get([$page_name => $currentPage - 1]) }}" rel="prev">@lang('pagination.previous')</a>
                    </li>
                @endif

                <!-- Next Page Link -->
                @if ($hasMorePages)
                    <li>
                        <a class="px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                           href="{{ admin_url_with_get([$page_name => $currentPage + 1]) }}" rel="next">@lang('pagination.next')</a>
                    </li>
                @else
                    <li class="disabled">
                        <span class="px-3 py-2 bg-gray-200 text-gray-500 cursor-not-allowed">@lang('pagination.next')</span>
                    </li>
                @endif
            </ul>
        </nav>
    @endif
</div>


{{--<div class="card-footer">--}}
{{--    <div class="row">--}}
{{--        <div class="col-sm d-none d-lg-block d-xl-block">--}}
{{--            <div class="btn-group justify-content-start" role="group">--}}
{{--                @foreach($per_pages as $per)--}}
{{--                    <a href="{{admin_url_with_get([$per_name => $per], [$page_name])}}"--}}
{{--                       class="btn btn-sm btn-{{$per == $per_page ? 'dark' : 'secondary'}}">{{$per}}</a>--}}
{{--                @endforeach--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        <div class="col-sm">--}}
{{--            <div--}}
{{--                    style="text-align: center">{{__('admin.showing_to_of_entries', ['show' => $from, 'to' => $to, 'of' => $total])}}</div>--}}
{{--        </div>--}}
{{--        <div class="col-sm d-none d-lg-block d-xl-block">--}}
{{--            @if ($hasPages)--}}
{{--                <nav>--}}
{{--                    <ul class="pagination justify-content-end pagination-sm mb-0">--}}
{{--                        --}}{{-- Previous Page Link --}}
{{--                        @if ($onFirstPage)--}}
{{--                            <li class="page-item disabled" aria-disabled="true"--}}
{{--                                aria-label="@lang('pagination.previous')">--}}
{{--                                <span class="page-link" aria-hidden="true">&lsaquo;</span>--}}
{{--                            </li>--}}
{{--                        @else--}}
{{--                            <li class="page-item">--}}
{{--                                <a class="page-link"--}}
{{--                                   href="{{ admin_url_with_get([$page_name => $currentPage - 1]) }}"--}}
{{--                                   rel="prev" aria-label="@lang('pagination.previous')">&lsaquo;</a>--}}
{{--                            </li>--}}
{{--                        @endif--}}

{{--                        --}}{{-- Pagination Elements --}}
{{--                        @foreach ($elements as $element)--}}
{{--                            --}}{{-- "Three Dots" Separator --}}
{{--                            @if (is_string($element))--}}
{{--                                <li class="page-item disabled" aria-disabled="true"><span--}}
{{--                                            class="page-link">{{ $element }}</span></li>--}}
{{--                            @endif--}}

{{--                            --}}{{-- Array Of Links --}}
{{--                            @if (is_array($element))--}}
{{--                                @foreach ($element as $page => $url)--}}
{{--                                    @if ($page == $currentPage)--}}
{{--                                        <li class="page-item active" aria-current="page"><span--}}
{{--                                                    class="page-link">{{ $page }}</span></li>--}}
{{--                                    @else--}}
{{--                                        <li class="page-item"><a class="page-link"--}}
{{--                                                                 href="{{ admin_url_with_get([$page_name => $page]) }}">{{ $page }}</a>--}}
{{--                                        </li>--}}
{{--                                    @endif--}}
{{--                                @endforeach--}}
{{--                            @endif--}}
{{--                        @endforeach--}}

{{--                        --}}{{-- Next Page Link --}}
{{--                        @if ($hasMorePages)--}}
{{--                            <li class="page-item">--}}
{{--                                <a class="page-link"--}}
{{--                                   href="{{ admin_url_with_get([$page_name => $currentPage + 1]) }}"--}}
{{--                                   rel="next" aria-label="@lang('pagination.next')">&rsaquo;</a>--}}
{{--                            </li>--}}
{{--                        @else--}}
{{--                            <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">--}}
{{--                                <span class="page-link" aria-hidden="true">&rsaquo;</span>--}}
{{--                            </li>--}}
{{--                        @endif--}}
{{--                    </ul>--}}
{{--                </nav>--}}
{{--            @endif--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
{{--<div class="row d-lg-none d-xl-none">--}}
{{--    <div class="col-sm">--}}
{{--        @if ($hasPages)--}}
{{--            <nav>--}}
{{--                <ul class="pagination justify-content-center pagination-sm">--}}
{{--                    --}}{{-- Previous Page Link --}}
{{--                    @if ($onFirstPage)--}}
{{--                        <li class="page-item disabled" aria-disabled="true">--}}
{{--                            <span class="page-link">@lang('pagination.previous')</span>--}}
{{--                        </li>--}}
{{--                    @else--}}
{{--                        <li class="page-item">--}}
{{--                            <a class="page-link"--}}
{{--                               href="{{ admin_url_with_get([$page_name => $currentPage - 1]) }}"--}}
{{--                               rel="prev">@lang('pagination.previous')</a>--}}
{{--                        </li>--}}
{{--                    @endif--}}

{{--                    --}}{{-- Next Page Link --}}
{{--                    @if ($hasMorePages)--}}
{{--                        <li class="page-item">--}}
{{--                            <a class="page-link"--}}
{{--                               href="{{ admin_url_with_get([$page_name => $currentPage + 1]) }}"--}}
{{--                               rel="next">@lang('pagination.next')</a>--}}
{{--                        </li>--}}
{{--                    @else--}}
{{--                        <li class="page-item disabled" aria-disabled="true">--}}
{{--                            <span class="page-link">@lang('pagination.next')</span>--}}
{{--                        </li>--}}
{{--                    @endif--}}
{{--                </ul>--}}
{{--            </nav>--}}
{{--        @endif--}}
{{--    </div>--}}
{{--</div>--}}
