@extends('layouts.app')

@section('title', 'Error 403')

@section('main_content')
    <div class="error-wrapper">
        <div class="container">
            <svg>
                <use href="{{ asset('assets/svg/icon-sprite.svg#error-403') }}"></use>
            </svg>
            <div class="col-md-8 offset-md-2">
                <h3>Forbidden Error</h3>
                <p class="sub-content">Jika Anda menerima kesalahan 403 Forbidden, verifikasi hak akses Anda atau hubungi
                    administrator server untuk mendapatkan otorisasi yang diperlukan.</p>
            </div>
            <div><a class="btn btn-primary btn-lg" href="{{ route('user.home') }}">KEMBALI KE HALAMAN UTAMABACK
                    TO HOME PAGE</a></div>
        </div>
    </div>
@endsection
