<div class="header">
    <div class="header-left">
        <a href="{{ url('/') }}" class="logo">
            <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" />
        </a>
        <a href="{{ url('/') }}" class="logo logo-small">
            <img
                src="{{ asset('assets/img/logo-small.png') }}"
                alt="Logo"
                width="60"
                height="60"
            />
        </a>
    </div>

    <div class="menu-toggle">
        <a href="javascript:void(0);" id="toggle_btn">
            <i class="fas fa-bars"></i>
        </a>
    </div>

    <div class="top-nav-search">
        <form method="POST" action="{{ route('logout') }}">
            <input
                type="text"
                class="form-control"
                placeholder="Search here"
                name="query"
            />
            <button class="btn" type="submit">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>

    <a class="mobile_btn" id="mobile_btn">
        <i class="fas fa-bars"></i>
    </a>

    <ul class="nav user-menu">


        <li class="nav-item dropdown noti-dropdown me-2">
            <a
                href="#"
                class="dropdown-toggle nav-link header-nav-list"
                data-bs-toggle="dropdown"
            >
                <img
                    src="{{ asset('assets/img/icons/header-icon-05.svg') }}"
                    alt=""
                />
            </a>
            <div class="dropdown-menu notifications">
                <div class="topnav-dropdown-header">
                    <span class="notification-title">Notifications</span>
                    <a href="javascript:void(0)" class="clear-noti">
                        Clear All
                    </a>
                </div>
                <div class="noti-content">
                    <ul class="notification-list">
                        <li class="notification-message">
                            <a href="#">
                                <div class="media d-flex">
                                    <span class="avatar avatar-sm flex-shrink-0">
                                        <img
                                            class="avatar-img rounded-circle"
                                            alt="User Image"
                                            src="{{ asset('assets/img/profiles/avatar-02.jpg') }}"
                                        />
                                    </span>
                                    <div class="media-body flex-grow-1">
                                        <p class="noti-details">
                                            <span class="noti-title">Carlson Tech</span>
                                            has approved
                                            <span class="noti-title">your estimate</span>
                                        </p>
                                        <p class="noti-time">
                                            <span class="notification-time">4 mins ago</span>
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <!-- المزيد من الإشعارات هنا -->
                    </ul>
                </div>
                <div class="topnav-dropdown-footer">
                    <a href="#">View all Notifications</a>
                </div>
            </div>
        </li>

        <li class="nav-item zoom-screen me-2">
            <a href="#" class="nav-link header-nav-list">
                <img
                    src="{{ asset('assets/img/icons/header-icon-04.svg') }}"
                    alt=""
                />
            </a>
        </li>

        <li class="nav-item dropdown has-arrow new-user-menus">
            <a
                href="#"
                class="dropdown-toggle nav-link"
                data-bs-toggle="dropdown"
            >
                <span class="user-img">
                    <img
                        class="rounded-circle"
                        src="{{ asset('assets/img/profiles/avatar-01.jpg') }}"
                        width="31"
                        alt="Soeng Souy"
                    />
                    <div class="user-text">
                        <h6>{{ Auth::user()->name }}</h6>
                        <p class="text-muted mb-0">Administrator</p>
                    </div>
                </span>
            </a>
            <div class="dropdown-menu">
                <div class="user-header">
                    <div class="avatar avatar-sm">
                        <img
                            src="{{ asset('assets/img/profiles/avatar-01.jpg') }}"
                            alt="User Image"
                            class="avatar-img rounded-circle"
                        />
                    </div>
                    <div class="user-text">
                        <h6>{{ Auth::user()->name }}</h6>
                        <p class="text-muted mb-0">Administrator</p>
                    </div>
                </div>
                <a class="dropdown-item" href="{{ route('profile.edit') }}">My Profile</a>
                <a class="dropdown-item" href="">Inbox</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
                
                <a class="dropdown-item" href="" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    {{ __('Logout') }}
                </a>
                </div>
        </li>
        <li class="nav-item dropdown language-drop me-2">
            {{-- <a
                href="#"
                class="dropdown-toggle nav-link header-nav-list"
                data-bs-toggle="dropdown"
            >
                <img
                    src="{{ asset('assets/img/icons/header-icon-01.svg') }}"
                    alt=""
                />
            </a> --}}
            {{-- <div class="dropdown-menu">
                <a class="dropdown-item"  href="{{ route('setLocale', ['locale' => 'en']) }}">
                    <i class="flag flag-lr me-2"></i>English
                </a>
                <a class="dropdown-item"  href="{{ route('setLocale', ['locale' => 'ar']) }}">
                    <i class="flag flag-ar me-2"></i>العربية
                </a>

            </div> --}}
            <div class="dropdown ms-auto">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="languageDropdown"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    🌐 {{ strtoupper(app()->getLocale()) }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
                    <li><a class="dropdown-item" href="{{ route('setLocale', ['locale' => 'en']) }}">English</a></li>
                    <li><a class="dropdown-item" href="{{ route('setLocale', ['locale' => 'ar']) }}">العربية</a></li>
                </ul>

            </div>
        </li>
    </ul>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    const mobileBtn = document.getElementById('mobile_btn');
    const sidebar = document.getElementById('sidebar');

    if (!mobileBtn || !sidebar) return; // إذا لم يكن أحد العناصر موجودًا، لا تنفذ شيئًا

    function togglesidebar() {
        if (sidebar.style.display === 'none' || sidebar.style.display === '') {
        sidebar.style.display = 'block';
        } else {
        sidebar.style.display = 'none';
        }
    }

    function handleSidebarToggle() {
        mobileBtn.onclick = null;

        if (window.innerWidth <= 992) {
        mobileBtn.onclick = togglesidebar;
        } else {
        sidebar.style.display = 'block';
        }
    }

    document.addEventListener('click', function (event) {
        // تحقق أولاً أن العنصر موجود قبل استخدام contains
        if (window.innerWidth <= 992 && sidebar && mobileBtn) {
        const isClickInsideSidebar = sidebar.contains(event.target);
        const isClickOnMobileBtn = mobileBtn.contains(event.target);

        if (!isClickInsideSidebar && !isClickOnMobileBtn) {
            sidebar.style.display = 'none';
        }
        }
    });

    handleSidebarToggle();
    window.addEventListener('resize', handleSidebarToggle);
    });

  </script>
  