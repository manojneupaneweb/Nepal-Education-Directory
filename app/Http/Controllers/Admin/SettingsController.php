<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function edit(): View
    {
        abort_unless(auth()->user()?->is_admin, 403);

        return view('admin.settings', [
            'user' => auth()->user(),
        ]);
    }

    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $request->user()->forceFill([
            'password' => $request->validated('password'),
            'password_changed_at' => now(),
        ])->save();

        return redirect()
            ->route('admin.settings.edit')
            ->with('status', 'Password updated.');
    }
}
