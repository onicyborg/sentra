@php
    $role = auth()->user()->role ?? null;
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
                {{-- UMUM (SEMUA ROLE)        --}}
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

                {{-- ========================= --}}
                {{-- ROLE: ADMIN               --}}
                {{-- ========================= --}}
                @if ($role === 'admin')
                    <!-- MASTER DATA SECTION -->
                    <div class="menu-item pt-5">
                        <div class="menu-content">
                            <span class="menu-section text-muted text-uppercase fs-8 ls-1">
                                Master Data
                            </span>
                        </div>
                    </div>

                    <!-- Angkatan -->
                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('batches.*') ? 'active' : '' }}"
                            href="{{ route('batches.index') }}">
                            <span class="menu-icon">
                                <i class="bi bi-calendar3 fs-2"></i>
                            </span>
                            <span class="menu-title">Angkatan</span>
                        </a>
                    </div>

                    <!-- Guru -->
                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('teachers.*') ? 'active' : '' }}"
                            href="{{ route('teachers.index') }}">
                            <span class="menu-icon">
                                <i class="bi bi-people fs-2"></i>
                            </span>
                            <span class="menu-title">Guru</span>
                        </a>
                    </div>

                    <!-- Kelas -->
                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('classes.*') ? 'active' : '' }}"
                            href="{{ route('classes.index') }}">
                            <span class="menu-icon">
                                <i class="bi bi-columns-gap fs-2"></i>
                            </span>
                            <span class="menu-title">Kelas</span>
                        </a>
                    </div>

                    <!-- Siswa -->
                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('students.*') ? 'active' : '' }}"
                            href="{{ route('students.index') }}">
                            <span class="menu-icon">
                                <i class="bi bi-person-circle fs-2"></i>
                            </span>
                            <span class="menu-title">Siswa</span>
                        </a>
                    </div>

                    <!-- KEHADIRAN -->
                    <div class="menu-item pt-5">
                        <div class="menu-content">
                            <span class="menu-section text-muted text-uppercase fs-8 ls-1">
                                Monitoring Kehadiran
                            </span>
                        </div>
                    </div>

                    <!-- Riwayat Kehadiran -->
                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('attendances.index') ? 'active' : '' }}"
                            href="{{ route('attendances.index') }}">
                            <span class="menu-icon">
                                <i class="bi bi-clock fs-2"></i>
                            </span>
                            <span class="menu-title">Riwayat Kehadiran</span>
                        </a>
                    </div>

                    <!-- LAPORAN & NOTIFIKASI -->
                    <div class="menu-item pt-5">
                        <div class="menu-content">
                            <span class="menu-section text-muted text-uppercase fs-8 ls-1">
                                Laporan & Notifikasi
                            </span>
                        </div>
                    </div>

                    <!-- Laporan Kehadiran -->
                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('reports.attendance') ? 'active' : '' }}"
                            href="{{ route('reports.attendance') }}">
                            <span class="menu-icon">
                                <i class="bi bi-graph-up fs-2"></i>
                            </span>
                            <span class="menu-title">Laporan Kehadiran</span>
                        </a>
                    </div>

                    <!-- Log Notifikasi -->
                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('notification_logs.*') ? 'active' : '' }}"
                            href="{{ route('notification_logs.index') }}">
                            <span class="menu-icon">
                                <i class="bi bi-bell fs-2"></i>
                            </span>
                            <span class="menu-title">Log Notifikasi</span>
                        </a>
                    </div>
                @endif

                {{-- ========================= --}}
                {{-- ROLE: GURU (TEACHER)      --}}
                {{-- ========================= --}}
                @if ($role === 'teacher')

                    <!-- MASTER DATA SECTION -->
                    <div class="menu-item pt-5">
                        <div class="menu-content">
                            <span class="menu-section text-muted text-uppercase fs-8 ls-1">
                                Master Data
                            </span>
                        </div>
                    </div>

                    <!-- Siswa -->
                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('teacher.students.*') ? 'active' : '' }}"
                            href="{{ route('teacher.students.index') }}">
                            <span class="menu-icon">
                                <i class="bi bi-person-circle fs-2"></i>
                            </span>
                            <span class="menu-title">Siswa</span>
                        </a>
                    </div>

                    <!-- KEHADIRAN -->
                    <div class="menu-item pt-5">
                        <div class="menu-content">
                            <span class="menu-section text-muted text-uppercase fs-8 ls-1">
                                Kehadiran Siswa
                            </span>
                        </div>
                    </div>

                    <!-- Input Kehadiran -->
                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('teacher.attendances.*') && !request()->routeIs('teacher.attendances.index') ? 'active' : '' }}"
                            href="{{ route('teacher.attendances.classes') }}">
                            <span class="menu-icon">
                                <i class="bi bi-pencil-square fs-2"></i>
                            </span>
                            <span class="menu-title">Input Kehadiran</span>
                        </a>
                    </div>


                    <!-- Riwayat Kehadiran -->
                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('teacher.attendances.index') ? 'active' : '' }}"
                            href="{{ route('teacher.attendances.index') }}">
                            <span class="menu-icon">
                                <i class="bi bi-clock fs-2"></i>
                            </span>
                            <span class="menu-title">Riwayat Kehadiran</span>
                        </a>
                    </div>

                    <!-- Laporan Kehadiran (opsional untuk guru) -->
                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('teacher.reports.attendance') ? 'active' : '' }}"
                            href="{{ route('teacher.reports.attendance') }}">
                            <span class="menu-icon">
                                <i class="bi bi-graph-up fs-2"></i>
                            </span>
                            <span class="menu-title">Laporan Kehadiran</span>
                        </a>
                    </div>
                @endif

                {{-- ========================= --}}
                {{-- ROLE: SISWA (STUDENT)     --}}
                {{-- ========================= --}}
                @if ($role === 'student')
                    <div class="menu-item pt-5">
                        <div class="menu-content">
                            <span class="menu-section text-muted text-uppercase fs-8 ls-1">
                                Kehadiran Saya
                            </span>
                        </div>
                    </div>

                    <!-- Riwayat Kehadiran Siswa -->
                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('student.attendances.index') ? 'active' : '' }}"
                            href="{{ route('student.attendances.index') }}">
                            <span class="menu-icon">
                                <i class="bi bi-clock fs-2"></i>
                            </span>
                            <span class="menu-title">Riwayat Kehadiran</span>
                        </a>
                    </div>
                @endif

                {{-- ========================= --}}
                {{-- PROFIL (SEMUA ROLE)       --}}
                {{-- ========================= --}}
                <div class="menu-item pt-5">
                    <a class="menu-link {{ request()->routeIs('profile.show') ? 'active' : '' }}"
                        href="{{ route('profile.show') }}">
                        <span class="menu-icon">
                            <i class="bi bi-gear fs-2"></i>
                        </span>
                        <span class="menu-title">Profil Saya</span>
                    </a>
                </div>

            </div>
            <!--end::Menu-->
        </div>
        <!--end::Scroll wrapper-->
    </div>
    <!--end::Menu wrapper-->
</div>
