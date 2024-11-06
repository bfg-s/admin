<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>
                    @if($pageIcon)
                        <i class="{{ $pageIcon }}"></i>
                    @endif
                    @if($pageTitle)
                        {{ $pageTitle }}
                    @endif
                    @foreach($buttonGroups as $buttonGroup)
                        {!! $buttonGroup !!}
                    @endforeach
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    @foreach($breadcrumbs as $breadcrumb)
                        <li class="breadcrumb-item {{$loop->last ? '' : 'active'}}">
                            @if($breadcrumb['url'])
                                <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a>
                            @else
                                {{ $breadcrumb['title'] }}
                            @endif
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>
