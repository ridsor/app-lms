<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\MeetingRequest;
use App\Models\Meeting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MeetingController extends Controller
{
    public function update(MeetingRequest $request, $code, $meeting_id)
    {
        try {
            $validated = $request->validated();

            $meeting = Meeting::findOrFail($meeting_id);
            $this->authorize('update', $meeting);

            $meeting->update($validated);

            return $this->sendResponse('Pertemuan berhasil diperbarui', $meeting);
        } catch (\Exception $e) {
            return $this->sendError(
                'Silakan coba lagi.',
                [],
                500
            );
        }
    }

    public function startLearning(Request $request, $meeting_id)
    {
        try {
            $meeting = Meeting::select('id', 'started_at', 'schedule_id', 'schedule_time_id')->with('schedule_time')->findOrFail($meeting_id);
            $this->authorize('update', $meeting);

            $today = Carbon::now();
            $isToday = $today->isSameDay(Carbon::parse($meeting->date));
            $isDuringSchedule = $isToday && $meeting->schedule_time->start_time <= now()
                && now() <= $meeting->schedule_time->end_time;

            if (! $isDuringSchedule) {
                return $this->sendError('Waktu mulai hanya dapat dilakukan saat waktu pertemuan.', [], 400);
            }

            if ($meeting->started_at) {
                return $this->sendError('Mulai belajar sudah dilakukan.', [], 400);
            }

            $meeting->update([
                'started_at' => now(),
            ]);

            return $this->sendResponse('Mulai belajar berhasil dilakukan.', $meeting);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return $this->sendError(
                'Silakan coba lagi.',
                [],
                500
            );
        }
    }
}
