<div
    @class(array_merge(['row'], $classes))
    @attributes($attributes)
>
    @if($left)
        <div class="col-md-2">
            <ul class="nav flex-column nav-tabs h-100" role="tablist" aria-orientation="vertical">
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
        </div>
        <div class="col-md-10">
            <div class="tab-content">
                @foreach($tabs as $tab)
                    {!! $tab['content'] !!}
                @endforeach
            </div>
        </div>
    @else
        <div class="col-md-10">
            <div class="tab-content">
                @foreach($tabs as $tab)
                    {!! $tab['content'] !!}
                @endforeach
            </div>
        </div>
        <div class="col-md-2">
            <ul class="nav flex-column nav-tabs nav-tabs-right h-100" role="tablist" aria-orientation="vertical">
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
        </div>
    @endif
</div>
