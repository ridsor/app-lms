<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\Teacher\StoreTeacherRequest;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;

class TeacherController extends Controller
{
  public function index(Request $request)
  {
    if (!$request->user()->can('viewAny', Teacher::class)) {
      abort(403);
    }

    if ($request->ajax()) {
      $data = Teacher::query()
        ->select(['id', 'name', 'nip', 'specialization', 'created_at'])
        ->filter($request->all());

      return DataTables::of($data)
        ->addColumn('id', function ($row) {
          return '<div class="checkbox-checked"><div class="form-check d-flex justify-content-center align-items-center"><input class="form-check-input select-row" type="checkbox" style="width: 12px; height: 12px;" value="' . $row->id . '" name="selected_ids[]" id="select-row-' . $row->id . '"></div></div>';
        })
        ->addColumn('Nama', fn($row) => '<div class="product-names"><p>' . $row->name . '</p></div>')
        ->addColumn('NIP', fn($row) => '<p class="f-light">' . $row->nip . '</p>')
        ->addColumn('Spesialisasi', fn($row) => '<span class="badge badge-light-primary">' . ($row->specialization ?: '-') . '</span>')
        ->addColumn('Waktu', fn($row) => $row->created_at ? $row->created_at->translatedFormat('d/m/Y H:i') : '-')
        ->addColumn('Aksi', function ($row) {
          $html = '
                  <div class="common-align gap-2 justify-content-start">
                      <a class="square-white view" data-id="' . $row->id . '"><svg>
                          <use href="' . asset('assets/svg/icon-sprite.svg#fill-view') . '">
                          </use>
                      </svg>
                      </a>
                      <a class="square-white edit"  data-id="' . $row->id . '">
                          <svg><use href="' . asset('assets/svg/icon-sprite.svg#edit-content') . '"></use></svg>
                      </a>
                      <a class="square-white trash"  data-id="' . $row->id . '">
                          <svg><use href="' . asset('assets/svg/icon-sprite.svg#trash1') . '"></use></svg>
                      </a>
                  </div>';
          return $html;
        })
        ->rawColumns(['id', 'Nama', 'NIP', 'Spesialisasi', 'Waktu', 'Aksi'])
        ->make(true);
    } else {
      $genders = [
        ['value' => 'M', 'label' => 'Laki-laki'],
        ['value' => 'F', 'label' => 'Perempuan'],
      ];
      $religions = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Budha', 'Konghucu'];
      return view('user.teacher.index', [
        'religions' => $religions,
        'genders' => $genders,
      ]);
    }
  }

  public function store(StoreTeacherRequest $request)
  {
    if (!$request->user()->can('create', Teacher::class)) {
      return abort(403);
    }
    try {
      $validated = $request->validated();
      $validated['date_of_birth'] = \Carbon\Carbon::createFromFormat('d/m/Y', $validated['date_of_birth'])->translatedFormat('Y-m-d');
      DB::beginTransaction();
      $user = User::create(['name' => $validated['name']]);
      $user->assignRole('teacher');
      $validated['user_id'] = $user->id;
      Teacher::create($validated);
      DB::commit();
      return $this->sendResponse('Guru berhasil ditambahkan', [], 201);
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('Error creating teacher: ' . $e->getMessage());
      return $this->sendError('Silakan coba lagi.', [], 500);
    }
  }

  public function show(Request $request, $id)
  {
    try {
      $teacher = Teacher::findOrFail($id);
      if (!$request->user()->can('view', $teacher)) {
        return abort(403);
      }
      $data = [
        'id' => $teacher->id,
        'name' => $teacher->name,
        'nip' => $teacher->nip,
        'specialization' => $teacher->specialization,
        'date_of_birth' => $teacher->date_of_birth ? \Carbon\Carbon::parse($teacher->date_of_birth)->format('d/m/Y') : null,
        'birthplace' => $teacher->birthplace,
        'gender' => $teacher->gender,
        'religion' => $teacher->religion,
        'created_at' => $teacher->created_at ? $teacher->created_at->translatedFormat('d/m/Y H:i') . ' WIT' : null,
      ];
      return $this->sendResponse('Data guru ditemukan', $data);
    } catch (\Exception $e) {
      return $this->sendError('Silakan coba lagi.', [], 500);
    }
  }

  public function edit(Request $request, $id)
  {
    try {
      $teacher = Teacher::findOrFail($id);
      if (!$request->user()->can('update', $teacher)) {
        return abort(403);
      }
      return $this->sendResponse('Data guru ditemukan', $teacher);
    } catch (\Exception $e) {
      Log::error('Error editing teacher: ' . $e->getMessage());
      return $this->sendError('Silakan coba lagi.', [], 500);
    }
  }

  public function update(StoreTeacherRequest $request, $id)
  {
    try {
      $teacher = Teacher::findOrFail($id);
      if (!$request->user()->can('update', $teacher)) {
        return abort(403);
      }
      $validated = $request->validated();
      $validated['date_of_birth'] = \Carbon\Carbon::createFromFormat('d/m/Y', $validated['date_of_birth'])->translatedFormat('Y-m-d');
      DB::beginTransaction();
      $teacher->update($validated);
      $teacher->user->update(['name' => $validated['name']]);
      DB::commit();
      return $this->sendResponse('Guru berhasil diedit', $teacher);
    } catch (\Exception $e) {
      DB::rollBack();
      return $this->sendError('Silakan coba lagi.', [], 500);
    }
  }

  public function destroy(Request $request, $id)
  {
    try {
      $teacher = Teacher::findOrFail($id);
      if (!$request->user()->can('delete', Teacher::class)) {
        return abort(403);
      }
      DB::beginTransaction();
      if ($teacher->user) {
        $teacher->user->delete();
      }
      if ($teacher->schedules()->count() > 0) {
        return $this->sendError(
          'Tidak dapat dihapus karena masih memiliki jadwal.',
          [],
          400
        );
      }
      $teacher->delete();
      DB::commit();
      return $this->sendResponse('Guru berhasil dihapus.');
    } catch (\Exception $e) {
      DB::rollBack();
      return $this->sendError('Silakan coba lagi.', [], 500);
    }
  }

  public function bulkDestroy(Request $request)
  {
    try {
      if (!$request->user()->can('delete', Teacher::class)) {
        return abort(403);
      }
      $ids = $request->input('ids');
      if (empty($ids)) {
        return $this->sendError('Tidak ada data yang dipilih untuk dihapus.', [], 400);
      }
      DB::beginTransaction();
      $teachers = Teacher::whereIn('id', $ids)->get();
      foreach ($teachers as $teacher) {
        if ($teacher->user) {
          $teacher->user->delete();
        }
        if ($teacher->schedules()->count() > 0) {
          return $this->sendError(
            'Tidak dapat dihapus karena masih memiliki jadwal.',
            [],
            400
          );
        }
        $teacher->delete();
      }
      DB::commit();
      return $this->sendResponse('Data yang dipilih berhasil dihapus.');
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('Error bulk destroying teachers: ' . $e->getMessage());
      return $this->sendError('Silakan coba lagi.', [], 500);
    }
  }

  public function exportAccount(Request $request)
  {
    try {
      if (!$request->user()->can('viewAny', Teacher::class)) {
        return abort(403);
      }
      $query = Teacher::query()->select('id', 'name', 'nip', 'specialization', 'date_of_birth', 'birthplace', 'religion', 'user_id');
      $teachers = $query->get();
      $exportData = $teachers->map(function ($teacher, $index) {
        return [
          'No' => $index + 1,
          'Nama' => $teacher->name,
          'NIP' => $teacher->nip,
          'Spesialisasi' => $teacher->specialization,
          'Username' => $teacher->user ? $teacher->user->username : '-',
          'Password' => $teacher->user ? $teacher->user->username : '-',
        ];
      });
      $filename = 'akun-guru.xlsx';
      return (new FastExcel($exportData))->download($filename);
    } catch (\Exception $e) {
      Log::info($e->getMessage());
      return back()->with('error', 'Terjadi kesalahan saat export data: ' . $e->getMessage());
    }
  }
}
