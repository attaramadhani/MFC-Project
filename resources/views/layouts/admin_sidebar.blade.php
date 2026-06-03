@php
    $adminName    = $adminName    ?? (auth()->user()->nama_user ?? 'Admin');
    $adminInitial = $adminInitial ?? mb_strtoupper(mb_substr($adminName, 0, 1));
@endphp

<aside class="admin-sidebar">
    <a href="{{ route('admin.dashboard') }}" class="sidebar-brand text-decoration-none">
        <div class="brand-logo" style="background: transparent; box-shadow: none; width: auto; height: auto;">
            <img src="{{ asset('img/logo.jpg') }}" alt="MFC Logo" height="42" class="rounded" style="object-fit: contain;">
        </div>
        <div>
            MFC<br>
            <small>Admin Panel</small>
        </div>
    </a>

    <nav class="sidebar-nav">
        <a href="{{ route('admin.dashboard') }}"
           class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <span class="icon">🏠</span>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('admin.menu.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.menu.*') ? 'active' : '' }}">
            <span class="icon">📋</span>
            <span>Menu</span>
        </a>

        <a href="{{ route('admin.orders.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
            <span class="icon">🧾</span>
            <span>Pesanan</span>
        </a>

        <a href="{{ route('admin.reports.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
            <span class="icon">📊</span>
            <span>Laporan</span>
        </a>

        <a href="{{ route('admin.users.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <span class="icon">👥</span>
            <span>Users</span>
        </a>

        <a href="{{ route('admin.profile.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
            <span class="icon">⚙️</span>
            <span>Profile</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="avatar-circle">{{ $adminInitial }}</div>
            <div>
                <div class="user-name">{{ $adminName }}</div>
                <div class="user-role">Administrator</div>
            </div>
        </div>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-secondary btn-sm w-100 rounded-pill">
                Logout
            </button>
        </form>
    </div>
</aside>