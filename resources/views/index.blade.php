@extends('layouts.app')

@section('title', env('SCHOOL_NAME'))

@section('main_content')
    <div class="container-fluid">
        <section class="section-space feature-section">
            <ul class="decoration">
                <li class="round-gif"><img src="{{ asset('assets/images/gif/home-decoration.gif') }}" alt=""></li>
            </ul>
            <div class="container">
                <div class="row">
                    <div class="col-sm-12 wow pulse">
                        <div class="landing-title">
                            <div class="d-flex d-flex gap-2 flex-column align-items-center mb-2">
                                <img class="img-fluid for-light" src="{{ asset('assets/images/logo_sekolah.png') }}"
                                    style="width: 50px" alt=""><img class="img-fluid for-dark"
                                    src="{{ asset('assets/images/logo_sekolah.png') }}" style="width: 50px" alt="">
                            </div>
                            <h5 class="mb-3">{{ env('SCHOOL_NAME') }}</h5>
                            <h2>
                                Sistem <span class="gradient-5">Manajemen Pembelajaran</span></h2>
                        </div>
                        <div class="vector-image"> <img src="{{ asset('assets/images/home-women.svg') }}"
                                alt="vector women">
                        </div>
                    </div>
                </div>
                <div class="row g-4 justify-content-center">
                    <div class="col-xxl-3 col-lg-4 col-sm-6">
                        <a href="{{ route('student.login') }}">
                            <div class="feature-box common-card bg-feature-1 border">
                                <div class="d-flex mb-3 justify-content-center">
                                    <div class="feature-icon mb-2 w-100 h-100"> <img style="height:100px"
                                            src="{{ asset('assets/images/student.svg') }}" alt="">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5>Siswa</h5>
                                    <span class="f-light">
                                        <i class="fa-solid fa-arrow-right fs-4"></i>
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-xxl-3 col-lg-4 col-sm-6">
                        <a href="{{ route('teacher.login') }}">
                            <div class="feature-box common-card bg-feature-2 border">
                                <div class="d-flex mb-3 justify-content-center">
                                    <div class="feature-icon mb-2 w-100 h-100"> <img style="height:100px"
                                            src="{{ asset('assets/images/teacher.svg') }}" alt="">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5>Guru</h5>
                                    <span class="f-light">
                                        <i class="fa-solid fa-arrow-right fs-4"></i>
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-xxl-3 col-lg-4 col-sm-6">
                        <a href="{{ route('parent.login') }}">
                            <div class="feature-box common-card bg-feature-3 border">
                                <div class="d-flex mb-3 justify-content-center">
                                    <div class="feature-icon mb-2 w-100 h-100"> <img style="height:100px"
                                            src="{{ asset('assets/images/parent.svg') }}" alt="">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5>Orangan Tua</h5>
                                    <span class="f-light">
                                        <i class="fa-solid fa-arrow-right fs-4"></i>
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-xxl-3 col-lg-4 col-sm-6">
                        <a href="{{ route('operator.login') }}">
                            <div class="feature-box common-card bg-feature-4 border">
                                <div class="d-flex mb-3 justify-content-center">
                                    <div class="feature-icon mb-2 w-100 h-100"> <img style="height:100px"
                                            src="{{ asset('assets/images/operator.svg') }}" alt="">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5>Operator</h5>
                                    <span class="f-light">
                                        <i class="fa-solid fa-arrow-right fs-4"></i>
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-xxl-3 col-lg-4 col-sm-6">
                        <a href="{{ route('vice-principal.login') }}">
                            <div class="feature-box common-card bg-feature-5 border">
                                <div class="d-flex mb-3 justify-content-center">
                                    <div class="feature-icon mb-2 w-100 h-100"> <img style="height:100px"
                                            src="{{ asset('assets/images/vice-principal.svg') }}" alt="">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5>Wakil Kepala Sekolah</h5>
                                    <span class="f-light">
                                        <i class="fa-solid fa-arrow-right fs-4"></i>
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
