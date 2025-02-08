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

        <li class="{{ $hasChild && $selected ? 'menu-open' : '' }}">

            <a href="{{$menu->getLink() ?: 'javascript:void(0)'}}"
               @if($menu->isTargetBlank()) target="_blank" @endif
               @if($menu->getDontUseSearch()) data-ignore="1" @endif
               @if($menu->isTarget()) target="_blank"
               @endif
               class="
                    flex items-center p-2 text-base text-gray-900 rounded-lg hover:bg-gray-100 group dark:text-gray-200 dark:hover:bg-gray-700
                    {{ !$hasChild ? ($selected ? 'bg-gray-100 text-gray-900 dark:bg-gray-700 dark:text-white' : '') : ( !isset($nes) && $selected ? 'bg-gray-100 text-gray-900 dark:bg-gray-700 dark:text-white' : '' ) }} {{ $hasChild ? 'has-treeview' : '' }}
{{--                    {{ request()->is('dashboard') ? 'bg-gray-100 text-gray-900 dark:bg-gray-700 dark:text-white' : 'text-gray-500 dark:text-gray-400' }}--}}
               "
               @if($hasChild) aria-controls="dropdown-menu-{{ $menu->getId() }}" data-collapse-toggle="dropdown-menu-{{ $menu->getId() }}" @endif
            >

                @if ($menu->getIcon())
                    <div class="flex-shrink-0 w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white">
                        <i class="{{$menu->getIcon()}}"></i>
                    </div>
                @endif

                <span class="flex-1 ml-3 text-left whitespace-nowrap" @if ($hasChild) sidebar-toggle-item @endif>
                    @lang($menu->getTitle())
                </span>

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
                            <svg sidebar-toggle-item class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                        @endif

                    @endif
            </a>

            @if($hasChild)
                <ul id="dropdown-menu-{{ $menu->getId() }}" class="{{ $hasChild && $selected ? '' : 'hidden' }} py-2 space-y-2 ml-6">
                    @include(admin_template('layouts.parts.side-bar-items'), ['items' => $child, 'nes' => true])
                </ul>
            @endif
        </li>

    @elseif($menu->getMainHeader())
        <li class="nav-header">@lang($menu->getMainHeader())</li>
    @endif
@endforeach
