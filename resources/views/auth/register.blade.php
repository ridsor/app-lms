@extends('layouts.app')

@section('main')
    <main>
        <div class="container-sm bg-white py-5 px-md-4 wrapper"
            style="max-width: 700px; margin-top: 100px; margin-bottom: 100px">
            <div class="logo d-flex align-items-center justify-content-center mb-4">
                <!-- Uncomment the line below if you also wish to use an image logo -->
                <img src="/assets/img/logo.png" style="height: 80px" alt="Logo">
            </div>
            @if (session()->has('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session()->get('error') }}
                </div>
            @endif
            @if (session()->has('success'))
                <div class="alert alert-info" role="alert">
                    {!! session()->get('success') !!}
                </div>
            @endif
            <form method="POST" action="{{ route('register.post') }}">
                @csrf
                <!-- Form Row-->
                <div class="row gx-3">
                    <!-- Form Group (name)-->
                    <div class="mb-3">
                        <label for="email" class="form-label visually-hidden">Nama</label>
                        <input class="form-control form-control-solid @error('name') is-invalid @enderror" type="text"
                            placeholder="Nama" name="name" value="{{ old('name') }}" aria-label="Name" />
                        <div class="invalid-feedback">
                            @error('name')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>
                    <!-- Form Group (email address)-->
                    <div class="mb-3">
                        <label for="email" class="form-label visually-hidden">Email</label>
                        <input class="form-control form-control-solid @error('email') is-invalid @enderror" type="text"
                            placeholder="Email" id="email" name="email" value="{{ old('email') }}"
                            placeholder="Password" aria-label="Email" />
                        <div class="invalid-feedback">
                            @error('email')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>
                    <!-- Form Row-->
                    <div>
                        <div class="row px-0">
                            <div class="col-md-6">
                                <!-- Form Group (choose password)-->
                                <div class="mb-3" x-data="{ show: false }">
                                    <label for="password" class="form-label visually-hidden">Password</label>
                                    <div class="position-relative @error('password') is-invalid @enderror">
                                        <input class="form-control form-control-solid" :type="show ? 'text' : 'password'"
                                            id="password" name="password" placeholder="Password" aria-label="Password" />

                                        <button type="button" @click="show = !show"
                                            class="position-absolute end-0 translate-middle-y me-2 border-0 bg-transparent top-50">
                                            <!-- Icon mata terbuka -->
                                            <i x-show="!show" class="bi bi-eye-fill"></i>
                                            <!-- Icon mata tertutup -->
                                            <i x-show="show" class="bi bi-eye-slash-fill"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback">
                                        @error('password')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Form Group (confirm password)-->
                                <div class="mb-3" x-data="{ show: false }">
                                    <label for="password_confirmation" class="form-label visually-hidden">Konfirmasi
                                        Password</label>
                                    <div class="position-relative @error('password') is-invalid @enderror">
                                        <input class="form-control form-control-solid" id="password_confirmation"
                                            :type="show ? 'text' : 'password'" name="password_confirmation"
                                            placeholder="Konfirmasi Password" aria-label="Confirm Password" />

                                        <button type="button" @click="show = !show"
                                            class="position-absolute end-0 translate-middle-y me-2 border-0 bg-transparent top-50">
                                            <!-- Icon mata terbuka -->
                                            <i x-show="!show" class="bi bi-eye-fill"></i>
                                            <!-- Icon mata tertutup -->
                                            <i x-show="show" class="bi bi-eye-slash-fill"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback">
                                        @error('password_confirmation ')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Form Group (form submission)-->
                    <div class="d-flex align-items-center justify-content-center mt-3">
                        <button class="btn btn-primary w-100 btn-lg fw-bold">Daftar</button>
                    </div>
            </form>

            <div class="divider" aria-label="Alternative sign up options">
                atau
            </div>

            <div class="social-login d-flex justify-content-center">
                <a class="btn btn-icon btn-google mx-1 text-white" href="{{ route('auth.google.redirect') }}"><i
                        class="fab fa-google fa-fw fa-sm"></i></a>
            </div>

            <div class="px-2 mt-5">
                <div class="small text-center">
                    Punya akun?
                    <a href="{{ route('login') }}">Masuk!</a>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('head')
    <link href="/assets/css/auth.css" rel="stylesheet" />
@endpush
