<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Curriculum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class SubjectController extends Controller
{
  public function index(Request $request, $curriculum_id)
  {
    if (!$request->user()->can('subject.*')) {
      return abort(403);
    }

    $curriculum = Curriculum::findOrFail($curriculum_id);

    if ($request->ajax()) {
      $data = Subject::where('curriculum_id', $curriculum_id)->filter($request->all())
        ->select([
          'id',
          'code',
          'name',
          'created_at',
        ]);

      return DataTables::of($data)
        ->addColumn('id', function ($row) {
          return '<div class="checkbox-checked"><div class="form-check d-flex justify-content-center align-items-center"><input class="form-check-input select-row" type="checkbox" style="width: 12px; height: 12px;" value="' . $row->id . '" name="selected_ids[]" id="select-row-' . $row->id . '"></div></div>';
        })
        ->addColumn('Kode', fn($row) => '<p class="f-light">' . $row->code . '</p>')
        ->addColumn('Nama', fn($row) => '<div class="product-names"><p>' . $row->name . '</p></div>')
        ->addColumn('Waktu', fn($row) => $row->created_at ? \Carbon\Carbon::parse($row->created_at)->translatedFormat('d/m/Y H:i') : '-')
        ->addColumn('Aksi', function ($row) {
          $html = '<div class="common-align gap-2 justify-content-start">';
          $html .= '<a class="square-white edit" data-id="' . $row->id . '"><svg><use href="' . asset('assets/svg/icon-sprite.svg#edit-content') . '"></use></svg></a>';
          $html .= '<a class="square-white trash" data-id="' . $row->id . '"><svg><use href="' . asset('assets/svg/icon-sprite.svg#trash1') . '"></use></svg></a>';
          $html .= '</div>';
          return $html;
        })
        ->rawColumns(['id', 'Kode', 'Nama', 'Waktu', 'Aksi'])
        ->make(true);
    }

    return view('user.subject.index', [
      'curriculum' => $curriculum,
    ]);
  }

  public function store(Request $request, $curriculum_id)
  {
    $request->validate([
      'name' => 'required|string|max:100|unique:subjects,name',
    ], [
      'name.unique' => 'Mata pelajaran sudah ada.',
    ]);
    try {
      if (!$request->user()->can('subject.*')) {
        return abort(403);
      }

      $subject = Subject::create([
        'curriculum_id' => $curriculum_id,
        'name' => $request->name,
      ]);
      return $this->sendResponse('Mata pelajaran berhasil ditambahkan', $subject, 201);
    } catch (\Exception $e) {
      Log::error('Error creating subject: ' . $e->getMessage());
      return $this->sendError(
        'Silakan coba lagi.',
        [],
        500
      );
    }
  }

  public function edit(Request $request, $curriculum_id, $id)
  {
    try {
      if (!$request->user()->can('subject.*')) {
        return abort(403);
      }

      $subject = Subject::where('curriculum_id', $curriculum_id)->findOrFail($id);
      return $this->sendResponse('Mata pelajaran berhasil ditemukan', $subject, 200);
    } catch (\Exception $e) {
      return $this->sendError(
        'Silakan coba lagi.',
        [],
        500
      );
    }
  }

  public function update(Request $request, $curriculum_id, $id)
  {

    $request->validate([
      'name' => 'required|string|max:100|unique:subjects,name,' . $id,
    ], [
      'name.unique' => 'Mata pelajaran sudah ada.',
    ]);
    try {
      if (!$request->user()->can('subject.*')) {
        return abort(403);
      }

      $subject = Subject::where('curriculum_id', $curriculum_id)->findOrFail($id);
      $subject->update([
        'name' => $request->name,
      ]);
      return $this->sendResponse('Mata pelajaran berhasil diedit', $subject, 200);
    } catch (\Exception $e) {
      Log::error('Error updating subject: ' . $e->getMessage());
      return $this->sendError(
        'Silakan coba lagi.',
        [],
        500
      );
    }
  }

  public function destroy(Request $request, $curriculum_id, $id)
  {
    try {
      if (!$request->user()->can('subject.*')) {
        return abort(403);
      }

      $subject = Subject::where('curriculum_id', $curriculum_id)->findOrFail($id);

      if ($subject->schedules()->count() > 0) {
        return $this->sendError(
          'Mata pelajaran tidak dapat dihapus karena masih memiliki jadwal.',
          [],
          400
        );
      }

      $subject->schedules()->delete();
      $subject->delete();

      return $this->sendResponse(
        'Mata pelajaran berhasil dihapus',
        [],
        200
      );
    } catch (\Exception $e) {
      Log::error('Error deleting subject: ' . $e->getMessage());
      return $this->sendError(
        'Silakan coba lagi.',
        [],
        500
      );
    }
  }

  public function bulkDestroy(Request $request, $curriculum_id)
  {
    try {
      $ids = $request->input('ids');
      if (empty($ids)) {
        return $this->sendError('Tidak ada data yang dipilih untuk dihapus.', [], 400);
      }
      DB::beginTransaction();
      $subjects = Subject::where('curriculum_id', $curriculum_id)->whereIn('id', $ids)->get();

      foreach ($subjects as $subject) {
        if ($subject->schedules()->count() > 0) {
          return $this->sendError(
            'Mata pelajaran tidak dapat dihapus karena masih memiliki jadwal.',
            [],
            400
          );
        }
      };

      foreach ($subjects as $subject) {
        $subject->delete();
      }
      DB::commit();

      return $this->sendResponse(
        'Data yang dipilih berhasil dihapus.',
        [],
        200
      );
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('Error bulk destroying subjects: ' . $e->getMessage());
      return $this->sendError(
        'Silakan coba lagi.',
        [],
        500
      );
    }
  }
}
