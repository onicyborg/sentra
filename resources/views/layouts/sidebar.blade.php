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
                @if (auth()->user()->roles()->where('name', 'admin_system')->exists())
                    <div class="menu-item">
                        <a class="menu-link {{ request()->is('/admin') || request()->routeIs('dashboard') ? 'active' : '' }}"
                            href="{{ route('dashboard') }}">
                            <span class="menu-icon">
                                <i class="bi bi-grid fs-2"></i>
                            </span>
                            <span class="menu-title">dashboard</span>
                        </a>
                    </div>
                @else
                    <div class="menu-item">
                        <a class="menu-link {{ request()->is('/') || request()->routeIs('home') ? 'active' : '' }}"
                            href="{{ route('home') }}">
                            <span class="menu-icon">
                                <i class="bi bi-grid fs-2"></i>
                            </span>
                            <span class="menu-title">Home</span>
                        </a>
                    </div>
                @endif

                @if (auth()->user()->roles()->where('name', 'admin_system')->exists())
                    <div class="menu-item pt-5">
                        <div class="menu-content">
                            <span class="menu-section text-muted text-uppercase fs-8 ls-1">
                                Admin System
                            </span>
                        </div>
                    </div>
                @endif

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

                {{-- ========================= --}}
                {{-- APLIKASI (USER)           --}}
                {{-- ========================= --}}
                <div class="menu-item pt-5">
                    <div class="menu-content">
                        <span class="menu-section text-muted text-uppercase fs-8 ls-1">
                            Aplikasi
                        </span>
                    </div>
                </div>

                @canany(['surat_masuk.create', 'surat_masuk.read', 'surat_masuk.distribute', 'surat_masuk.verify'])
                    <div class="menu-item">
                        <a class="menu-link" href="{{ url('/surat-masuk') }}">
                            <span class="menu-icon">
                                <i class="bi bi-inbox fs-2"></i>
                            </span>
                            <span class="menu-title">Surat Masuk</span>
                        </a>
                    </div>
                @endcanany

                @canany(['surat_keluar.create', 'surat_keluar.read'])
                    <div class="menu-item">
                        <a class="menu-link" href="{{ url('/surat-keluar') }}">
                            <span class="menu-icon">
                                <i class="bi bi-send fs-2"></i>
                            </span>
                            <span class="menu-title">Surat Keluar</span>
                        </a>
                    </div>
                @endcanany

                @can('surat_masuk.follow_up')
                    <div class="menu-item">
                        <a class="menu-link" href="{{ url('/tindak-lanjut') }}">
                            <span class="menu-icon">
                                <i class="bi bi-journal-check fs-2"></i>
                            </span>
                            <span class="menu-title">Tindak Lanjut</span>
                        </a>
                    </div>
                @endcan

                <div class="menu-item">
                    <a class="menu-link" href="{{ url('/notifications') }}">
                        <span class="menu-icon">
                            <i class="bi bi-bell fs-2"></i>
                        </span>
                        <span class="menu-title">Notifikasi</span>
                    </a>
                </div>

                @can('archive.read')
                    <div class="menu-item">
                        <a class="menu-link" href="{{ url('/archive') }}">
                            <span class="menu-icon">
                                <i class="bi bi-archive fs-2"></i>
                            </span>
                            <span class="menu-title">Arsip</span>
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
