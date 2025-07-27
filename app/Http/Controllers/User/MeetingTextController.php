<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\MeetingText;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MeetingTextController extends Controller
{
    public function index($meeting_text_id)
    {
        try {
            $meetingText = MeetingText::findOrFail($meeting_text_id);
            $this->authorize('view', $meetingText);

            return $this->sendResponse(
                'Teks pertemuan berhasil ditemukan.',
                $meetingText
            );
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return $this->sendError('Teks pertemuan tidak ditemukan.', [], 404);
        }
    }

    public function store(Request $request, $meeting_id)
    {
        $validated = $request->validate([
            'text' => 'required|string',
        ], [
            'text.required' => 'Teks pertemuan tidak boleh kosong.',
            'text.string' => 'Teks pertemuan harus berupa string.',
        ]);

        try {
            $meeting = Meeting::findOrFail($meeting_id);
            $this->authorize('update', $meeting);

            $meetingText = MeetingText::create([
                'meeting_id' => $meeting->id,
                'text' => $validated['text'],
            ]);

            return $this->sendResponse(
                'Teks pertemuan berhasil disimpan.',
                $meetingText,
                201
            );
        } catch (\Exception $e) {
            return $this->sendError('Silakan coba lagi.', [], 500);
        }
    }

    public function update(Request $request, $meeting_text_id)
    {
        $validated = $request->validate([
            'text' => 'required|string',
        ], [
            'text.required' => 'Teks pertemuan tidak boleh kosong.',
            'text.string' => 'Teks pertemuan harus berupa string.',
        ]);

        try {
            $meetingText = MeetingText::findOrFail($meeting_text_id);
            $this->authorize('update', $meetingText);

            $meetingText->update($validated);

            return $this->sendResponse(
                'Teks pertemuan berhasil diperbarui.',
                $meetingText
            );
        } catch (\Exception $e) {
            return $this->sendError('Silakan coba lagi.', [], 500);
        }
    }

    public function destroy($meeting_text_id)
    {
        try {
            $meetingText = MeetingText::findOrFail($meeting_text_id);
            $this->authorize('delete', $meetingText);

            $meetingText->delete();

            return $this->sendResponse(
                'Teks pertemuan berhasil dihapus.'
            );
        } catch (\Exception $e) {
            return $this->sendError('Silakan coba lagi.', [], 500);
        }
    }
}
