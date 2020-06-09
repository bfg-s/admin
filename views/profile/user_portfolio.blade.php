<div class="text-center">
    <img class="profile-user-img img-fluid img-circle"
         src="{{asset($user->avatar)}}"
         alt="User profile picture">
</div>

<h3 class="profile-username text-center">{{$user->name}}</h3>

<p class="text-muted text-center">{{$user->roles->pluck('name')->implode(', ')}}</p>

<ul class="list-group list-group-unbordered mb-3">
    <li class="list-group-item">
        <b>Created at</b> <a class="float-right">{{$user->created_at}}</a>
    </li>
    <li class="list-group-item">
        <b>Updated at</b> <a class="float-right">{{$user->created_at}}</a>
    </li>
</ul>