@props(['content'])

<div class="item w-100 border rounded-1 position-relative" tabindex="0">
    @can('material.*')
        <div class="d-flex gap-2 p-3 position-absolute end-0 top-0 action_meeting_text">
            <div class="bg-white">
                <button onclick="handleEditMeetingText(event, {{ $content->id }})"
                    class="edit btn d-flex align-items-center bg-20-warning border justify-content-center text-warning p-2"
                    style="width: 38px; height: 38px;">
                    <i data-feather="edit-2" style="width: 20px; height: 20px"></i>
                </button>
            </div>
            <div class="bg-white">
                <button onclick="handleDeleteMeetingText(event, {{ $content->id }})" style="width: 38px; height: 38px;"
                    class="btn d-flex align-items-center bg-20-danger border justify-content-center text-danger p-2">
                    <i data-feather="trash-2" style="width: 20px; height: 20px"></i>
                </button>
            </div>
        </div>
    @endcan
    <div class="meeting-text ql-editor h-auto text-wrap py-4">
        {!! $content->text !!}
    </div>
</div>
