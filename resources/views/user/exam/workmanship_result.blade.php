@extends('layouts.user.app')

@section('title', 'Hasil Ujian')

@section('styles')
    <style>
        .wrapper-result {
            background: #eee;
        }

        .dark-only .wrapper-result {
            background: #1d1e26 !important;
        }

        .progress-bar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>

@endsection

@section('main_content')
    <div class="container-fluid p-0">
        <div class="page-title">
            <div class="row p-2 p-sm-0">
                <div class="col-sm-6">
                    <h3>Ujian</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('user.home') }}"> <svg class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('user.exam.index') }}">
                                Ujian
                            </a>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid e-category p-0">
        <div class="card h-100 my-0 rounded-responsive mb-3">
            <div class="card-body p-3">
                <h1 class="text-center mb-3">Hasil Ujian</h1>
                <div class="wrapper-result p-3 mb-3">
                    <div class="text-center mb-3">
                        <p class="mb-0 fs-5">Anda memperoleh <span
                                class="fw-medium txt-primary">{{ $examResult->formatted_score }} poin</span></p>
                    </div>
                    @php
                        $percentage =
                            $exam->questions_count > 0
                                ? ($examResult->correct_answers_count / $exam->questions_count) * 100
                                : 0;
                    @endphp
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <div class="progress-bar position-relative border border-secondary-subtle"
                            style="background: conic-gradient(#6a5acd {{ $percentage * 100 }}%, #eee 0);">
                            <div class="position-absolute top-0 wrapper-result rounded-circle position-center"
                                style="width:90%;height:90%; background:#eee">
                                <div class="d-flex align-items-center justify-content-center" style="height: 100%;">
                                    <div id="progress-text" class="fs-3"><span
                                            class="fw-bold">{{ $examResult->correct_answers_count }}</span><span
                                            class="fs-6">/{{ $exam->questions_count }}</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <p class="mb-0">Anda menjawab {{ $examResult->correct_answers_count }} dari
                            {{ $exam->questions_count }} pertanyaan</p>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-12">
                        <div class="col-12">
                            <label class="form-label">Judul</label>
                            <p class="c-o-light f-w-600">
                                <span>
                                    {{ $exam->title }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="col-12">
                        <div>
                            <label class="form-label">Deskripsi</label>
                        </div>
                        @if ($exam?->description)
                            <div class="ql-editor text-wrap h-auto p-0">
                                {!! $exam?->description !!}
                            </div>
                        @else
                            <span>-</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
