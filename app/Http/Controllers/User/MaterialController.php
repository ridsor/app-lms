<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\MaterialRequest;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    public function show($materi_id)
    {
        try {
            $material = Material::findOrFail($materi_id);
            $this->authorize('view', $material);

            return $this->sendResponse(
                'Materi berhasil ditemukan.',
                $material
            );
        } catch (\Exception $e) {
            return $this->sendError('Materi tidak ditemukan.', [], 404);
        }
    }

    public function store(MaterialRequest $request, $meeting_id)
    {
        try {
            $this->authorize('create', Material::class);

            $validated = $request->validated();

            $fileType = $validated['file_type'];
            if ($fileType === 'Link') {
                $validated['file_path'] = $validated['material_link'];
            } else {
                $filePath = $request->file('file_path')->store('file/materi');
                $validated['file_path'] = $filePath;
                $file = $request->file('file_path');
                $file_name = $file->getClientOriginalName();
                $file_size = $file->getSize();
                $validated['file_name'] = $file_name;
                $validated['file_size'] = $file_size;
            }

            $validated['meeting_id'] = $meeting_id;

            $material = Material::create($validated);

            return $this->sendResponse('Materi berhasil disimpan', $material, 201);
        } catch (\Exception $e) {
            return $this->sendError('Silakan coba lagi.', [], 500);
        }
    }

    public function update(MaterialRequest $request, $materi_id)
    {
        try {
            $material = Material::findOrFail($materi_id);
            $this->authorize('update', $material);

            $validated = $request->validated();

            if ($validated['file_type'] === 'Link') {
                $validated['file_path'] = $validated['material_link'];
            } else {
                if ($validated['deletedFile']) {
                    if (!empty($material->file_path) && Storage::exists($material->file_path)) {
                        Storage::delete($material->file_path);
                    }
                    $validated['file_path'] = null;
                    $validated['file_name'] = null;
                    $validated['file_size'] = null;
                }

                if ($request->hasFile('file_path')) {
                    if (!empty($material->file_path) && Storage::exists($material->file_path)) {
                        Storage::delete($material->file_path);
                    }
                    $filePath = $request->file('file_path')->store('file/materi');
                    $validated['file_path'] = $filePath;
                    $file = $request->file('file_path');
                    $file_name = $file->getClientOriginalName();
                    $file_size = $file->getSize();
                    $validated['file_name'] = $file_name;
                    $validated['file_size'] = $file_size;
                }
            }

            $material->update($validated);

            return $this->sendResponse('Materi berhasil diperbarui', $material);
        } catch (\Exception $e) {
            return $this->sendError('Silakan coba lagi.', [], 500);
        }
    }

    public function destroy($materi_id)
    {
        try {
            $material = Material::findOrFail($materi_id);
            $this->authorize('delete', $material);

            if (!empty($task->file_path) && Storage::exists($material->file_path)) {
                Storage::delete($material->file_path);
            }

            $material->delete();

            return $this->sendResponse('Materi berhasil dihapus');
        } catch (\Exception $e) {
            return $this->sendError('Silakan coba lagi.', [], 500);
        }
    }

    public function getFile(Request $request, $materi_id)
    {
        $material = Material::findOrFail($materi_id);
        if (!$request->hasValidSignature()) {
            $this->authorize('view', $material);
        }

        if ($material->file_type === 'Link') {
            return abort(404, 'File tidak ditemukan.');
        }

        if (Storage::exists($material->file_path)) {
            return response()->file(Storage::path($material->file_path));
        }

        return abort(404, 'File tidak ditemukan.');
    }

    public function downloadFile($materi_id)
    {
        $material = Material::findOrFail($materi_id);
        $this->authorize('view', $material);

        if ($material->file_type === 'Link') {
            return redirect($material->file_path);
        }

        if (Storage::exists($material->file_path)) {
            return Storage::download($material->file_path, $material->file_name);
        }

        return abort(404, 'File tidak ditemukan.');
    }
}
