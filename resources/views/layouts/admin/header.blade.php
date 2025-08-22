        <!-- Page Header Start-->
        <div class="page-header">
            <div class="header-wrapper row m-0">
                <div class="header-logo-wrapper col-auto p-0">
                    <div class="logo-wrapper">
                        <a href="{{ route('admin.home') }}">
                            <img class="img-fluid for-light" src="{{ asset('assets/images/logo/logo.png') }}"
                                alt="">
                            <img class="img-fluid for-dark" src="{{ asset('assets/images/logo/logo_dark.png') }}"
                                alt="">
                        </a>
                    </div>
                    <div class="toggle-sidebar">
                        <i class="status_toggle middle sidebar-toggle" data-feather="align-center"></i>
                    </div>
                </div>
                <div class="nav-right col-xxl-7 col-xl-6 col-md-7 col-8 pull-right right-header p-0 ms-auto">
                    <ul class="nav-menus">
                        <li class="fullscreen-body">
                            <span>
                                <svg id="maximize-screen">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#full-screen') }}"></use>
                                </svg>
                            </span>
                        </li>
                        <li>
                            <div class="mode">
                                <svg>
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#moon') }}"></use>
                                </svg>
                            </div>
                        </li>
                        <li class="profile-nav onhover-dropdown pe-0 py-0">
                            <div class="d-flex profile-media">
                                <img class="b-r-10"
                                    src="{{ auth()->user()->image ? asset('storage/' . auth()->user()->image) : asset('assets/svg/user-placeholder.svg') }}"
                                    width="35" height="35" alt="" style="object-fit: cover;">

                                <div class="flex-grow-1">
                                    <span>{{ ucfirst(auth()?->user()?->name) }}</span>
                                    <p class="mb-0">{{ auth()?->user()->username }} <i
                                            class="middle fa-solid fa-angle-down"></i>
                                    </p>
                                </div>
                            </div>
                            <ul class="profile-dropdown onhover-show-div">

                                <li><a href="{{ route('account.index', ['username' => auth()->user()->username]) }}"><i
                                            data-feather="user"></i><span>Akun </span></a></li>
                                {{-- <li><a href="{{ route('admin.mail_box') }}"><i data-feather="mail"></i><span>Inbox</span></a></li> --}}
                                {{-- <li><a href="{{ route('admin.task') }}"><i data-feather="file-text"></i><span>Taskboard</span></a></li> --}}
                                {{-- <li><a href="{{ route('admin.add_user') }}"><i data-feather="settings"></i><span>Settings</span></a></li> --}}

                                <li>
                                    <a href="{{ route('logout') }}"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i data-feather="log-in"></i><span>Log out</span>
                                    </a>
                                </li>
                                <form action="{{ route('logout') }}" method="POST" class="d-none" id="logout-form">
                                    @csrf
                                </form>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- Page Header Ends -->
