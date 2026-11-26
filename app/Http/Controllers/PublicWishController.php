<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWishRequest;
use App\Models\ActivityLog;
use App\Models\Guest;
use App\Models\Invitation;
use App\Models\Wish;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PublicWishController extends Controller
{
    public function store(StoreWishRequest $request, string $slug): RedirectResponse
    {
        $invitation = Invitation::where('slug', $slug)->firstOrFail();

        if ($invitation->status->value !== 'published') {
            abort(404, 'Undangan ini belum tersedia.');
        }

        $guest = null;
        $token = $request->route('token') ?? $request->input('token');
        if (filled($token)) {
            $guest = Guest::where('invitation_id', $invitation->id)
                ->where('token', $token)
                ->firstOrFail();
        }

        $data = $request->validated();

        $wish = Wish::create([
            'invitation_id' => $invitation->id,
            'guest_id' => $guest?->id,
            'name' => $guest?->name ?? $data['name'],
            'message' => $this->filter($data['message']),
            'ip_address' => $request->ip(),
        ]);

        ActivityLog::record('wish.created', "Ucapan baru dari {$wish->name}.", $invitation);

        return back()->with('success', 'Ucapan Anda berhasil dikirim. Terima kasih!');
    }

    public function load(Request $request, string $slug): JsonResponse
    {
        $invitation = Invitation::where('slug', $slug)->firstOrFail();

        if ($invitation->status->value !== 'published') {
            abort(404);
        }

        $perPage = 8;
        $page = max(1, (int) $request->query('page', 1));
        $query = $invitation->wishes()->visible()->latest();
        $total = (clone $query)->count();
        $wishes = $query->skip(($page - 1) * $perPage)->take($perPage)->get()
            ->map(fn (Wish $wish): array => [
                'name' => $wish->name,
                'message' => $wish->message,
                'time' => $wish->created_at->diffForHumans(),
                'reply' => $wish->reply_message,
            ]);

        return response()->json([
            'wishes' => $wishes,
            'has_more' => $total > $page * $perPage,
            'next_page' => $total > $page * $perPage ? $page + 1 : null,
        ]);
    }

    private function filter(string $message): string
    {
        $words = collect(config('profanity.words', []))
            ->map(fn (string $word): string => preg_quote($word, '/'))
            ->implode('|');

        if ($words === '') {
            return $message;
        }

        return preg_replace("/\b({$words})\b/iu", (string) config('profanity.replacement', '***'), $message) ?: $message;
    }
}
