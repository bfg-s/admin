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
                @foreach($contents as $tab)
                    <li class="nav-item">
                        <a
                            href="#{{ $tab->getId() }}"
                            @class(['nav-link', 'active' => $tab->isActive()])
                            id="{{ $tab->getId() }}-label"
                            data-toggle="pill"
                            role="tab"

                            aria-selected="{{ $tab->isActive() ? 'true' : 'false' }}"
                        >
                            @if($tab->getIcon())
                                <i class="{{ $tab->getIcon() }} mr-1"></i>
                            @endif
                            @lang($tab->getTitle())
                        </a>
                    </li>
                @endforeach
            </ul>
        @if($vertical) </div> @endif
        @if($vertical) <div class="col-md-10"> @endif
            <div @class(['tab-content', 'w-100' => ! $vertical])>
                @foreach($contents as $tab)
{{--                    @foreach($tab->getContents() as $content)--}}
{{--                        {!! $content !!}--}}
{{--                    @endforeach--}}
                    @include(admin_template('components.tab-content'), array_merge([
                        'contents' => $tab->getContents(),
                        'attributes' => $tab->getAttributes(),
                    ], $tab->getViewDate()))
                @endforeach
            </div>
        @if($vertical) </div> @endif
    @else
        @if($vertical) <div class="col-md-10"> @endif
            <div @class(['tab-content', 'w-100' => ! $vertical])>
                @foreach($contents as $tab)
                    @include(admin_template('components.tab-content'), array_merge([
                        'contents' => $tab->getContents(),
                        'attributes' => $tab->getAttributes(),
                    ], $tab->getViewDate()))
                @endforeach
            </div>
        @if($vertical) </div> @endif
        @if($vertical) <div class="col-md-2"> @endif
            <ul
                @class(['nav nav-tabs nav-tabs-right', 'flex-column h-100' => $vertical, 'w-100' => ! $vertical])
                role="tablist"
                @if($vertical) aria-orientation="vertical" @endif
            >
                @foreach($contents as $tab)
                    <li class="nav-item">
                        <a
                            href="#{{ $tab->getId() }}"
                            @class(['nav-link', 'active' => $tab->isActive()])
                            id="{{ $tab->getId() }}-label"
                            data-toggle="pill"
                            role="tab"

                            aria-selected="{{ $tab->isActive() ? 'true' : 'false' }}"
                        >
                            @if($tab->getIcon())
                                <i class="{{ $tab->getIcon() }} mr-1"></i>
                            @endif
                            @lang($tab->getTitle())
                        </a>
                    </li>
                @endforeach
            </ul>
        @if($vertical) </div> @endif
    @endif
</div>
