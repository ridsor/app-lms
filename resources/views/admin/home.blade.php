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
          @role('admin')
            <div class="col-12">
              <div class="card">
                <div class="card-header card-no-border pb-0">
                  <h5>Log Aktivitas Terbaru</h5>
                </div>
                <div class="card-body">
                  <div class="activity-log-main custom-scrollbar">
                    @if ($activities->count() > 0)
                      <div class="table-responsive">
                        <table class="table table-bordless">
                          <thead>
                            <tr>
                              <th>Waktu</th>
                              <th>Pengguna</th>
                              <th>Aksi</th>
                              <th>Deskripsi</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach ($activities as $activity)
                              <tr>
                                <td>
                                  <p class="f-light">{{ $activity->created_at->translatedFormat('d M Y H:i') }}</p>
                                </td>
                                <td>
                                  <div class="d-flex align-items-center gap-2">
                                    <img class="img-30 rounded-circle"
                                      src="{{ optional($activity->causer)->image ? asset('storage/' . optional($activity->causer)->image) : asset('assets/svg/user-placeholder.svg') }}"
                                      alt="">
                                    <p>{{ optional($activity->causer)->name ?? 'Sistem' }}</p>
                                  </div>
                                </td>
                                <td>
                                  <span>
                                    {{ strtoupper($activity->log_name) }}
                                  </span>
                                </td>
                                <td style="min-width: 200px">
                                  <p class="f-light">{{ $activity->description }}</p>
                                </td>
                              </tr>
                            @endforeach
                          </tbody>
                        </table>
                      </div>
                    @else
                      <div class="d-flex justify-content-center mb-3 w-100">
                        <div class="px-4 py-5 d-grid" style="justify-items: center">
                          <img style="width: 120px; height: 120px" src="{{ asset('assets/images/data-empty.png') }}" />
                          <p class="fw-semibold mb-0 text-center">Ups! Data Kosong</p>
                          <p class="mb-0 text-center">Belum ada aktivitas yang tercatat.</p>
                        </div>
                      </div>
                    @endif
                  </div>
                </div>
              </div>
            </div>
          @endrole
        </div>
      </div>
    </div>
  </div>
@endsection
