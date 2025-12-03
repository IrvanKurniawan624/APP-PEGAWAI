<header class="bg-white border-bottom">
    <div class="d-flex justify-content-between align-items-center py-3 gap-4 pe-4">
        <div class="d-flex align-items-center gap-3">
            <button id="sidebarToggle" class="btn btn-light shadow-sm ms-2">
                <i class="fa-solid fa-bars"></i>
            </button>
    
            <h5 class="mb-0 text-dark">@yield('title')</h5>
        </div>

        <div class="dropdown mt-auto text-center">
            <a href="#" class="d-flex align-items-center justify-content-center link-dark text-decoration-none dropdown-toggle"
            data-bs-toggle="dropdown">
                <img src="{{ asset('icons/avatar.png') }}" class="rounded-circle me-2" width="32">
                <strong class="sidebar-text">{{ Auth::user()->name }}</strong>
            </a>

            <ul class="dropdown-menu shadow">
                
                @if(Auth::user()->role === 'karyawan')
                <li>
                    <a class="dropdown-item" href="{{ route('profile.karyawan') }}">
                        <i class="fa-solid fa-user me-2"></i>
                        <span class="sidebar-text">Profile</span>
                    </a>
                </li>

                <li><hr class="dropdown-divider"></li>
                @endif

                <li>
                    <form method="GET" action="{{ route('logout') }}">
                        <button class="dropdown-item text-danger">
                            <i class="fa-solid fa-right-from-bracket me-2"></i>
                            <span class="sidebar-text">Logout</span>
                        </button>
                    </form>
                </li>

            </ul>
        </div>

    </div>
</header>