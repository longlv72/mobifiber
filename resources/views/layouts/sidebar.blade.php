<ul class="navbar-nav" id="navbar-nav">
    <li class="menu-title"><i class="ri-more-fill"></i> <span data-key="t-reports">Báo cáo</span></li>
    <!-- Báo cáo doanh thu -->
    <li class="nav-item">
        <a class="nav-link menu-link collapsed" href="#sidebarDashboards" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarDashboards">
            <i class="bx bx-bar-chart-alt-2"></i> <span data-key="t-widgets">Báo cáo</span>
        </a>
        <div class="collapse menu-dropdown" id="sidebarDashboards">
            <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                    <a href="{{route('report')}}" class="nav-link" data-key="t-analytics">Doanh thu theo hợp dồng</a>
                </li>
                <li class="nav-item">
                    <a href="{{route('report-customer')}}" class="nav-link" data-key="t-crm">Doanh thu khách hàng</a>
                </li>
                <li class="nav-item">
                    <a href="{{route('report-growth')}}" class="nav-link" data-key="t-crm">Phát triển thuê bao</a>
                </li>
            </ul>
        </div>
    </li>
    <li class="menu-title"><i class="ri-more-fill"></i> <span data-key="t-utilities">Tiện ích</span></li>

    <!-- Quản lý đổi tác -->
    <li class="nav-item">
        <a class="nav-link menu-link" href="{{route('list-partner')}}">
            <i class="bx bxs-user-detail"></i> <span data-key="t-list-partner">Quản lý đối tác</span>
        </a>
    </li>

    <!-- Quản lý thiết bị -->
    <li class="nav-item">
        <a class="nav-link menu-link" href="{{route('list-devices')}}">
            <i class="bx bxs-devices"></i> <span data-key="t-devices">Quản lý thiết bị</span>
        </a>
    </li>

    <!-- Quản lý tòa nhà -->
    <li class="nav-item">
        <a class="nav-link menu-link collapsed" href="#sidebarBuilding" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarBuilding">
            <i class="bx bx-bar-chart-alt-2"></i> <span data-key="t-widgets">Quản lý tòa nhà</span>
        </a>
        <div class="collapse menu-dropdown" id="sidebarBuilding">
            <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                    <a href="{{route('list-buildings')}}" class="nav-link" data-key="t-analytics">Tòa nhà</a>
                </li>
                <li class="nav-item">
                    <a href="{{route('view-map')}}" class="nav-link" data-key="t-crm">Bản đồ tòa nhà</a>
                </li>
            </ul>
        </div>
    </li>

    <!-- Quản lý gói -->
    <li class="nav-item">
        <a class="nav-link menu-link" href="{{route('list-package')}}">
            <i class="bx bxs-package"></i> <span data-key="t-package">Quản lý gói cước</span>
        </a>
    </li>

    <li class="menu-title"><i class="ri-more-fill"></i> <span data-key="t-functions">Chức năng</span></li>

    <!-- Quản lý nhân viên -->
    <li class="nav-item">
        <a class="nav-link menu-link" href="{{route('list-employee')}}">
            <i class="bx bxs-user"></i> <span data-key="t-widgets">Quản lý nhân viên</span>
        </a>
    </li>

    <!-- Quản lý hợp đồng -->
    <li class="nav-item">
        <a class="nav-link menu-link" href="{{route('list-contracts')}}">
            <i class="bx bxs-file-doc"></i> <span data-key="t-widgets">Quản lý hợp đồng</span>
        </a>
    </li>

    <!-- Quản lý khách hàng -->
    <li class="nav-item">
        <a class="nav-link menu-link" href="{{route('list-customers')}}">
            <i class="bx bx-user-pin"></i> <span data-key="t-widgets">Quản lý khách hàng</span>
        </a>
    </li>

    <!-- Quản lý công việc -->
    <li class="nav-item">
        <a class="nav-link menu-link" href="{{route('list-jobs')}}">
            <i class="bx bx-donate-blood"></i> <span data-key="t-widgets">Quản lý công việc</span>
        </a>
    </li>

    <!-- Quản lý quyền -->
    <li class="nav-item">
        <a class="nav-link menu-link" href="{{route('list-role-groups')}}">
            <i class="bx bx-lock-open"></i> <span data-key="t-widgets">Nhóm quyền</span>
        </a>
    </li>

</ul>
