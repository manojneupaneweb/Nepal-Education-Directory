<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class CloudflareController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()?->is_admin, 403);

        return view('admin.cloudflare');
    }
}
