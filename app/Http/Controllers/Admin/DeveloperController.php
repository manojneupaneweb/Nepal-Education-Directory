<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectError;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DeveloperController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()?->is_admin, 403);

        return view('admin.developer.index', [
            'errors' => ProjectError::query()->latest()->paginate(20),
        ]);
    }

    public function resolve(ProjectError $projectError): RedirectResponse
    {
        abort_unless(auth()->user()?->is_admin, 403);

        $projectError->update(['resolved_at' => now()]);

        return redirect()
            ->route('admin.developer.index')
            ->with('status', 'Error marked as resolved.');
    }
}
