<ul class="navbar-nav" x-data="liveReloader">
    <li :class="{'nav-item': true}">
        <button class="nav-link btn btn-link" title="Live reloader" type="button" @click="pp">
            <i :class="{fas: true, 'fa-play': !play, 'fa-pause': play}"></i>
        </button>
    </li>
    <li :class="{'nav-item': true, dropdown: true}" style="margin-left: -10px;">
        <button aria-expanded="false" bind:disabled="play" aria-haspopup="true" class="btn btn-link nav-item"
                data-reference="parent" data-toggle="dropdown" style="padding-left: 0; padding-bottom: 0" type="button">
            <span x-text="interval"></span> <span class="d-none d-lg-inline d-xl-inline">sec</span>
        </button>
        <div :class="{'dropdown-menu': true}">
            <template x-for="(i,k) in intervals">
                <a :key="k" :class="{'dropdown-item': true, active: interval===i}"
                   href="javascript:void(0)" @click="si(i)">
                    <span x-text="i"></span> sec
                </a>
            </template>
        </div>
    </li>
</ul>
