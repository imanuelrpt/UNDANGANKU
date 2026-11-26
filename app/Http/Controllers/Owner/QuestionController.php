<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuestionRequest;
use App\Models\ActivityLog;
use App\Models\Invitation;
use App\Models\InvitationQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function store(StoreQuestionRequest $request, Invitation $invitation): RedirectResponse
    {
        $this->authorize('update', $invitation);

        $invitation->questions()->create([
            'label' => $request->validated('label'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        ActivityLog::record('question.created', 'Pertanyaan RSVP ditambahkan.', $invitation);

        return back()->with('success', 'Pertanyaan berhasil ditambahkan.');
    }

    public function update(Request $request, InvitationQuestion $question): RedirectResponse
    {
        $this->authorize('update', $question->invitation);

        $validated = $request->validate([
            'label' => ['required', 'string', 'max:150'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $question->update([
            'label' => $validated['label'],
            'is_active' => $request->boolean('is_active'),
        ]);

        ActivityLog::record('question.updated', 'Pertanyaan RSVP diperbarui.', $question->invitation);

        return back()->with('success', 'Pertanyaan berhasil diperbarui.');
    }

    public function destroy(InvitationQuestion $question): RedirectResponse
    {
        $this->authorize('update', $question->invitation);

        $invitation = $question->invitation;
        $question->delete();

        ActivityLog::record('question.deleted', 'Pertanyaan RSVP dihapus.', $invitation);

        return back()->with('success', 'Pertanyaan dihapus.');
    }
}
