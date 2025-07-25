@php
  use App\Helpers\Helper;
@endphp

<div class="position-relative overflow-auto custom-scrollbar meeting-sidebar" id="my-sticky">
  <div class="d-flex flex-column" id="meeting-sidebar">
    @foreach ($schedule->meetings as $key => $value)
      <a href="{{ route('user.schedule.showByMeeting', ['code' => $schedule->subject->code, 'meeting_id' => $value->id]) }}"
        data-meeting-id="{{ $value->id }}"
        class="p-3 w-100 border-bottom selected-item  {{ isset($meeting) ? ($meeting->id == $value->id ? 'active' : '') : '' }}">
        <p class="fw-medium mb-2">Pertemuan {{ $key + 1 }} </p>
        <div class="d-flex flex-wrap gap-1">
          <span
            class="badge m-0 badge-light-secondary px-2 py-1">{{ Helper::getMeetingMethodLabel($value->schedule_time->meeting_method) }}</span>
          <span class="badge m-0 badge-light-primary px-2 py-1" id="status">{{ $value->status }}</span>
          <span class="badge m-0 badge-light-warning px-2 py-1">{{ Helper::getMeetingTypeLabel($value->type) }}</span>
        </div>
      </a>
    @endforeach
  </div>
</div>
