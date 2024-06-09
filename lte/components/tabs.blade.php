<div
    @class(['row'])
    @attributes($attributes)
>
    @if($left)
        @if($vertical) <div class="col-md-2"> @endif
            <ul
                @class(['nav nav-tabs', 'flex-column h-100' => $vertical, 'w-100' => ! $vertical])
                role="tablist"
                @if($vertical) aria-orientation="vertical" @endif
            >
                @foreach($tabs as $tab)
                    <li class="nav-item">
                        <a
                            href="#{{ $tab['id'] }}"
                            @class(['nav-link', 'active' => $tab['active']])
                            id="{{ $tab['id'] }}-label"
                            data-toggle="pill"
                            role="tab"

                            aria-selected="{{ $tab['active'] ? 'true' : 'false' }}"
                        >
                            @if($tab['icon'])
                                <i class="{{ $tab['icon'] }} mr-1"></i>
                            @endif
                            @lang($tab['title'])
                        </a>
                    </li>
                @endforeach
            </ul>
        @if($vertical) </div> @endif
        @if($vertical) <div class="col-md-10"> @endif
            <div @class(['tab-content', 'w-100' => ! $vertical])>
                @foreach($tabs as $tab)
                    {!! $tab['content'] !!}
                @endforeach
            </div>
        @if($vertical) </div> @endif
    @else
        @if($vertical) <div class="col-md-10"> @endif
            <div @class(['tab-content', 'w-100' => ! $vertical])>
                @foreach($tabs as $tab)
                    {!! $tab['content'] !!}
                @endforeach
            </div>
        @if($vertical) </div> @endif
        @if($vertical) <div class="col-md-2"> @endif
            <ul
                @class(['nav nav-tabs nav-tabs-right', 'flex-column h-100' => $vertical, 'w-100' => ! $vertical])
                role="tablist"
                @if($vertical) aria-orientation="vertical" @endif
            >
                @foreach($tabs as $tab)
                    <li class="nav-item">
                        <a
                            href="#{{ $tab['id'] }}"
                            @class(['nav-link', 'active' => $tab['active']])
                            id="{{ $tab['id'] }}-label"
                            data-toggle="pill"
                            role="tab"

                            aria-selected="{{ $tab['active'] ? 'true' : 'false' }}"
                        >
                            @if($tab['icon'])
                                <i class="{{ $tab['icon'] }} mr-1"></i>
                            @endif
                            @lang($tab['title'])
                        </a>
                    </li>
                @endforeach
            </ul>
        @if($vertical) </div> @endif
    @endif
</div>
