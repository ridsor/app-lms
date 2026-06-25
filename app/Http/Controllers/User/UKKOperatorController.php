<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Rap2hpoutre\FastExcel\FastExcel;

class UKKOperatorController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->user()->can('operator_ukk.*')) {
            abort(403);
        }

        if ($request->ajax()) {
            $data = User::role('operator')
                ->whereHas('permissions', function($q) {
                    $q->where('name', 'ukk.evaluation');
                })
                ->select(['id', 'name', 'username', 'created_at']);

            return DataTables::of($data)
                ->addColumn('id', function ($row) {
                    return '<div class="checkbox-checked"><div class="form-check d-flex justify-content-center align-items-center"><input class="form-check-input select-row" type="checkbox" style="width: 12px; height: 12px;" value="' . $row->id . '" name="selected_ids[]" id="select-row-' . $row->id . '"></div></div>';
                })
                ->addColumn('Nama', fn($row) => '<div class="product-names"><p>' . $row->name . '</p></div>')
                ->addColumn('Username', fn($row) => '<p class="f-light">' . $row->username . '</p>')
                ->addColumn('Waktu', fn($row) => $row->created_at ? $row->created_at->translatedFormat('d/m/Y H:i') : '-')
                ->addColumn('Aksi', function ($row) {
                    return '
                        <div class="common-align gap-2 justify-content-start">
                            <a class="square-white edit" data-id="' . $row->id . '">
                                <svg><use href="' . asset('assets/svg/icon-sprite.svg#edit-content') . '"></use></svg>
                            </a>
                            <a class="square-white trash" data-id="' . $row->id . '">
                                <svg><use href="' . asset('assets/svg/icon-sprite.svg#trash1') . '"></use></svg>
                            </a>
                        </div>';
                })
                ->rawColumns(['id', 'Nama', 'Username', 'Waktu', 'Aksi'])
                ->make(true);
        }

        return view('user.ukk-operator.index');
    }

    public function store(Request $request)
    {
        if (!$request->user()->can('operator_ukk.*')) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();
            // Generate username otomatis dari nama (menggunakan logic User::generateUsername)
            $username = User::generateUsername($request->name);
            
            $user = User::create([
                'name' => $request->name,
                'username' => $username,
                'password' => Hash::make($username), // Password default sama dengan username
            ]);
            $user->assignRole('operator');
            $user->givePermissionTo('ukk.evaluation');
            DB::commit();

            return $this->sendResponse('Operator UKK berhasil ditambahkan. Username & Password: ' . $username, $user, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating UKK operator: ' . $e->getMessage());
            return $this->sendError('Silakan coba lagi.', [], 500);
        }
    }

    public function edit(Request $request, $id)
    {
        if (!$request->user()->can('operator_ukk.*')) {
            abort(403);
        }

        try {
            $user = User::role('operator')->findOrFail($id);
            return $this->sendResponse('Data operator ditemukan', $user);
        } catch (\Exception $e) {
            return $this->sendError('Data tidak ditemukan.', [], 404);
        }
    }

    public function update(Request $request, $id)
    {
        if (!$request->user()->can('operator_ukk.*')) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'password' => 'nullable|string|min:6',
        ]);

        try {
            $user = User::role('operator')->findOrFail($id);
            DB::beginTransaction();
            $data = [
                'name' => $request->name,
                'username' => $request->username,
            ];
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }
            $user->update($data);
            DB::commit();

            return $this->sendResponse('Operator UKK berhasil diperbarui', $user);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating UKK operator: ' . $e->getMessage());
            return $this->sendError('Silakan coba lagi.', [], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->can('operator_ukk.*')) {
            abort(403);
        }

        try {
            $user = User::role('operator')->findOrFail($id);
            $user->delete();
            return $this->sendResponse('Operator UKK berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error deleting UKK operator: ' . $e->getMessage());
            return $this->sendError('Silakan coba lagi.', [], 500);
        }
    }

    public function bulkDestroy(Request $request)
    {
        if (!$request->user()->can('operator_ukk.*')) {
            abort(403);
        }

        $ids = $request->input('ids');
        if (empty($ids)) {
            return $this->sendError('Tidak ada data yang dipilih.', [], 400);
        }

        try {
            User::role('operator')->whereIn('id', $ids)->delete();
            return $this->sendResponse('Operator yang dipilih berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error bulk deleting UKK operators: ' . $e->getMessage());
            return $this->sendError('Silakan coba lagi.', [], 500);
        }
    }

    public function export(Request $request)
    {
        if (!$request->user()->can('operator_ukk.*')) {
            abort(403);
        }

        try {
            $operators = User::role('operator')
                ->whereHas('permissions', function ($q) {
                    $q->where('name', 'ukk.evaluation');
                })
                ->select('name', 'username')
                ->get();

            $headerRows = [
                ['DAFTAR OPERATOR UKK' => ''],
                [],
                ['No' => 'No', 'Nama' => 'Nama', 'Username' => 'Username', 'Password' => 'Password (Default)'],
            ];

            $exportData = $operators->map(function ($operator, $index) {
                return [
                    'No' => $index + 1,
                    'Nama' => $operator->name,
                    'Username' => $operator->username,
                    'Password (Default)' => $operator->username,
                ];
            })->toArray();

            $filename = 'operator-ukk-' . now()->format('Y-m-d') . '.xlsx';
            $exportData = array_merge($headerRows, $exportData);

            return (new FastExcel($exportData))->download($filename);
        } catch (\Exception $e) {
            Log::error('Error exporting UKK operators: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat export data.');
        }
    }
}
