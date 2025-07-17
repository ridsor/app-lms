@php
  use App\Helpers\Helper;
@endphp

@extends('layouts.user.app')

@section('title', 'Jadwal')

@section('main_content')
  <div class="container-fluid">
    <div class="page-title">
      <div class="row">
        <div class="col-sm-6">
          <h3>Jadwal</h3>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('user.home') }}"> <svg class="stroke-icon">
                  <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                </svg></a></li>
            <li class="breadcrumb-item active">Jadwal</li>
          </ol>
        </div>
      </div>
    </div>
    <div class="container-fluid e-category">
      <div class="row">
        <div class="col-sm-12 px-0">
          <div class="card">
            <div class="card-body projects-wrapper">
              <div class="tab-content" id="top-tabContent">
                <div class="tab-pane fade show active" id="top-home" role="tabpanel" aria-labelledby="top-home-tab">
                  <div class="row g-4">
                    @foreach ($schedules as $schedule)
                      <div class="col-xxl-3 col-md-6 col-ed-4 box-col-6">
                        <a href="/s">
                          <div class="progress-project-box">
                            <div class="list-box title-line-primary">
                              <div class="header-top"><span
                                  class="badge badge-light-primary">{{ $schedule->meeting_await }}</span>
                              </div>
                              <div class="project-body">
                                <div class="common-f-start gap-3">
                                  <div>
                                    <h6 class="mb-2">
                                      <span class="text-capitalize">
                                        {{ $schedule->subject->name }}
                                      </span>
                                    </h6>
                                    <div class="d-flex gap-2 flex-wrap">
                                      <div>
                                        <span class="fw-medium text-nowrap w-fit">
                                          {{ $schedule->class->name }} -
                                          {{ $schedule->class->level }}{{ $schedule->class->major ? ' - ' . $schedule->class->major->name : '' }}
                                        </span>
                                      </div>
                                      <div style="max-width: fit-content">&middot;</div>
                                      <div>
                                        <span class="text-nowrap">{{ $schedule->class->students_count }} Siswa</span>
                                      </div>
                                    </div>
                                    <div class="d-flex flex-column">
                                      @foreach ($schedule->days_time as $day_time)
                                        <div class="d-flex gap-2">
                                          <div class="col d-flex align-items-center">
                                            <i class="fa-solid fa-calendar"></i>
                                            <span class="mb-0 ms-2">{{ Helper::getDayName($day_time['day']) }}</span>
                                          </div>
                                          <div class="col">&middot;</div>
                                          <span>
                                            {{ $day_time['start_time'] }} - {{ $day_time['end_time'] }} WIT
                                          </span>
                                        </div>
                                      @endforeach
                                    </div>
                                  </div>
                                </div>
                                <div class="project-bottom common-space">
                                  <div class="d-flex flex-column gap-1">
                                    <p class="mb-0">Guru Pengajar</p>
                                    <p class="mb-0 fw-semibold">{{ $schedule->teacher->name }}</p>
                                  </div>
                                  <img class="rounded-circle common-circle" style="width: 30px; height: 30px;"
                                    src="{{ $schedule->teacher->user->image ? asset('storage/' . $schedule->teacher->user->image) : asset('assets/svg/user-placeholder.svg') }}"
                                    alt="user">
                                </div>
                              </div>
                            </div>
                          </div>
                        </a>
                      </div>
                    @endforeach
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
