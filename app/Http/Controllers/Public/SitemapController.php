<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\University;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = collect([
            route('home'),
            route('universities.index'),
            route('university-programs.index'),
            route('schools.index'),
            route('provinces.index'),
        ])->merge(
            University::query()->pluck('slug')->map(fn (string $slug) => route('universities.show', $slug))
        )->merge(
            University::query()->pluck('slug')->map(function (string $slug) {
                $key = str_ends_with($slug, '-university') ? Str::beforeLast($slug, '-university') : $slug;

                return route('university-programs.university', $key);
            })
        )->merge(
            Program::query()->whereNotNull('university_id')->pluck('slug')->map(fn (string $slug) => route('university-programs.show', $slug))
        );

        $xml = view('public.sitemap', ['urls' => $urls])->render();

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
