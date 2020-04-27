<ul class="nav nav-pills mr-2">
    @foreach($tabs as $tab)
        <li class="nav-item">
            <a class="nav-link {{ (request()->has($p_id) ? request()->get($p_id) == $tab['mode']->name : $loop->first) ? 'active' : ''}}" href="#{{$tab['name']}}" data-toggle="tab">
                @if(isset($tab['icon']) && $tab['icon'])
                    <i class="{{$tab['icon']}}"></i>
                @endif
                {{$tab['title']}}
            </a>
        </li>
    @endforeach
</ul>
