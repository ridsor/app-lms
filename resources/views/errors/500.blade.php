@extends('layouts.app')

@section('title', 'Error 404')

@section('main_content')
    <div class="error-wrapper">
        <div class="container">
            <svg>
                <use href="{{ asset('assets/svg/icon-sprite.svg#error-500') }}"></use>
            </svg>
            <div class="col-md-8 offset-md-2">
                <h3>Server Error</h3>
                <p class="sub-content">Server tidak dapat menyelesaikan pemrosesan permintaan Anda karena terjadi kesalahan.
                </p>
            </div>
            <div><a class="btn btn-primary btn-lg" href="{{ route('user.home') }}">KEMBALI KE HALAMAN UTAMAv</a>
            </div>
        </div>
    </div>
@endsection
