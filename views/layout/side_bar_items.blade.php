@foreach(($items ?? gets()->lte->menu->nested_collect->where('parent_id', 0)) as $menu)

    @php
        $access = isset($menu['roles']) ? lte_user()->hasRoles($menu['roles']) : true;
        $childs = gets()->lte->menu->nested_collect->where('parent_id', '!=', 0)->where('parent_id', $menu['id']);
        $on = $childs->count() ? !!$childs->where('active', true)->count() : true;
        //if(isset($menu['title']) && $menu['title'] === 'Layout') {dd($menu, $childs->where('active', true));}
    @endphp

    @if($menu['active'] && $access && $on)

        @php
            $selected = $menu['selected'] || $childs->where('selected', true)->count()
        @endphp

        <li class="nav-item {{ $childs->count() && $selected ? 'menu-open' : '' }}">

            <a href="{{$menu['link'] && !$childs->count() ? (isset($menu['link.index']) ? $menu['link.index']() : $menu['link']) : 'javascript:void(0)'}}"
               @if($menu['target']) target="_blank"
               @endif class="nav-link {{ !$childs->count() ? ($selected ? 'active' : '') : ( !isset($nes) && $selected ? 'active' : '' ) }} {{ $childs->count() ? 'has-treeview' : '' }}">

                @if (isset($menu['icon']))

                    <i class="nav-icon {{$menu['icon']}}"></i>

                @endif

                <p>
                    @lang($menu['title'])

                    @if (isset($menu['badge']) && is_array($menu['badge']))

                        <span
                            id="nav_badge_{{isset($menu['badge']['id']) && $menu['badge']['id'] ? $menu['badge']['id'] : $menu['id']}}"
                            class="right badge badge-{{isset($menu['badge']['type']) ? $menu['badge']['type'] : 'info'}}" {!! isset($menu['badge']['title']) ? "title='{$menu['badge']['title']}'" : "" !!}>
                            @if(isset($menu['badge']['instructions']) && $menu['badge']['instructions'])
                                {{eloquent_instruction($menu['badge']['text'], $menu['badge']['instructions'])->count()}}
                            @else
                                {{isset($menu['badge']['text']) ? __($menu['badge']['text']) : 0}}
                            @endif
                        </span>

                    @elseif(isset($menu['badge']))

                        <span id="nav_badge_{{$menu['id']}}" class="right badge badge-info">
                            @lang($menu['badge'])
                        </span>

                    @elseif($childs->count())

                        @php
                            $with_badges = 0
                        @endphp

                        @if($with_badges)
                            <span class="right badge badge-info">
                                {{$with_badges}}
                            </span>
                        @else
                            <i class="right fas fa-angle-left"></i>
                        @endif

                    @endif
                </p>
            </a>

            @if($childs->count())
                <ul class="nav nav-treeview">
                    @include('lte::layout.side_bar_items', ['items' => $childs, 'nes' => true])
                </ul>
            @endif
        </li>

    @elseif(isset($menu['main_header']) && $menu['main_header'])
        <li class="nav-header">@lang($menu['main_header'])</li>
    @endif
@endforeach
