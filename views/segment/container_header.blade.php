<!-- Content Header (Page header) -->
@php
    $menu = gets()->lte->menu->now;
    $__head_title = ['LteAdmin'];
@endphp

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>
                    @if(isset($page_info))
                        @if(is_array($page_info))
                            @if(isset($page_info['icon'])) <i class="{{$page_info['icon']}}"></i> @elseif(isset($menu['icon'])) <i class="{{$menu['icon']}}"></i>  @endif {{__($page_info['head_title'] ?? ($page_info['title'] ?? 'Blank page'))}}
                        @else
                            @if(isset($menu['icon'])) <i class="{{$menu['icon']}}"></i>  @endif {{__($page_info)}}
                        @endif
                    @else
                        @if(isset($menu['icon'])) <i class="{{$menu['icon']}}"></i> @endif {{__($menu['head_title'] ?? ($menu['title'] ?? 'Blank page'))}}
                    @endif
                </h1>
            </div>
            @php($first = gets()->lte->menu->nested_collect->first())
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    @if (gets()->lte->menu->now_parents->count() && $first['id'] !== $menu['id'])
                        <li class="breadcrumb-item active">
                            {{__($first['title'])}}
                        </li>
                        @foreach(gets()->lte->menu->now_parents->reverse() as $item)
                            <li class="breadcrumb-item {{$loop->last ? '' : 'active'}}">
                                {{__($item['title'])}}
                                @php($__head_title[] = __($item['title']))
                            </li>
                        @endforeach
                    @endif
                    @if (isset($breadcrumb) && is_array($breadcrumb))
                        @foreach($breadcrumb as $item)
                            <li class="breadcrumb-item {{$loop->last ? '' : 'active'}}">
                                {{__($item)}}
                                @php($__head_title[] = __($item))
                            </li>
                        @endforeach
                    @endif
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

@php(LJS::respond()->title(implode(' | ', $__head_title)))
