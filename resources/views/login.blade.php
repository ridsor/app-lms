@php
    use App\Helpers\Helper;
@endphp

@extends('layouts.app')

@section('title', 'Login')

@section('main_content')
    <!-- login page start-->
    <div class="container-fluid p-0">
        <div class="row m-0">
            <div class="col-12 p-0">
                <div class="login-card login-dark">
                    <div>
                        <div>
                            <a class="logo" href="index.html"><img class="img-fluid for-light"
                                    src="/assets/images/logo/logo.png" alt="looginpage" /><img class="img-fluid for-dark"
                                    src="/assets/images/logo/logo_dark.png" alt="looginpage" /></a>
                        </div>
                        <div class="login-main">
                            <form class="theme-form" action="{{ route(Helper::getRouteByRole($role) . '.login') }}"
                                method="POST">
                                @csrf
                                <h4>
                                    Masuk sebagai {{ Helper::getRoleLabel($role) }}
                                </h4>
                                <p>Masukkan username & password untuk login</p>
                                <div class="form-group">
                                    <label class="col-form-label" for="username">Username</label><input
                                        class="form-control @error('username') is-invalid @enderror" type="text"
                                        name="username" id="username" placeholder="Masukkan username" autofocus />
                                    @error('username')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group needs-validation">
                                    <label class="col-form-label" for="password">Password</label>
                                    <div class="form-input">
                                        <div class="position-relative @error('password') is-invalid @enderror">
                                            <input class="form-control" type="password" name="password" id="password"
                                                placeholder="*********" />
                                            <div class="show-hide"><span class="show"> </span></div>
                                        </div>
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group mb-0">
                                    <div class="form-check">
                                        <input class="checkbox-primary form-check-input" id="checkbox1" type="checkbox"
                                            name="remember" /><label class="text-muted form-check-label"
                                            for="checkbox1">Ingat saya</label>
                                    </div>
                                    <div class="text-end">
                                        <button
                                            class="btn btn-primary btn-block w-100 mt-3 spinner-btn d-flex align-items-center justify-content-center gap-1"
                                            type="submit">
                                            Masuk
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $('.btn.spinner-btn').click(function(event) {
            event.preventDefault();
            var $btn = $(this);
            $btn.removeClass('btn-block');
            $btn.prop('disabled', true);
            $btn.append(
                '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span>'
            );
            $(this).parents('form')[0].submit();
        });
    </script>
    <script src="/assets/js/login.js"></script>
@endsection
