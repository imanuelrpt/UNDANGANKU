<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->string('search').'%')
                ->orWhere('email', 'like', '%'.$request->string('search').'%');
        }

        if ($request->filled('role')) {
            $query->where('role', $request->string('role'));
        }

        return view('admin.users.index', [
            'users' => $query->withCount('invitations')->latest()->paginate(20)->withQueryString(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();

        if ($user->id === auth()->id() && $validated['role'] !== 'admin') {
            return back()->with('error', 'Anda tidak dapat menurunkan role akun sendiri.');
        }

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'is_active' => $request->boolean('is_active'),
        ]);

        ActivityLog::record('user.updated', "Akun {$user->name} diperbarui.", null, ['role' => $user->role->value]);

        return back()->with('success', 'Akun berhasil diperbarui.');
    }

    public function toggle(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        ActivityLog::record('user.toggled', "Akun {$user->name} ".($user->is_active ? 'diaktifkan' : 'dinonaktifkan').'.');

        return back()->with('success', 'Status akun berhasil diubah.');
    }
}
