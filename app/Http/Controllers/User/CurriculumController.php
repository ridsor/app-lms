<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Curriculum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\Curriculum\CurriculumRequest;

class CurriculumController extends Controller
{
  public function index(Request $request)
  {
    if (!$request->user()->can('curriculum.*')) {
      return abort(403);
    }

    if ($request->ajax()) {
      $data = Curriculum::withCount('subjects')->filter($request->all());

      return DataTables::of($data)
        ->addColumn('id', function ($row) {
          $html = '<div class="checkbox-checked"><div class="form-check d-flex justify-content-center align-items-center"><input class="form-check-input select-row" type="checkbox" style="width: 12px; height: 12px;" value="' . $row->id . '" name="selected_ids[]" id="select-row-' . $row->id . '"></div></div>';
          return $html;
        })
        ->addColumn('Nama', function ($row) {
          return $row->name;
        })
        ->addColumn('Deskripsi', function ($row) {
          return $row->description ? '<div class="ql-editor p-0 m-0">' . $row->description . '</div>' : '-';
        })
        ->addColumn('Mata Pelajaran', function ($row) {
          $html = '
          <a class="square-white view d-flex align-items-center" style="cursor: pointer; width: fit-content" href="' . route('user.subject.index', ['curriculum_id' => $row->id]) . '">
                <span class="badge badge-light-primary">Mata Pelajaran (' . $row->subjects_count . ')</span>
          </a>
          ';
          return $html;
        })
        ->addColumn('Status', function ($row) {
          return $row->status ?
            '<a class="curriculum-active" style="cursor: pointer" data-id="' . $row->id . '" data-name="' . $row->name . '"><span class="badge badge-light-success">Aktif</span></a>'
            :
            '<a class="curriculum-inactive" style="cursor: pointer" data-id="' . $row->id . '" data-name="' . $row->name . '"><span class="badge badge-light-secondary">Tidak Aktif</span></a>';
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
        ->rawColumns(['id', 'Nama', 'Deskripsi', 'Mata Pelajaran', 'Status', 'Waktu', 'Aksi'])
        ->make(true);
    } else {
      return view('user.curriculum.index');
    }
  }

  public function store(CurriculumRequest $request)
  {
    try {
      if (!$request->user()->can('curriculum.*')) {
        return abort(403);
      }
      $validated = $request->validated();
      $validated['status'] = true;
      $curriculum = Curriculum::create($validated);
      return $this->sendResponse('Kurikulum berhasil ditambahkan', [
        'id' => $curriculum->id,
        'name' => $curriculum->name,
        'description' => $curriculum->description,
        'status' => $curriculum->status,
        'created_at' => $curriculum->created_at->translatedFormat('d/m/Y H:i')
      ], 201);
    } catch (\Exception $e) {
      return $this->sendError('Silakan coba lagi.', [], 500);
    }
  }

  public function edit(Request $request, $id)
  {
    try {
      if (!$request->user()->can('curriculum.*')) {
        return abort(403);
      }
      $curriculum = Curriculum::find($id);
      if (!$curriculum) {
        return $this->sendError('Data kurikulum tidak ditemukan.', [], 404);
      }
      return $this->sendResponse('Data kurikulum ditemukan', $curriculum);
    } catch (\Exception $e) {
      return $this->sendError('Silakan coba lagi.', [], 500);
    }
  }

  public function update(CurriculumRequest $request, $id)
  {
    try {
      if (!$request->user()->can('curriculum.*')) {
        return abort(403);
      }
      $validated = $request->validated();
      $curriculum = Curriculum::findOrFail($id);
      $curriculum->update($validated);
      return $this->sendResponse('Kurikulum berhasil diedit', $curriculum);
    } catch (\Exception $e) {
      return $this->sendError('Silakan coba lagi.', [], 500);
    }
  }

  public function destroy(Request $request, $id)
  {
    try {
      if (!$request->user()->can('curriculum.*')) {
        return abort(403);
      }
      $curriculum = Curriculum::with('subjects.schedules')->findOrFail($id);

      if ($curriculum->subjects->count() > 0) {
        return $this->sendError('Kurikulum tidak dapat dihapus karena masih terdapat mata pelajaran yang terkait.', [], 400);
      }

      $curriculum->delete();
      return $this->sendResponse('Kurikulum berhasil dihapus.');
    } catch (\Exception $e) {
      Log::info($e->getMessage());
      return $this->sendError('Silakan coba lagi.', [], 500);
    }
  }

  public function bulkDestroy(Request $request)
  {
    try {
      if (!$request->user()->can('curriculum.*')) {
        return abort(403);
      }
      $ids = $request->input('ids');
      if (empty($ids)) {
        return $this->sendError('Tidak ada data yang dipilih untuk dihapus.', [], 400);
      }
      $curriculums = Curriculum::whereIn('id', $ids)->get();
      foreach ($curriculums as $curriculum) {
        if ($curriculum->subjects->count() > 0) {
          return $this->sendError('Kurikulum tidak dapat dihapus karena masih terdapat mata pelajaran yang terkait.', [], 400);
        }
        $curriculum->delete();
      }
      return $this->sendResponse('Data yang dipilih berhasil dihapus.');
    } catch (\Exception $e) {
      Log::info($e->getMessage());
      return $this->sendError('Silakan coba lagi.', [], 500);
    }
  }

  public function active(Request $request, $id)
  {
    try {
      if (!$request->user()->can('curriculum.*')) {
        return abort(403);
      }
      $curriculum = Curriculum::findOrFail($id);
      if ($curriculum->status) {
        $curriculum->update(['status' => false]);
      } else {
        $curriculum->update(['status' => true]);
      }
      return $this->sendResponse('Kurikulum berhasil diaktifkan.', $curriculum);
    } catch (\Exception $e) {
      Log::info($e->getMessage());
      return $this->sendError('Silakan coba lagi.', [], 500);
    }
  }
}
