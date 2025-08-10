<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskSubmissionEvaluationRequest;
use App\Http\Requests\TaskSubmissionRequest;
use App\Models\Task;
use App\Models\TaskSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TaskSubmissionController extends Controller
{
    public function submitTask(TaskSubmissionRequest $request, $task_id)
    {
        try {
            // Validate and authorize
            $task = Task::findOrFail($task_id);
            $this->authorize('view', $task);

            $user = $request->user();
            $studentId = $user->student->id;

            $taskSubmission = TaskSubmission::firstOrNew([
                'task_id' => $task_id,
                'student_id' => $studentId
            ]);

            if ($taskSubmission->exists) {
                $this->authorize('update', $taskSubmission);
            }

            // Process content
            $contents = $taskSubmission->contents ? json_decode($taskSubmission->contents, true) : [];

            $validated = $request->validated();
            // Handle content deletion
            $this->handleContentDeletion($validated, $contents);

            // Process new links
            $this->processLinks($request->input('links', []), $contents);

            // Process new files
            $this->processFiles($request->file('files', []), $contents);

            $this->filterRemovedItems($validated, $contents);

            // Prepare submission data
            $submissionData = [
                'contents' => json_encode(array_values($contents)),
                'group_members' => isset($validated['group_members']) ? json_encode(array_values($validated['group_members'])) : null,
                'submitted_at' => now()
            ];

            // Save submission
            if ($taskSubmission->exists) {
                $taskSubmission->update($submissionData);
                return $this->sendResponse('Tugas berhasil diserahkan.', [], 200);
            } else {
                TaskSubmission::create($submissionData + [
                    'task_id' => $task_id,
                    'student_id' => $studentId
                ]);
                return $this->sendResponse('Tugas berhasil diserahkan.', [], 201);
            }
        } catch (\Exception $e) {
            return $this->sendError('Silakan coba lagi.', [], 500);
        }
    }

    public function show(Request $request, $task_id)
    {
        $user = $request->user();
        $task = Task::with(['meeting.schedule.class.students:id,user_id,class_id,nis,name'])->findOrFail($task_id);
        $this->authorize('viewPossession', $task);
        $members = $task->meeting->schedule->class->students
            ->where('user_id', '!=', $user->id)
            ->pluck('nis', 'name')
            ->map(function ($nis, $name) {
                return $name . ' (' . $nis . ')';
            })
            ->values()
            ->toArray();

        $task_submission = TaskSubmission::where('task_id', $task->id);
        if ($user->hasRole('parent')) {
            $task_submission->where('student_id', $user->parent->id);
        } else {
            $task_submission->where('student_id', $user->student->id);
        }
        $task_submission = $task_submission->first();

        if (!is_null($task_submission?->contents)) {
            $task_submission->contents = json_decode($task_submission?->contents ?? '[]', true);
        }

        return view('user.task.show-tasksubmission', compact('task', 'task_submission', 'members'));
    }

    protected function handleContentDeletion(array $validated, array &$contents): void
    {
        if (!isset($validated['deleteContent'])) {
            return;
        }

        foreach ($validated['deleteContent'] as $item) {
            $index = array_search($item, array_column($contents, 'id'));

            // Pastikan item ditemukan sebelum diproses
            if ($index !== false && isset($contents[$index])) {
                $itemToDelete = $contents[$index];

                if ($itemToDelete['type'] === 'file' && !empty($itemToDelete['path'])) {
                    Storage::delete($itemToDelete['path']);
                }

                unset($contents[$index]);
                $contents = array_values($contents);
            }
        }
    }


    /**
     * Process new links
     */
    protected function processLinks(array $links, array &$contents): void
    {
        foreach ($links as $item) {
            $parsedUrl = parse_url($item['url']);
            $contents[] = [
                'id' => Str::uuid(),
                'type' => 'link',
                'url' => $item['url'],
                'domain' => $parsedUrl['host'] ?? null,
            ];
        }
    }

    /**
     * Process uploaded files
     */
    protected function processFiles(array $files, array &$contents): void
    {
        foreach ($files as $item) {
            $contents[] = [
                'id' => Str::uuid(),
                'type' => 'file',
                'name' => $item['file']->getClientOriginalName(),
                'path' => $item['file']->store('file/penyerahan-tugas'),
                'size' => $item['file']->getSize(),
            ];
        }
    }

    /**
     * Filter out removed items
     */
    protected function filterRemovedItems(array $validated, array &$contents): void
    {
        $filterTypes = ['links', 'files'];

        foreach ($filterTypes as $type) {
            if (!isset($validated[$type])) {
                continue;
            }

            $idsToRemove = array_column($validated[$type], 'id');
            $contents = array_filter($contents, fn($item) => !in_array($item['id'], $idsToRemove));
        }
    }

    public function evaluation(Request $request, $task_id, $page = 1)
    {
        $query = TaskSubmission::where('task_id', $task_id)
            ->with(['grader', 'student'])
            ->orderBy('submitted_at', 'desc');

        $submission = $query->simplePaginate(1, ['*'], 'page', $page);
        $task_submission = $query->simplePaginate(1, ['*'], 'page', $page)->first();

        $this->authorize('update', $task_submission);
        $task = $task_submission->task;
        $index = $task->meeting->schedule->meetings->search(function ($item) use ($task) {
            return $item->id == $task->meeting->id;
        });
        $task->meeting->meeting_at = $index + 1;

        $task_submission->contents = $task_submission->contents ? json_decode($task_submission->contents ?? '[]') : [];
        $task_submission->group_members = $task_submission->group_members ? json_decode($task_submission->group_members ?? '[]') : [];

        return view('user.task.evaluation', [
            'task_submission' => $task_submission,
            'task' => $task,
            'submission' => $submission
        ]);
    }

    public function postEvaluation(TaskSubmissionEvaluationRequest $request, $task_submission_id)
    {
        try {
            $task_submission = TaskSubmission::with([
                'task',
            ])->findOrFail($task_submission_id);
            $this->authorize('update', $task_submission);

            $validated = $request->validated();

            $validated['graded_by'] = $request->user()->teacher->id;
            $validated['graded_at'] = now();

            $task_submission->update($validated);

            return $this->sendResponse('Penilaian berhasil disimpan.');
        } catch (\Exception $e) {
            return $this->sendError('Silakan coba lagi.', [], 500);
        }
    }

    public function downloadFile($task_submission_id, $id)
    {
        $task_submission = TaskSubmission::with([
            'task',
        ])->findOrFail($task_submission_id);
        $this->authorize('update', $task_submission);

        $contents = $task_submission->contents ? json_decode($task_submission->contents ?? '[]') : [];

        $index = array_search($id, array_column($contents, 'id'));

        if (!empty($contents[$index]->path) && Storage::exists($contents[$index]->path)) {
            return Storage::download($contents[$index]->path, $contents[$index]->name);
        }

        return abort(404, 'File tidak ditemukan.');
    }
}
