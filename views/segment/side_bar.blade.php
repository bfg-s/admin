<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">

    <!-- Sidebar -->
    <div class="sidebar" @live('sidebar')>
        <!-- Sidebar user (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex" data-href="{{route('lte.profile')}}">
            <div class="image">
                <img src="{{asset(lte_user()->avatar)}}" class="img-circle elevation-2" alt="{{lte_user()->name}}">
            </div>
            <div class="info">
                <a class="d-block">{{lte_user()->name}}</a>
                <small class="d-block" title="You permissions"><span class="badge badge-success">{!! LteAdmin::user()->roles->pluck('name')->implode('</span>, <span class="badge badge-success">') !!}</span></small>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-sidebar nav-child-indent flex-column" data-widget="treeview" role="menu" data-accordion="true">
                @include('lte::segment.side_bar_items')
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
