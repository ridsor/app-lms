<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    public function show($task_id)
    {
        try {
            $task = Task::findOrFail($task_id);
            $this->authorize('view', $task);

            return $this->sendResponse(
                'Tugas berhasil ditemukan.',
                $task
            );
        } catch (\Exception $e) {
            return $this->sendError('Tugas tidak ditemukan.', [], 404);
        }
    }

    public function store(TaskRequest $request, $meeting_id)
    {
        try {
            $this->authorize('create', Task::class);

            $validated = $request->validated();

            if ($request->hasFile('file_path')) {
                $filePath = $request->file('file_path')->store('file/tugas');
                $validated['file_path'] = $filePath;
                $file = $request->file('file_path');
                $file_name = $file->getClientOriginalName();
                $file_size = $file->getSize();
                $validated['file_name'] = $file_name;
                $validated['file_size'] = $file_size;
            }

            $validated['meeting_id'] = $meeting_id;

            $task = Task::create($validated);

            return $this->sendResponse('Tugas berhasil disimpan', $task, 201);
        } catch (\Exception $e) {
            return $this->sendError('Silakan coba lagi.', [], 500);
        }
    }

    public function update(TaskRequest $request, $task_id)
    {
        try {
            $task = Task::findOrFail($task_id);
            $this->authorize('update', $task);

            $validated = $request->validated();
            $validated = array_filter($validated, function ($value) {
                return !is_null($value);
            });

            if ($validated['deletedFile']) {
                if (!empty($task->file_path) && Storage::exists($task->file_path)) {
                    Storage::delete($task->file_path);
                }
                $validated['file_path'] = null;
                $validated['file_name'] = null;
                $validated['file_size'] = null;
            }

            if ($request->hasFile('file_path')) {
                if (!empty($task->file_path) && Storage::exists($task->file_path)) {
                    Storage::delete($task->file_path);
                }
                $filePath = $request->file('file_path')->store('file/tugas');
                $validated['file_path'] = $filePath;
                $file = $request->file('file_path');
                $file_name = $file->getClientOriginalName();
                $file_size = $file->getSize();
                $validated['file_name'] = $file_name;
                $validated['file_size'] = $file_size;
            }

            if (!$validated['allow_late_submission']) {
                $validated['late_submission_time'] = null;
            }

            $task->update($validated);

            return $this->sendResponse('Tugas berhasil diperbarui', $task);
        } catch (\Exception $e) {
            return $this->sendError('Silakan coba lagi.', [], 500);
        }
    }

    public function destroy($task_id)
    {
        try {
            $task = Task::findOrFail($task_id);
            $this->authorize('delete', $task);

            if (!empty($task->file_path) && Storage::exists($task->file_path)) {
                Storage::delete($task->file_path);
            }

            $task->delete();

            return $this->sendResponse('Tugas berhasil dihapus');
        } catch (\Exception $e) {
            return $this->sendError('Silakan coba lagi.', [], 500);
        }
    }

    public function getFile(Request $request, $task_id)
    {
        $task = Task::findOrFail($task_id);
        $this->authorize('view', $task);

        if (!empty($task->file_path) && Storage::exists($task->file_path)) {
            return response()->file(Storage::path($task->file_path));
        }

        return abort(404, 'File not found');
    }

    public function downloadFile($task_id)
    {
        $task = Task::findOrFail($task_id);
        $this->authorize('view', $task);

        if (!empty($task->file_path) && Storage::exists($task->file_path)) {
            return Storage::download($task->file_path, $task->file_name);
        }

        return abort(404, 'File not found');
    }
}
