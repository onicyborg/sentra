@php
    // Permission-based rendering (RBAC Hybrid)
@endphp

<div class="app-sidebar-menu overflow-hidden flex-column-fluid">
    <!--begin::Menu wrapper-->
    <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper">
        <!--begin::Scroll wrapper-->
        <div id="kt_app_sidebar_menu_scroll" class="scroll-y my-5 mx-3" data-kt-scroll="true" data-kt-scroll-activate="true"
            data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer"
            data-kt-scroll-save-state="true">

            <!--begin::Menu-->
            <div class="menu menu-column menu-rounded menu-sub-indention fw-semibold fs-6" id="#kt_app_sidebar_menu"
                data-kt-menu="true" data-kt-menu-expand="false">

                {{-- ========================= --}}
                {{-- ADMIN SYSTEM              --}}
                {{-- ========================= --}}
                <div class="menu-item">
                    <a class="menu-link {{ request()->is('/') || request()->routeIs('dashboard') ? 'active' : '' }}"
                        href="{{ route('dashboard') }}">
                        <span class="menu-icon">
                            <i class="bi bi-grid fs-2"></i>
                        </span>
                        <span class="menu-title">Dashboard</span>
                    </a>
                </div>

                <div class="menu-item pt-5">
                    <div class="menu-content">
                        <span class="menu-section text-muted text-uppercase fs-8 ls-1">
                            Admin System
                        </span>
                    </div>
                </div>

                @can('user.manage')
                <div class="menu-item">
                    <a class="menu-link" href="{{ url('/admin/users') }}">
                        <span class="menu-icon">
                            <i class="bi bi-people fs-2"></i>
                        </span>
                        <span class="menu-title">Users</span>
                    </a>
                </div>
                @endcan

                @can('role.manage')
                <div class="menu-item">
                    <a class="menu-link" href="{{ url('/admin/roles') }}">
                        <span class="menu-icon">
                            <i class="bi bi-shield-lock fs-2"></i>
                        </span>
                        <span class="menu-title">Roles</span>
                    </a>
                </div>
                @endcan

                @can('permission.manage')
                <div class="menu-item">
                    <a class="menu-link" href="{{ url('/admin/permissions') }}">
                        <span class="menu-icon">
                            <i class="bi bi-key fs-2"></i>
                        </span>
                        <span class="menu-title">Permissions</span>
                    </a>
                </div>
                @endcan

                @can('archive.read')
                <div class="menu-item">
                    <a class="menu-link" href="{{ url('/admin/archive') }}">
                        <span class="menu-icon">
                            <i class="bi bi-archive fs-2"></i>
                        </span>
                        <span class="menu-title">Arsip (Global)</span>
                    </a>
                </div>
                @endcan

                @can('notification.read')
                <div class="menu-item">
                    <a class="menu-link" href="{{ url('/admin/notifications') }}">
                        <span class="menu-icon">
                            <i class="bi bi-bell fs-2"></i>
                        </span>
                        <span class="menu-title">Notifications</span>
                    </a>
                </div>
                @endcan

                @can('audit.read')
                <div class="menu-item">
                    <a class="menu-link" href="{{ url('/admin/activity-logs') }}">
                        <span class="menu-icon">
                            <i class="bi bi-clipboard-data fs-2"></i>
                        </span>
                        <span class="menu-title">Activity Logs</span>
                    </a>
                </div>
                @endcan

            </div>
            <!--end::Menu-->
        </div>
        <!--end::Scroll wrapper-->
    </div>
    <!--end::Menu wrapper-->
</div>
