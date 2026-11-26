<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateThemeRequest;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class ThemeController extends Controller
{
    public function index(): View
    {
        return view('admin.themes.index', [
            'themes' => config('themes.list', []),
            'overrides' => Cache::get('themes.overrides', []),
        ]);
    }

    public function update(UpdateThemeRequest $request, string $theme): RedirectResponse
    {
        $validated = $request->validated();

        if ($validated['theme'] !== $theme) {
            return back()->with('error', 'Tema tidak sesuai.');
        }

        $overrides = Cache::get('themes.overrides', []);
        $overrides[$theme] = (bool) $validated['active'];

        Cache::forever('themes.overrides', $overrides);
        Cache::forget('themes.active');

        ActivityLog::record('theme.updated', "Tema {$theme} ".($overrides[$theme] ? 'diaktifkan' : 'dinonaktifkan').'.');

        return back()->with('success', 'Status tema berhasil diperbarui.');
    }
}
