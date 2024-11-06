<div class="text-center">
    <img class="profile-user-img img-fluid img-circle"
         src="{{asset($user->avatar)}}"
         alt="User profile picture">
</div>

<h3 class="profile-username text-center">{{$user->name}} <small>({{ $user->login }})</small></h3>

<p class="text-muted text-center">{{$user->email}}</p>

<ul class="list-group list-group-unbordered mb-3">
    <li class="list-group-item">
        <b>ID</b> <a class="float-right">{{$user->id}}</a>
    </li>
    <li class="list-group-item">
        <b>@lang('admin.2fa_secure')</b>
        {!! $user->two_factor_confirmed_at
            ? '<span class="badge badge-success float-right">Enabled</span>'
            : '<span class="badge badge-danger float-right">Disabled</span>'
        !!}
    </li>
    <li class="list-group-item">
        <b>@lang('admin.roles')</b>
        <span class="badge badge-success float-right">
            {!! Admin::user()->roles->pluck('name')->implode('</span>&nbsp;<span class="badge badge-warning">') !!}
        </span>
    </li>
    <li class="list-group-item">
        <b>@lang('admin.activity')</b>
        <a class="float-right">{{ $user->logs()->count() }}</a>
    </li>
    <li class="list-group-item">
        <b>@lang('admin.day_activity')</b>
        <a class="float-right">
            {{ $user->logs()->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])->count() }}
        </a>
    </li>
    <li class="list-group-item">
        <b>@lang('admin.created_at')</b>
        <a class="float-right">{{$user->created_at}}</a>
    </li>
    <li class="list-group-item">
        <b>@lang('admin.updated_at')</b>
        <a class="float-right">{{$user->updated_at}}</a>
    </li>
</ul>
