@php
    use App\Helpers\Helper;
@endphp

@extends(request()->user()->hasRole('admin') ? 'layouts.admin.app' : 'layouts.user.app')


@section('title', $user->name)

@section('styles')
    <style>
        .show-hide-change-password {
            position: absolute;
            top: 50% !important;
            right: 20px;
            transform: translateY(-50%);
        }

        .show-hide-change-password span {
            cursor: pointer;
            font-size: 13px;
            color: var(--theme-default);
        }

        .show-hide-change-password span.show:before {
            content: "\f06e";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            color: #999999;
        }

        .show-hide-change-password span:before {
            content: "\f070";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            color: #999999;
        }

        .needs-validation .show-hide-change-password {
            right: 30px;
        }
    </style>
@endsection

@section('main_content')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6">
                    <h3>Akun</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a
                                href="{{ route(request()->user()->hasRole('admin') ? 'admin.home' : 'user.home') }}"> <svg
                                    class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item active">Akun</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid p-0">
        <div class="user-profile">
            <div class="row g-0"><!-- user profile first-style start-->
                <div class="col-sm-12 mb-3">
                    <div class="card rounded-responsive hovercard text-center common-user-image"
                        style="background: linear-gradient(103.75deg,#33B1EE -13.9%,var(--theme-default) 79.68%)">
                        <div class="cardheader" style="height:300px">
                            <div class="user-image">
                                <div class="avatar">
                                    <div class="common-align">
                                        <div class="bg-light"><img id="output"
                                                style="aspect-ratio: 1/1 !important; object-fit: cover; min-height: 120px; min-width: 120px"
                                                src="{{ auth()->user()->image ? asset('storage/' . auth()->user()->image) : asset('assets/svg/user-placeholder.svg') }}"
                                                alt="Profile Image"><input type="file"
                                                accept="image/png, image/jpg, image/jpeg" onchange="loadFile(event)">
                                            <div class="icon-wrapper" id="cancelButton"><i
                                                    class="icofont icofont-error"></i></div>
                                            <div class="icon-wrapper"><i class="icofont icofont-pencil-alt-5"></i></div>
                                        </div>
                                        <div class="user-designation"><a target="_blank"
                                                href="">{{ $user->name }}</a>
                                            <div class="desc">
                                                {{ Helper::getRoleLabel($user->getRoleNames()->first()) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @role('teacher')
                    <div class="col-12">
                        <div class="card user-bio rounded-responsive">
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                        <div class="ttl-info text-start">
                                            <h6>NIP</h6>
                                            <span>{{ $user->teacher->nip }}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                        <div class="ttl-info text-start">
                                            <h6>Spesialisasi</h6>
                                            <span>{{ $user->teacher->specialization }}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                        <div class="ttl-info text-start pb-0">
                                            <h6>Tempat & Tanggal Lahir</h6>
                                            <span>{{ $user->teacher->birthplace }},
                                                {{ $user->teacher->date_of_birth->translatedFormat('d F Y') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                        <div class="ttl-info text-start">
                                            <h6>Jenis Kelamin</h6>
                                            <span>{{ Helper::getGenderLabel($user->teacher->gender) }}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                        <div class="ttl-info text-start">
                                            <h6>Agama</h6>
                                            <span>{{ $user->teacher->religion }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endrole
                @role('student')
                    <div class="col-12">
                        <div class="card user-bio rounded-responsive">
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                        <div class="ttl-info text-start">
                                            <h6>NISN</h6>
                                            <span>{{ $user->student->nisn }}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                        <div class="ttl-info text-start">
                                            <h6>NIS</h6>
                                            <span>{{ $user->student->nis }}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                        <div class="ttl-info text-start pb-0">
                                            <h6>Tempat & Tanggal Lahir</h6>
                                            <span>{{ $user->student->birthplace }},
                                                {{ $user->student->date_of_birth->translatedFormat('d F Y') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                        <div class="ttl-info text-start">
                                            <h6>Jenis Kelamin</h6>
                                            <span>{{ Helper::getGenderLabel($user->student->gender) }}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                        <div class="ttl-info text-start">
                                            <h6>Agama</h6>
                                            <span>{{ $user->student->religion }}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                        <div class="ttl-info text-start">
                                            <h6>Tahun Masuk</h6>
                                            <span>{{ $user->student->admission_year }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endrole
                <div class="col-12">
                    <div class="row scope-bottom-wrapper user-profile-wrapper">
                        {{-- <div class="col-12">
              <div class="card">
                <div class="card-body">
                  <ul class="sidebar-left-icons nav nav-pills d-flex" id="add-product-pills-tab" role="tablist">
                    <li class="nav-item"> <a class="nav-link active" id="security-tab" data-bs-toggle="pill"
                        href="#security" role="tab" aria-controls="security" aria-selected="false">
                        <div class="nav-rounded">
                          <div class="product-icons"><i class="fa-solid fa-shield"></i></div>
                        </div>
                        <div class="product-tab-content">
                          <h6>Keamanan</h6>
                        </div>
                      </a></li>
                  </ul>
                </div>
              </div>
            </div> --}}
                        <div class="col-12">
                            <div class="row">
                                <div class="col-12">
                                    <div class="tab-content" id="add-product-pills-tabContent">
                                        <div class="tab-pane fade show active" id="security" role="tabpanel"
                                            aria-labelledby="security-tab">
                                            <div class="notification">
                                                <div class="card rounded-responsive">
                                                    <div class="card-header">
                                                        <h5>Keamanan</h5>
                                                    </div>
                                                    <div class="card-body dark-timeline">
                                                        <div>
                                                            <h6 class="mb-2 txt-primary">Ubah Sandi</h6>
                                                            <form class="custom-input" id="change-password"
                                                                onsubmit="return handleChangePassword(event)">
                                                                <div class="form-group mb-2">
                                                                    <label class="col-form-label">Kata Sandi Saat
                                                                        Ini</label>
                                                                    <div class="form-input position-relative">
                                                                        <input class="form-control password" type="password"
                                                                        autocomplete="off"
                                                                            name="current_password">
                                                                        <div class="show-hide-change-password"><span
                                                                                class="show"></span></div>
                                                                    </div>
                                                                    <div class="invalid-feedback">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group mb-2">
                                                                    <label class="col-form-label">Kata Sandi
                                                                        Baru</label>
                                                                    <div class="form-input position-relative">
                                                                        <input class="form-control password"
                                                                            type="password" name="password" autocomplete="off">
                                                                        <div class="show-hide-change-password"><span
                                                                                class="show"></span></div>
                                                                    </div>
                                                                    <div class="invalid-feedback">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group mb-2">
                                                                    <label class="col-form-label">Konfirmasi Kata
                                                                        Sandi</label>
                                                                    <div class="form-input position-relative">
                                                                        <input class="form-control password"
                                                                            type="password" name="password_confirmation" autocomplete="off">
                                                                        <div class="show-hide-change-password"><span
                                                                                class="show"></span></div>
                                                                    </div>
                                                                    <div class="invalid-feedback">
                                                                    </div>
                                                                </div>
                                                                <div class="form-footer mt-3">
                                                                    <button class="btn btn-primary btn-block"
                                                                        name="submit">Simpan</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- user profile menu end-->
            </div>
        </div>
    </div><!-- Container-fluid Ends-->
@endsection

@section('scripts')
    {{-- <script src="{{ asset('assets/js/counter/custom-counter1.js') }}"></script>
  <script src="{{ asset('assets/js/tooltip-init.js') }}"></script> --}}
    <script>
        const username = "{{ $user->username }}";

        $("#change-password").on("click", ".show-hide-change-password span", function(e) {
            if ($(this).hasClass("show")) {
                $(this).removeClass("show");
                $(this)
                    .parent()
                    .parent()
                    .find('input.password')
                    .attr("type", "text");
            } else {
                $(this)
                    .parent()
                    .parent()
                    .find('input.password')
                    .attr("type", "password");
                $(this).addClass("show");
            }
        });
        $('#change-password').on("submit", function() {
            $(".show-hide-change-password span").addClass("show");
            $(".show-hide-change-password")
                .parent()
                .parent()
                .find('input.password')
                .attr("type", "password");
        });
    </script>
    <script src="{{ asset('assets/js/profile.js') }}"></script>
@endsection
