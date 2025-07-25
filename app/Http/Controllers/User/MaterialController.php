<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\MaterialRequest;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MaterialController extends Controller
{
    public function store(MaterialRequest $request, $meeting_id)
    {
        $validated = $request->validated();
        Log::info('Validated data: '. $meeting_id);

        $fileType = $validated['file_type'];
        if ($fileType === 'Link') {
            $validated['file_path'] = $validated['material_link'];
        } else {
            $filePath = $request->file('file_path')->store('materi');
            $validated['file_path'] = $filePath;
        }

        $validated['meeting_id'] = $meeting_id;

        $material = Material::create($validated);

        return $this->sendResponse('Materi berhasil disimpan', $material, 201);
    }
}
