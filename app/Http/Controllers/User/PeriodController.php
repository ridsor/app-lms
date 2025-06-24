<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Period;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class PeriodController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:vice-principal']);
    }

    public function index()
    {

        $periods = Period::select('id', 'semester', 'academic_year', 'start_date', 'end_date', 'status')->filter(request()->all())->paginate(5)->onEachSide(1);
        return view('user.periods.index', [
            'periods' => $periods
        ]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'semester' => 'required|in:odd,even',
            'academic_year' => 'required|string|max:15',
            'start_date' => 'required|date_format:d/m/Y',
            'end_date' => 'required|date_format:d/m/Y|after:start_date',
            'status' => 'boolean'
        ]);

        try {
            $validated['start_date'] = Carbon::createFromFormat('d/m/Y', $validated['start_date'])->translatedFormat('Y-m-d');
            $validated['end_date'] = Carbon::createFromFormat('d/m/Y', $validated['end_date'])->translatedFormat('Y-m-d');
            $validated['status'] = true;
            $period = Period::create($validated);

            return $this->sendResponse('Periode berhasil ditambahkan.', $period);
        } catch (\Exception $e) {
            return $this->sendError(
                'Silakan coba lagi.',
                [],
                500
            );
        }
    }

    public function show($id)
    {
        try {
            $period = Period::find($id);
            if (!$period) {
                return $this->sendError(
                    'Data periode tidak ditemukan.',
                    [],
                    404
                );
            }
            return $this->sendResponse('Data periode berhasil diambil.', $period);
        } catch (\Exception $e) {
            return $this->sendError(
                'Silakan coba lagi.',
                [],
                500
            );
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'semester' => 'required|in:odd,even',
            'academic_year' => 'required|string|max:15',
            'start_date' => 'required|date_format:d/m/Y',
            'end_date' => 'required|date_format:d/m/Y|after:start_date',
            'status' => 'boolean'
        ]);

        try {
            $validated['start_date'] = Carbon::createFromFormat('d/m/Y', $validated['start_date'])->translatedFormat('Y-m-d');
            $validated['end_date'] = Carbon::createFromFormat('d/m/Y', $validated['end_date'])->translatedFormat('Y-m-d');
            $period = Period::findOrFail($id);
            $period->update($validated);

            return $this->sendResponse('Periode berhasil diedit.', $period);
        } catch (\Exception $e) {
            return $this->sendError(
                'Silakan coba lagi.',
                [],
                500
            );
        }
    }

    public function destroy($id)
    {
        try {
            $period = Period::findOrFail($id);

            // Cek apakah periode memiliki jadwal terkait
            if ($period->schedules()->count() > 0) {
                return $this->sendError(
                    'Data ini masih digunakan/referensi oleh entitas lain.',
                    [],
                    422
                );
            }

            $period->delete();

            return $this->sendResponse('Periode berhasil dihapus.');
        } catch (\Exception $e) {
            return $this->sendError(
                'Silakan coba lagi.',
                [],
                500
            );
        }
    }

    public function active($id)
    {
        try {
            $period = Period::findOrFail($id);
            $period->update(['status' => true]);

            return $this->sendResponse('Periode berhasil diaktifkan.', $period);
        } catch (\Exception $e) {
            return $this->sendError(
                'Silakan coba lagi.',
                [],
                500
            );
        }
    }
}
