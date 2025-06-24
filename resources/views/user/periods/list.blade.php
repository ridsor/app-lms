<div class="cf nestable-lists">
    <div class="dd" id="nestable3">
        <ul class="dd-list">
            @if (isset($periods))
                @forelse ($periods as $item)
                    <li class="dd-item dd3-item position-relative {{ isset($period) && $period->id == $item->id ? 'active' : '' }}"
                        data-id="{{ $item->id }}">
                        <div class="dd3-content d-flex align-items-sm-center justify-content-between px-3">
                            <div class="period-info col-12 flex-1 col-md">
                                <div class="period-name text-truncate">
                                    <strong>{{ $item->semester == 'odd' ? 'Ganjil' : 'Genap' }} -
                                        {{ $item->academic_year }}</strong>
                                </div>
                                <div class="period-dates text-muted small">
                                    {{ $item->start_date->translatedFormat('d/m/Y') }} -
                                    {{ $item->end_date->translatedFormat('d/m/Y') }}
                                </div>
                                <div class="period-status">
                                    @if ($item->status)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Tidak Aktif</span>
                                    @endif
                                </div>
                            </div>
                            <div class="actions d-flex align-items-center col-12 col-md-auto">
                                <a href="#" class="delete-data position-relative z-2"
                                    data-id="{{ $item->id }}"
                                    data-name="{{ $item->semester == 'odd' ? 'Ganjil' : 'Genap' }} - {{ $item->academic_year }}"
                                    title="Hapus Periode">
                                    <i data-feather="trash-2" class="text-danger"></i>
                                </a>
                                <a href="#" class="edit-data position-relative z-2" data-id="{{ $item->id }}"
                                    title="Edit Periode">
                                    <i data-feather="edit" class="text-warning"></i>
                                </a>
                                <a href="" class="active-data position-absolute top-0 start-0 end-0 bottom-0 z-1"
                                    data-id="{{ $item->id }}"
                                    data-name="{{ $item->semester == 'odd' ? 'Ganjil' : 'Genap' }} - {{ $item->academic_year }}"></a>
                            </div>
                        </div>
                    </li>
                @empty
                    <div class="no-data-detail text-center">
                        <img class="img-lg" src="{{ asset('assets/svg/no-data-holder.svg') }}" alt="no-data">
                        <div class="data-not-found">
                            <span>Data Periode tidak ditemukan</span>
                        </div>
                    </div>
                @endforelse
            @endif
        </ul>
    </div>

    @if (isset($periods) && $periods->hasPages())
        <div class="pagination-wrapper mt-3">
            <nav aria-label="Page navigation">
                {{ $periods->appends(request()->query())->links('pagination::bootstrap-5') }}
            </nav>
        </div>
    @endif
</div>
