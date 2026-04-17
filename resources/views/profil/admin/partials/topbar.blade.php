<header class="admin-topbar">
    <div class="topbar-left">
        <h1 class="page-title">@yield('page_title', 'Dashboard')</h1>
        <div class="page-subtitle d-none d-lg-block">Admin Panel — Restu Guru Promosindo</div>
    </div>

    <div class="topbar-right">
        <div class="admin-user">
            <div class="user-avatar" aria-hidden="true">RG</div>
            <div class="user-meta d-none d-sm-block">
                <div class="user-name">Restu Guru</div>
                <div class="user-role">Admin</div>
            </div>
        </div>

        <a class="btn-logout" href="javascript:void(0)" id="btnLogout" title="Logout">
            <i class="bi bi-box-arrow-right"></i>
            <span class="d-none d-sm-inline">Logout</span>
        </a>
    </div>
</header>
