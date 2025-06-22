@extends('layouts.app')

@section('main')
    <main>
        <div class="container-sm bg-white p-0 py-5 wrapper" style="max-width: 700px; margin-top: 100px; margin-bottom: 100px">
            <div class="fs-3 fw-bold mb-0 text-center">Setel Ulang Kata Sandi</div>
            <hr class="my-3" />
            <div class="px-2 px-md-4 py-4">
                <div class="text-center small text-muted mb-4">
                    Buat kata sandi yang kuat untuk akun dengan e-mail
                    {{ $request->email }}
                </div>
                @if (Session::has('error'))
                    <div class="alert alert-danger" role="alert">
                        {{ Session::get('error') }}
                    </div>
                @endif
                <form method="POST" action="{{ route('password.store') }}">
                    @csrf
                    <!-- Form Row-->
                    <div class="row gx-3">
                        <!-- Form Group (email address)-->
                        <div class="mb-3">
                            <label for="email" class="form-label visually-hidden">Email</label>
                            <input class="form-control form-control-solid @error('email') is-invalid @enderror"
                                type="text" placeholder="Email" id="email" name="email"
                                value="{{ old('email', $request->email) }}" placeholder="Password" aria-label="Email" />
                            <div class="invalid-feedback">
                                @error('email')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>
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
                        <!-- Form Group (form submission)-->
                        <div class="d-flex align-items-center justify-content-center mt-3">
                            <button class="btn btn-primary w-100 btn-lg fw-bold">Simpan</button>
                        </div>
                </form>
            </div>
        </div>
        <hr />
        <div class="px-2 py-3">
            <div class="small text-center">
                Pengguna baru?
                <a href="{{ route('register') }}">Buat akun!</a>
            </div>
        </div>
    </main>
@endsection

@push('head')
    <link href="/assets/css/auth.css" rel="stylesheet" />
@endpush
