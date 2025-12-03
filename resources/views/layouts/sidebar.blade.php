<div id="sidebar" class="sidebar bg-white shadow-sm app-sidebar sidebar-expanded">
    <div class="d-flex flex-column flex-shrink-0 p-3">

        <div class="text-center mb-4">
            <img src="{{ asset('icons/logo.png') }}" alt="Logo" style="height:60px;">
            <h5 class="fw-bold mt-2 text-purple">APP PEGAWAI</h5>
        </div>

        @php
            $url_menu = Request::segment(1);
            $role = Auth::user()->role ?? '';
        @endphp

        <!-- HOME -->
        <div class="text-uppercase small fw-bold text-muted mt-3 mb-2">Home</div>
        <ul class="nav nav-pills flex-column mb-3 gap-1">
            <li>
                <a href="{{ route('dashboard') }}" class="nav-link {{ $url_menu === 'dashboard' ? 'active' : '' }}">
                    <i class="fa-solid fa-gauge-high me-2"></i>
                    <span>Dashboard</span>
                </a>
            </li>
        </ul>

        @if($role === 'admin')
            <!-- ATTENDANCE -->
            <div class="text-uppercase small fw-bold text-muted mt-3 mb-2">Attendance</div>
            <ul class="nav nav-pills flex-column mb-3 gap-1">
                <li>
                    <a href="{{ route('attendance.admin') }}"  class="nav-link {{ $url_menu === 'attendance' && Request::segment(2) === 'admin' ? 'active' : '' }}">
                        <i class="fa-solid fa-calendar-check me-2"></i>
                        <span>Overview</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('permission.admin') }}" 
                    class="nav-link {{ $url_menu === 'permission' && Request::segment(2) === 'admin' ? 'active' : '' }}">
                        <i class="fa-solid fa-clipboard-check me-2"></i>
                        <span>Approval Izin</span>
                    </a>
                </li>
            </ul>

            <!-- MASTER DATA -->
            <div class="text-uppercase small fw-bold text-muted mt-3 mb-2">Master Data</div>
            <ul class="nav nav-pills flex-column mb-3 gap-1">
                <li>
                    <a href="{{ route('employees.index') }}" class="nav-link {{ $url_menu === 'employees' ? 'active' : '' }}">
                        <i class="fa-solid fa-users me-2"></i>
                        <span>Employees</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('departments.index') }}" class="nav-link {{ $url_menu === 'departments' ? 'active' : '' }}">
                        <i class="fa-solid fa-building me-2"></i>
                        <span>Departments</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('positions.index') }}" class="nav-link {{ $url_menu === 'positions' ? 'active' : '' }}">
                        <i class="fa-solid fa-id-card me-2"></i>
                        <span>Positions</span>
                    </a>
                </li>
            </ul>

            <!-- TOOLS -->
            <div class="text-uppercase small fw-bold text-muted mt-3 mb-2">Tools</div>
            <ul class="nav nav-pills flex-column mb-3 gap-1">
                <li>
                    <a href="{{ route('announcement.index') }}" class="nav-link {{ $url_menu === 'announcement' ? 'active' : '' }}">
                        <i class="fa-solid fa-bullhorn me-2"></i>
                        <span>Announcement</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('salary.admin') }}" class="nav-link {{ $url_menu === 'salary' ? 'active' : '' }}">
                        <i class="fa-solid fa-money-check-dollar"></i>
                        <span class="sidebar-text">Kelola Gaji</span>
                    </a>
                </li>
            </ul>
        @endif

        @if($role === 'karyawan')
            <!-- ATTENDANCE -->
            <div class="text-uppercase small fw-bold text-muted mt-3 mb-2">Attendance</div>
            <ul class="nav nav-pills flex-column mb-3 gap-1">
                <li>
                    <a href="{{ route('permission.karyawan') }}" 
                    class="nav-link {{ $url_menu === 'permission' ? 'active' : '' }}">
                        <i class="fa-solid fa-envelope-open-text me-2"></i>
                        <span>Pengajuan Izin</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('attendance.karyawan') }}" class="nav-link {{ $url_menu === 'attendance' ? 'active' : '' }}">
                        <i class="fa-solid fa-calendar-check me-2"></i>
                        <span>Attendance</span>
                    </a>
                </li>
            </ul>

            <!-- TOOLS -->
            <div class="text-uppercase small fw-bold text-muted mt-3 mb-2">Tools</div>
            <ul class="nav nav-pills flex-column mb-3 gap-1">
                <li class="nav-item">
                    <a href="{{ route('salary.karyawan') }}" class="nav-link {{ $url_menu === 'salary' ? 'active' : '' }}">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                        <span class="sidebar-text">Slip Gaji</span>
                    </a>
                </li>
            </ul>
        @endif

    </div>
</div>
