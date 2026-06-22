@php
  use App\Helpers\Helper;
@endphp

@props(['content'])

<div class="item d-flex w-100 border rounded-1 flex-column">
  <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse"
    data-bs-target="#material-content-{{ $content->id }}" aria-expanded="false"
    aria-controls="material-content-{{ $content->id }}">
    <div class="row g-0 align-items-center w-100">
      <div class="col-12 col-md-auto">
        <div class="d-flex flex-column align-items-center py-3 px-4">
          <div class="mb-2">
            <img style="width:30px; height:30px" class="theme-aware-icon"
              src="{{ asset('assets/icons/agenda.png') }}" />
          </div>
          <p class="mb-0 text-center">Materi</p>
        </div>
      </div>
      <div class="col flex-grow-1">
        <div class="py-0 pb-3 px-3 px-md-0 py-md-3 me-md-5">
          <p class="fw-medium mb-1">{{ $content->title }}</p>
          <div style="font-size: .8rem;" class="text-secondary">
            <div class="d-flex gap-2">
              <div class="d-flex align-items-center">
                <span class="icon"><i data-feather="calendar" style="width:18px; height: 18px"></i></span>
                <span class="mb-0 ms-2">{{ $content->created_at->translatedFormat('d M Y') }}</span>
              </div>
              <div>&middot;</div>
              <span>
                {{ $content->created_at->translatedFormat('H:i') }} WIT
              </span>
            </div>
          </div>
        </div>
      </div>
      <div class="position-absolute top-0 end-0 col-auto top-middle">
        <div class="p-3 d-flex align-items-center justify-content-center">
          <i class="svg-color position-static" data-feather="chevron-down"></i>
        </div>
      </div>
    </div>
  </button>
  <div class="accordion-collapse collapse w-100" id="material-content-{{ $content->id }}">
    <div class="d-flex justify-content-end gap-2 p-3">
      @if ($content->file_type != 'Archive' && $content->file_type != 'Link')
        @can('material.view')
          <a href="{{ route('user.material.file.download', ['materi_id' => $content->id]) }}"
            style="width: 38px; height: 38px;"
            class="btn d-flex align-items-center bg-20-info border justify-content-center text-info p-2">
            <i data-feather="download" style="width: 20px; height: 20px"></i>
          </a>
        @endcan
      @endif
      @can('material.*')
        <button class="btn d-flex align-items-center bg-20-warning border justify-content-center text-warning p-2"
          style="width: 38px; height: 38px;" onclick="handleEditMaterial(event, {{ $content->id }})">
          <i data-feather="edit-2" style="width: 20px; height: 20px"></i>
        </button>
        <button class="btn d-flex align-items-center bg-20-danger border justify-content-center text-danger p-2"
          style="width: 38px; height: 38px;" onclick="handleDeleteMaterial(event, {{ $content->id }})">
          <i data-feather="trash-2" style="width: 20px; height: 20px"></i>
        </button>
      @endcan
    </div>
    <div class="description mb-3">
      <p class="px-3 mb-0 fw-medium">Deskripsi</p>
      <div class="ql-editor text-wrap h-auto">
        {!! $content->description !!}
      </div>
    </div>
    <div class="view_file_path">
      @switch($content->file_type)
        @case('eBook')
          <div class="eBook">
            @php
              $fileUrl = URL::temporarySignedRoute(
                  'user.material.file.get', // nama route
                  now()->addMinutes(60), // masa berlaku
                  ['materi_id' => $content->id],
              );
              $fileType = Helper::getFileType($content->file_name);
            @endphp
            @if ($fileType == 'image')
              <div class="d-flex justify-content-center align-items-center p-3 h-100">
                <img src="{{ $fileUrl }}" alt="{{ $content->file_name }}"
                  style="max-width: 100%; height: auto; object-fit: contain;">
              </div>
            @elseif (Helper::isGooglePreviewable($content->file_name))
              <iframe src="https://docs.google.com/gview?url={{ urlencode($fileUrl) }}&embedded=true" width="100%"
                height="600px" frameborder="0"></iframe>
            @else
              <iframe src="{{ $fileUrl }}" width="100%" height="600px" frameborder="0"></iframe>
            @endif
          </div>
        @break

        @case('Archive')
          <div class="Archive py-3 px-3 mx-2 rounded-2 d-flex align-items-center flex-column gap-1">
            <div style="display:flex;align-items:center;justify-content:center;min-width:32px;min-height:32px;">
              <i class="fa fa-file text-primary fs-2"></i>
            </div>
            <div class="fw-medium text-break" style="font-size: .8rem">
              {{ $content?->file_name . ' (' . number_format($content?->file_size / (1024 * 1024), 2) . 'mb)' ?? '-' }}
            </div>
            <a href="{{ route('user.material.file.download', ['materi_id' => $content->id]) }}"
              style="width: 38px; height: 38px;"
              class="btn d-flex align-items-center bg-20-info border justify-content-center text-info p-2">
              <i data-feather="download" style="width: 20px; height: 20px"></i>
            </a>
          </div>
        @break

        @case('Link')
          <div class="Link py-3 px-3 mx-2 rounded-2 d-flex align-items-center flex-md-row flex-column gap-2">
            <div class="link d-flex align-items-center gap-2 copy-link" style="cursor:pointer;" title="Salin link"
              tabindex="0" onclick="handleCopyText('{{ $content->file_path }}')">
              <div class="px-2"
                style="display:flex;align-items:center;justify-content:center;min-width:32px;min-height:32px;">
                <i class="fa fa-link text-primary fs-4"></i>
              </div>
              <div class="fw-medium text-break" style="font-size: .8rem">{{ $content->file_path }}</div>
            </div>
            <a href="{{ $content->file_path }}" target="_blank"
              class="btn d-flex align-items-center bg-20-info border justify-content-center text-info p-2">
              <span style="font-size: .8rem" class="text-nowrap me-2">Buka Link</span>
              <i data-feather="external-link" style="width: 20px; height: 20px"></i>
            </a>
          </div>
        @break

        @default
      @endswitch
    </div>

  </div>
</div>
