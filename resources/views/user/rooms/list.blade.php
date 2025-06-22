<div class="cf nestable-lists">
    <div class="dd" id="nestable3">
        <ul class="dd-list">
            @if (isset($rooms))
                @forelse ($rooms as $item)
                    <li class="dd-item dd3-item {{ isset($room) && $room->id == $item->id ? 'active' : '' }}"
                        data-id="{{ $item->id }}">
                        <div class="dd3-content d-flex align-items-center justify-content-between px-3">
                            <span class="room-name text-truncate flex-1">{{ $item->name }}</span>
                            <div class="actions d-flex align-items-center">
                                <a href="#" class="delete-data" data-id="{{ $item->id }}"
                                    data-name="{{ $item->name }}" title="Hapus Ruangan">
                                    <i data-feather="trash-2" class="text-danger"></i>
                                </a>
                                <a href="#" class="edit-data" data-id="{{ $item->id }}" title="Edit Ruangan">
                                    <i data-feather="edit" class="text-warning"></i>
                                </a>
                            </div>
                        </div>
                    </li>
                @empty
                    <div class="no-data-detail text-center">
                        <img class="img-lg" src="{{ asset('assets/svg/no-data-holder.svg') }}" alt="no-data">
                        <div class="data-not-found">
                            <span>Data Ruangan tidak ditemukan</span>
                        </div>
                    </div>
                @endforelse
            @endif
        </ul>
    </div>

    @if (isset($rooms) && $rooms->hasPages())
        <div class="pagination-wrapper mt-3">
            <nav aria-label="Page navigation">
                {{ $rooms->appends(request()->query())->links('pagination::bootstrap-5') }}
            </nav>
        </div>
    @endif
</div>
