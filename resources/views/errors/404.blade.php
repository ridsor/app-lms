@extends('layouts.app')

@section('title', 'Error 404')

@section('main_content')
    <div class="error-wrapper">
        <div class="container">
            <svg>
                <use href="{{ asset('assets/svg/icon-sprite.svg#error-404') }}"></use>
            </svg>
            <div class="col-md-8 offset-md-2">
                <h3>Kami Tidak Dapat Menemukan Halaman Ini</h3>
                <p class="sub-content">Anda mungkin tidak dapat menemukan halaman yang Anda cari, atau mungkin telah
                    dipindahkan atau diganti namanya.</p>
            </div>
            <div><a class="btn btn-primary btn-lg mb-4" href="{{ route('user.home') }}">KEMBALI KE HALAMAN UTAMA</a></div>
        </div>
    </div>
@endsection
