<section class="pt-7 px-7">
    <div class="flex items-center justify-between pb-1 mb-1">
        <div class="flex">
            <h3 class="text-xl font-normal text-gray-500 dark:text-gray-400">
                @if($pageIcon)
                    <i class="{{ $pageIcon }} mr-2"></i>
                @endif
                @if($pageTitle)
                    {{ $pageTitle }}
                @endif
            </h3>
            @if($buttonGroups)
                <div class="ml-3 text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white">
                    @foreach($buttonGroups as $buttonGroup)
                        {!! $buttonGroup !!}
                    @endforeach
                </div>
            @endif
        </div>
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 text-sm font-medium md:space-x-2">
                @foreach($breadcrumbs as $breadcrumb)
                    <li class="inline-flex items-center text-gray-700 hover:text-primary-600 dark:text-gray-300 dark:hover:text-white">
                        @if($loop->first)
                            <svg class="w-5 h-5 mr-2.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                        @else
                            <svg class="w-6 h-6 mr-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        @endif
                        @if($breadcrumb['url'])
                            <a href="{{ $breadcrumb['url'] }}" class="inline-flex items-center text-gray-700 hover:text-primary-600 dark:text-gray-300 dark:hover:text-white">
                                {{ $breadcrumb['title'] }}
                            </a>
                        @else
                            <span class="inline-flex items-center text-gray-700 hover:text-primary-600 dark:text-gray-300 dark:hover:text-white">
                                    {{ $breadcrumb['title'] }}
                                </span>
                        @endif
                    </li>
                @endforeach
            </ol>
        </nav>
{{--        <div>--}}
{{--            <ol class="flex items-center text-sm text-gray-500 dark:text-gray-400">--}}
{{--                @foreach($breadcrumbs as $breadcrumb)--}}
{{--                    <li class="flex items-center">--}}
{{--                        @if($breadcrumb['url'])--}}
{{--                            <a href="{{ $breadcrumb['url'] }}" class="hover:text-gray-900 dark:hover:text-white">{{ $breadcrumb['title'] }}</a>--}}
{{--                        @else--}}
{{--                            <span>{{ $breadcrumb['title'] }}</span>--}}
{{--                        @endif--}}
{{--                        @if(!$loop->last)--}}
{{--                            <i class="fas fa-angle-right mx-2"></i>--}}
{{--                        @endif--}}
{{--                    </li>--}}
{{--                @endforeach--}}
{{--            </ol>--}}
{{--        </div>--}}
    </div>
</section>


{{--<!-- Content Header (Page header) -->--}}
{{--<section class="content-header">--}}
{{--    <div class="container-fluid">--}}
{{--        <div class="row mb-2">--}}
{{--            <div class="col-sm-6">--}}
{{--                <h1>--}}
{{--                    @if($pageIcon)--}}
{{--                        <i class="{{ $pageIcon }}"></i>--}}
{{--                    @endif--}}
{{--                    @if($pageTitle)--}}
{{--                        {{ $pageTitle }}--}}
{{--                    @endif--}}
{{--                    @foreach($buttonGroups as $buttonGroup)--}}
{{--                        {!! $buttonGroup !!}--}}
{{--                    @endforeach--}}
{{--                </h1>--}}
{{--            </div>--}}
{{--            <div class="col-sm-6">--}}
{{--                <ol class="breadcrumb float-sm-right">--}}
{{--                    @foreach($breadcrumbs as $breadcrumb)--}}
{{--                        <li class="breadcrumb-item {{$loop->last ? '' : 'active'}}">--}}
{{--                            @if($breadcrumb['url'])--}}
{{--                                <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a>--}}
{{--                            @else--}}
{{--                                {{ $breadcrumb['title'] }}--}}
{{--                            @endif--}}
{{--                        </li>--}}
{{--                    @endforeach--}}
{{--                </ol>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div><!-- /.container-fluid -->--}}
{{--</section>--}}
