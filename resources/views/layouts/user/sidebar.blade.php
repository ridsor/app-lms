            <!-- Page Sidebar Start-->
            <div class="sidebar-wrapper" data-sidebar-layout="stroke-svg">
                <div>
                    <div class="logo-wrapper"><a href="{{ route('user.home') }}"><img class="img-fluid for-light"
                                src="{{ asset('assets/images/logo/logo.png') }}" alt=""><img
                                class="img-fluid for-dark" src="{{ asset('assets/images/logo/logo_dark.png') }}"
                                alt=""></a>
                        <div class="back-btn"><i class="fa-solid fa-angle-left"></i></div>
                        <div class="toggle-sidebar"><i class="status_toggle middle sidebar-toggle" data-feather="grid">
                            </i></div>
                    </div>
                    <div class="logo-icon-wrapper"><a href="{{ route('user.home') }}"><img class="img-fluid"
                                src="{{ asset('assets/images/logo/logo-icon.png') }}" alt=""></a></div>
                    <nav class="sidebar-main">
                        <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
                        <div id="sidebar-menu">
                            <ul class="sidebar-links" id="simple-bar">
                                <li class="back-btn"><a href="{{ route('user.home') }}"><img class="img-fluid"
                                            src="{{ asset('assets/images/logo/logo-icon.png') }}" alt=""></a>
                                    <div class="mobile-back text-end"><span>Kembali</span><i
                                            class="fa-solid fa-angle-right ps-2" aria-hidden="true"></i></div>
                                </li>
                                <li class="pin-title sidebar-main-title">
                                    <div>
                                        <h6>Pinned</h6>
                                    </div>
                                </li>
                                <li class="sidebar-main-title">
                                    <div>
                                        <h6 class="lan-1">General</h6>
                                    </div>
                                </li>
                                <li class="sidebar-list">
                                    <i class="fa-solid fa-thumbtack"></i>
                                    {{-- <label class="badge badge-light-primary">0</label> --}}
                                    <a class="sidebar-link sidebar-title link-nav" href="{{ route('user.home') }}"><svg
                                            class="stroke-icon">
                                            <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                        </svg><svg class="fill-icon">
                                            <use href="{{ asset('assets/svg/icon-sprite.svg#fill-home') }}"></use>
                                        </svg><span class="lan-3">Beranda </span></a>
                                </li>

                                @role('vice-principal')
                                    <li class="sidebar-list">
                                        <i class="fa-solid fa-thumbtack"></i>
                                        <a class="sidebar-link sidebar-title link-nav"
                                            href="{{ route('user.period.index') }}"><svg class="stroke-icon">
                                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-calendar') }}">
                                                </use>
                                            </svg><svg class="fill-icon">
                                                <use href="{{ asset('assets/svg/icon-sprite.svg#fill-calendar') }}"></use>
                                            </svg><span class="lan-3">Periode </span></a>
                                    </li>
                                    <li class="sidebar-list">
                                        <i class="fa-solid fa-thumbtack"></i>
                                        <a class="sidebar-link sidebar-title link-nav"
                                            href="{{ route('user.room.index') }}"><svg class="stroke-icon">
                                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-board') }}"></use>
                                            </svg><svg class="fill-icon">
                                                <use href="{{ asset('assets/svg/icon-sprite.svg#fill-board') }}"></use>
                                            </svg><span class="lan-3">Ruangan </span></a>
                                    </li>
                                    <li class="sidebar-list">
                                        <i class="fa-solid fa-thumbtack"></i>
                                        <a class="sidebar-link sidebar-title link-nav"
                                            href="{{ route('user.class.index') }}"><svg class="stroke-icon">
                                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-user') }}"></use>
                                            </svg><svg class="fill-icon">
                                                <use href="{{ asset('assets/svg/icon-sprite.svg#fill-user') }}"></use>
                                            </svg><span>Kelas</span></a>
                                    </li>
                                @endrole
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>
            <!-- Page Sidebar Ends-->
