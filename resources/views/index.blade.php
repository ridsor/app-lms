@extends('layouts.app')
@section('main_content')
    <div class="container py-5">
        <h1 class="text-center mb-5">Selamat Datang di Portal LMS</h1>
        <div class="row justify-content-center">
            <div class="col-md-3 mb-4">
                <a href="{{ route('student.login') }}" class="card text-center shadow h-100 text-decoration-none">
                    <div class="card-body">
                        <h5 class="card-title">Login Siswa</h5>
                        <p class="card-text">Masuk sebagai Siswa</p>
                    </div>
                </a>
            </div>
            <div class="col-md-3 mb-4">
                <a href="{{ route('teacher.login') }}" class="card text-center shadow h-100 text-decoration-none">
                    <div class="card-body">
                        <h5 class="card-title">Login Guru</h5>
                        <p class="card-text">Masuk sebagai Guru</p>
                    </div>
                </a>
            </div>
            <div class="col-md-3 mb-4">
                <a href="{{ route('parent.login') }}" class="card text-center shadow h-100 text-decoration-none">
                    <div class="card-body">
                        <h5 class="card-title">Login Orang Tua</h5>
                        <p class="card-text">Masuk sebagai Orang Tua</p>
                    </div>
                </a>
            </div>
            <div class="col-md-3 mb-4">
                <a href="{{ route('vice-principal.login') }}" class="card text-center shadow h-100 text-decoration-none">
                    <div class="card-body">
                        <h5 class="card-title">Login Wakil Kepala Sekolah Bidang Kurikulum</h5>
                        <p class="card-text">Masuk sebagai Wakil Kepala Sekolah Bidang Kurikulum</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
@endsection
