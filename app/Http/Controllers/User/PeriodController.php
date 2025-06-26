<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Period;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class PeriodController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:vice-principal']);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Period::filter($request->all());

            return DataTables::of($data)
                ->addColumn('id', function ($row) {
                    $html = '<div class="checkbox-checked"><div class="form-check d-flex justify-content-center align-items-center"><input class="form-check-input select-row" type="checkbox" style="width: 12px; height: 12px;" value="' . $row->id . '" name="selected_ids[]" id="select-row-' . $row->id . '"></div></div>';
                    return $html;
                })
                ->addColumn('Semester', function ($row) {
                    return $row->semester == 'odd' ? 'Ganjil' : 'Genap';
                })
                ->addColumn('Tahun Akademik', function ($row) {
                    return $row->academic_year;
                })
                ->addColumn('Periode', function ($row) {
                    return $row->start_date->translatedFormat('d/m/Y') . ' - ' . $row->end_date->translatedFormat('d/m/Y');
                })
                ->addColumn('Status', function ($row) {
                    return $row->status ? '
                        <a class="period-active" style="cursor: pointer" data-id="' . $row->id . '" data-name="' . ($row->semester == 'odd' ? 'Ganjil' : 'Genap') . ' ' . $row->academic_year . '">
                        <span class="badge bg-success">Aktif</span>
                        </a>'
                        :
                        '<a class="period-inactive" style="cursor: pointer" data-id="' . $row->id . '" data-name="' . ($row->semester == 'odd' ? 'Ganjil' : 'Genap') . ' ' . $row->academic_year . '">
                        <span class="badge bg-secondary">Tidak Aktif</span>
                        </a>';
                })
                ->editColumn('Waktu', function ($row) {
                    return $row->created_at->translatedFormat('d/m/Y H:i');
                })
                ->addColumn('Aksi', function ($row) {
                    $html = '<div class="common-align gap-2 justify-content-start">'
                        . '<a class="square-white edit" style="cursor: pointer" data-id="' . $row->id . '"><svg><use href="' . asset('assets/svg/icon-sprite.svg#edit-content') . '"></use></svg></a>'
                        . '<a class="square-white trash" style="cursor: pointer" data-id="' . $row->id . '"><svg><use href="' . asset('assets/svg/icon-sprite.svg#trash1') . '"></use></svg></a>'
                        . '</div>';
                    return $html;
                })
                ->rawColumns(['id', 'Semester', 'Tahun Akademik', 'Periode', 'Status', 'Waktu', 'Aksi'])
                ->make(true);
        } else {
            $periods = Period::filter(request()->all())->paginate(10);
            return view('user.periods.index', [
                'periods' => $periods
            ]);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'semester' => 'required|in:odd,even',
            'academic_year' => 'required|string|max:15',
            'start_date' => 'required|date_format:d/m/Y',
            'end_date' => 'required|date_format:d/m/Y|after:start_date',
        ]);
        try {
            $validated['start_date'] = Carbon::createFromFormat('d/m/Y', $validated['start_date'])->translatedFormat('Y-m-d');
            $validated['end_date'] = Carbon::createFromFormat('d/m/Y', $validated['end_date'])->translatedFormat('Y-m-d');
            $validated['status'] = true;
            $period = Period::create($validated);
            return $this->sendResponse('Periode berhasil ditambahkan', [
                'id' => $period->id,
                'semester' => $period->semester,
                'academic_year' => $period->academic_year,
                'start_date' => $period->start_date->translatedFormat('d/m/Y'),
                'end_date' => $period->end_date->translatedFormat('d/m/Y'),
                'status' => $period->status,
                'created_at' => $period->created_at->translatedFormat('d/m/Y H:i')
            ], 201);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return $this->sendError('Silakan coba lagi.', [], 500);
        }
    }

    public function show($id)
    {
        try {
            $period = Period::find($id);
            if (!$period) {
                return $this->sendError('Data periode tidak ditemukan.', [], 404);
            }
            return $this->sendResponse('Data periode ditemukan', $period);
        } catch (\Exception $e) {
            return $this->sendError('Silakan coba lagi.', [], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'semester' => 'required|in:odd,even',
            'academic_year' => 'required|string|max:15',
            'start_date' => 'required|date_format:d/m/Y',
            'end_date' => 'required|date_format:d/m/Y|after:start_date',
        ]);
        try {
            $validated['start_date'] = Carbon::createFromFormat('d/m/Y', $validated['start_date'])->translatedFormat('Y-m-d');
            $validated['end_date'] = Carbon::createFromFormat('d/m/Y', $validated['end_date'])->translatedFormat('Y-m-d');
            $period = Period::findOrFail($id);
            $period->update($validated);
            return $this->sendResponse('Periode berhasil diedit', $period);
        } catch (\Exception $e) {
            return $this->sendError('Silakan coba lagi.', [], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $period = Period::findOrFail($id);
            if ($period->schedules()->count() > 0) {
                return $this->sendError('Data ini masih digunakan/referensi oleh entitas lain.', [], 422);
            }
            $period->delete();
            return $this->sendResponse('Periode berhasil dihapus.');
        } catch (\Exception $e) {
            return $this->sendError('Silakan coba lagi.', [], 500);
        }
    }

    public function bulkDestroy(Request $request)
    {
        try {
            $ids = $request->input('ids');
            if (empty($ids)) {
                return $this->sendError('Tidak ada data yang dipilih untuk dihapus.', [], 400);
            }
            $periods = Period::whereIn('id', $ids)->get();
            foreach ($periods as $period) {
                if ($period->schedules()->count() > 0) {
                    return $this->sendError('Beberapa periode masih digunakan dalam jadwal.', [], 422);
                }
            }
            Period::whereIn('id', $ids)->delete();
            return $this->sendResponse('Data yang dipilih berhasil dihapus.');
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return $this->sendError('Silakan coba lagi.', [], 500);
        }
    }

    public function active($id)
    {
        try {
            $period = Period::findOrFail($id);
            $period->update(['status' => true]);
            return $this->sendResponse('Periode berhasil diaktifkan.', $period);
        } catch (\Exception $e) {
            return $this->sendError('Silakan coba lagi.', [], 500);
        }
    }
}
