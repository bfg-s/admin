@extends($default_page)

@php
    /** @var \Lar\LteAdmin\Models\LteUser $user */
@endphp

@section('content')

    <div class="row">
        <div class="col-md-3">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
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
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
        <div class="col-md-9">
            @card()

                @cardhead('Данные администратора')
                    @cardheadtools()
                        @buttongroup()
                            @bgroupreload()
                        @endbuttongroup
                    @endcardheadtools
                @endcardhead

                @tabs()

                    @tab('General', 'fas fa-cogs')

                        @cardbodyform($user)

                            @formgroup('Avatar', 'avatar', null)
                                @formfile()
                            @endformgroup

                            @formgroup('Login', 'login')
                                @forminput(['rule' => ['required']])
                            @endformgroup

                            @formgroup('Email', 'email')
                                @forminput(['rule' => ['required']])
                            @endformgroup

                            @formgroup('Name', 'name')
                                @forminput(['rule' => ['required']])
                            @endformgroup

                        @endcardbodyform

                        @formfooter()

                    @endtab

                    @tab('Change password', 'fas fa-key')

                        @cardbodyform()

                            @hiddens(['ch_password' => 'true'])

                            @formgroup('New password', 'password')
                                @formpassword(['rule' => ['required']])
                            @endformgroup

                            @formgroup('Repeat new password', 'password_confirmation')
                                @formpassword(['rule' => ['required', 'confirmation']])
                            @endformgroup

                        @endcardbodyform

                        @formfooter()

                    @endtab

                @endtabs

            @endcard
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->

@endsection