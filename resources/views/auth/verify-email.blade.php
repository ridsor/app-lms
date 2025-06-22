@extends('layouts.app')

@section('main')
    <main>
        <div class="container-sm bg-white p-0 py-5 wrapper" style="max-width: 700px; margin-top: 100px; margin-bottom: 100px">
            <div class="fs-3 fw-bold mb-0 text-center">Verifikasi</div>
            <hr class="my-3" />
            <div class="px-2 px-md-4 py-4">
                <div class="text-center small text-muted mb-4">
                    Mohon verifikasi email kamu melalu email yang kami kirim padamu.
                </div>
                <div class="text-center small text-muted mb-4">
                    Tidak dapat email?
                </div>
                <form method="post" action="{{ route('verification.send') }}">
                    @csrf
                    <div class="d-flex align-items-center justify-content-center mt-3">
                        <button class="btn btn-primary w-100 btn-lg fw-bold">Kirim Lagi</button>
                    </div>
                </form>

            </div>
        </div>
    </main>
@endsection

@push('head')
    <link href="/assets/css/auth.css" rel="stylesheet" />
@endpush
