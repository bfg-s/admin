@foreach(($items ?? admin_repo()->menuList->where('parent_id', 0)) as $menu)

    @php
        $access = !$menu->getRoles() || admin_user()->hasRoles($menu->getRoles());
        $child = $menu->getChild();
        $hasChild = $child && $child->isNotEmpty();
        $hasChildSelected = $hasChild && $child->where('selected', true)->count();
        $badge = $menu->getBadge();
        $selected = $menu->isSelected() || $hasChildSelected;
    @endphp

    @if($menu->isActive() && $access)

        <li class="nav-item {{ $hasChild && $selected ? 'menu-open' : '' }}">

            <a href="{{$menu->getLink() ?: 'javascript:void(0)'}}"
               @if($menu->isTargetBlank()) target="_blank" @endif
               @if($menu->getDontUseSearch()) data-ignore="1" @endif
               @if($menu->isTarget()) target="_blank"
               @endif class="nav-link {{ !$hasChild ? ($selected ? 'active' : '') : ( !isset($nes) && $selected ? 'active' : '' ) }} {{ $hasChild ? 'has-treeview' : '' }}">

                @if ($menu->getIcon())
                    <i class="nav-icon {{$menu->getIcon()}}"></i>
                @endif

                <p>
                    @lang($menu->getTitle())

                    @if (is_array($badge))

                        <span
                                id="nav_badge_{{isset($badge['id']) && $badge['id'] ? $badge['id'] : $menu->getId()}}"
                                class="right badge badge-{{$badge['type'] ?? 'info'}}" {!! isset($badge['title']) ? "title='{$badge['title']}'" : "" !!}>
                            @if(isset($badge['instructions']) && $badge['instructions'])
                                {{eloquent_instruction($badge['text'], $badge['instructions'])->count()}}
                            @else
                                {{isset($badge['text']) ? __($badge['text']) : 0}}
                            @endif
                        </span>

                    @elseif(isset($badge))

                        <span id="nav_badge_{{$menu->getId()}}" class="right badge badge-info">
                            @lang($badge)
                        </span>

                    @elseif($hasChild)

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

            @if($hasChild)
                <ul class="nav nav-treeview">
                    @include(admin_template('layouts.parts.side-bar-items'), ['items' => $child, 'nes' => true])
                </ul>
            @endif
        </li>

    @elseif($menu->getMainHeader())
        <li class="nav-header">@lang($menu->getMainHeader())</li>
    @endif
@endforeach
