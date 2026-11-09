<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\View\View;

class ContactMessageController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()?->is_admin, 403);

        return view('admin.messages.index', [
            'messages' => ContactMessage::query()->latest()->paginate(20),
        ]);
    }
}
