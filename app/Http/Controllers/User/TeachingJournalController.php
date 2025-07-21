<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\TeachingJournalRequest;
use App\Models\Meeting;
use App\Models\TeachingJournal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TeachingJournalController extends Controller
{
    public function store(TeachingJournalRequest $request, $meeting_id)
    {
        try {
            $meeting = Meeting::findOrFail($meeting_id);
            $this->authorize('update', $meeting);

            $isDuringSchedule = $meeting->schedule_time->start_time <= now()
                && now() <= $meeting->schedule_time->end_time->addHours(2);

            if (!$isDuringSchedule) {
                return $this->sendError('Jurnal hanya dapat diisi selama waktu pertemuan hingga 2 jam setelahnya.', [], 400);
            }

            $validated = $request->validated();
            $validated['meeting_id'] = $meeting_id;
            if ($meeting->teaching_journal) {
                $meeting->teaching_journal->update($validated);
                return $this->sendResponse('Jurnal berhasil diperbarui.', $meeting->teaching_journal);
            } else {
                $meeting->teaching_journal()->create($validated);
                return $this->sendResponse('Jurnal berhasil disimpan.', $meeting->teaching_journal, 201);
            }
        } catch (\Exception $e) {
            return $this->sendError(
                'Silakan coba lagi.',
                [],
                500
            );
        }
    }
}
