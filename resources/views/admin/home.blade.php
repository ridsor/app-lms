@php
    use App\Helpers\Helper;
@endphp

@extends('layouts.admin.app')

@section('title', 'Beranda')

@section('main_content')
    <div class="container-fluid dashboard-3 pt-3">
        <div class="row g-2">
            <div class="col-12">
                <div class="row">
                    <div class="col-12">
                        <div class="card o-hidden welcome-card">
                            <div class="card-body" style="min-height: 138px">
                                <h4 class="mb-3 mt-1 f-w-500 mb-0 f-22">Hi {{ request()->user()->name }} <span> <img
                                            src="{{ asset('assets/icons/hand.svg') }}" alt="hand vector"></span>
                                </h4>
                                <p>Selamat Datang</p>
                            </div><img class="welcome-img" src="{{ asset('assets/icons/widget.svg') }}" alt="search image">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
