@foreach(($items ?? admin_repo()->nestedCollect->where('parent_id', 0)) as $menu)

    @php
        $access = !isset($menu['roles'])  ||  lte_user()->hasRoles($menu['roles']);
        $child = admin_repo()->nestedCollect->where('parent_id', '!=', 0)->where('parent_id', $menu['id']);
        $on = !$child->count()  ||  !!$child->where('active', true)->count();
        //if(isset($menu['title']) && $menu['title'] === 'Layout') {dd($menu, $child->where('active', true));}
    @endphp

    @if($menu['active'] && $access && $on)

        @php
            $selected = $menu['selected'] || $child->where('selected', true)->count()
        @endphp

        <li class="nav-item {{ $child->count() && $selected ? 'menu-open' : '' }}">

            <a href="{{$menu['link'] && !$child->count() ? (isset($menu['link.index']) ? $menu['link.index']() : $menu['link']) : 'javascript:void(0)'}}"
               @if($menu['target']) target="_blank"
               @endif class="nav-link {{ !$child->count() ? ($selected ? 'active' : '') : ( !isset($nes) && $selected ? 'active' : '' ) }} {{ $child->count() ? 'has-treeview' : '' }}">

                @if (isset($menu['icon']))

                    <i class="nav-icon {{$menu['icon']}}"></i>

                @endif

                <p>
                    @lang($menu['title'])

                    @if (isset($menu['badge']) && is_array($menu['badge']))

                        <span
                            id="nav_badge_{{isset($menu['badge']['id']) && $menu['badge']['id'] ? $menu['badge']['id'] : $menu['id']}}"
                            class="right badge badge-{{$menu['badge']['type'] ?? 'info'}}" {!! isset($menu['badge']['title']) ? "title='{$menu['badge']['title']}'" : "" !!}>
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

                    @elseif($child->count())

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

            @if($child->count())
                <ul class="nav nav-treeview">
                    @include('lte::layout.side_bar_items', ['items' => $child, 'nes' => true])
                </ul>
            @endif
        </li>

    @elseif(isset($menu['main_header']) && $menu['main_header'])
        <li class="nav-header">@lang($menu['main_header'])</li>
    @endif
@endforeach
