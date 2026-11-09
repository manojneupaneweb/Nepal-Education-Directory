<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SearchKeyword;
use Illuminate\View\View;

class KeywordController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()?->is_admin, 403);

        return view('admin.keywords.index', [
            'keywords' => SearchKeyword::query()->latest()->paginate(30),
        ]);
    }
}
