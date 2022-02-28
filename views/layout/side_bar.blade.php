<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-inner" @live(
        'sidebar')>
        <!-- Sidebar user (optional) -->
        <div class="user-panel mt-1 pb-1 mb-3 d-flex">
            <a class="image" href="{{route('lte.profile')}}">
                <img src="{{asset(lte_user()->avatar)}}" class="img-circle elevation-2" alt="{{lte_user()->name}}">
            </a>
            <div class="info">
                <div class="d-block text-light">
                    {{lte_user()->name}} &nbsp;
                    <a href="{{route('lte.profile')}}" title="{{__('lte.edit_profile')}}">
                        <i class="fas fa-edit" style="font-size: 12px;"></i>
                    </a>
                </div>
                <small class="d-block text-light">
                    {{ lte_user()->email }}
                </small>
                <small class="d-block text-light">
                        <span
                            class="badge badge-success">{!! LteAdmin::user()->roles->pluck('name')->implode('</span>&nbsp;<span class="badge badge-success">') !!}</span>
                </small>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-sidebar nav-child-indent flex-column" data-widget="treeview" role="menu"
                data-accordion="true">
                @include('lte::layout.side_bar_items')
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    </div>
    <!-- /.sidebar -->
</aside>
