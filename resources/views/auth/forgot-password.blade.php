@extends('layouts.app')

@section('main')
    <main>
        <div class="container-sm bg-white p-0 py-5 wrapper" style="max-width: 700px; margin-top: 100px; margin-bottom: 100px">
            <div class="fs-3 fw-bold mb-0 text-center">Pemulihan Kata Sandi</div>
            <hr class="my-3" />
            <div class="px-2 px-md-4 py-4">
                <div class="text-center small text-muted mb-4">
                    Masukkan alamat email Anda di bawah ini dan kami
                    akan mengirimkan tautan untuk mengatur ulang kata sandi Anda.
                </div>
                @if (Session::has('error'))
                    <div class="alert alert-danger" role="alert">
                        {{ Session::get('error') }}
                    </div>
                @endif
                @if (Session::has('status'))
                    <div class="alert alert-success" role="alert">
                        {{ Session::get('status') }}
                    </div>
                @endif
                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <!-- Form Row-->
                    <div class="row gx-3">
                        <!-- Form Group (email address)-->
                        <div class="mb-3">
                            <label for="email" class="form-label visually-hidden">Email</label>
                            <input class="form-control form-control-solid @error('email') is-invalid @enderror"
                                type="text" placeholder="Email" id="email" name="email" value="{{ old('email') }}"
                                placeholder="Password" aria-label="Email" />
                            <div class="invalid-feedback">
                                @error('email')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <!-- Form Group (form submission)-->
                        <div class="d-flex align-items-center justify-content-center mt-3">
                            <button class="btn btn-primary w-100 btn-lg fw-bold">Kirim</button>
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
