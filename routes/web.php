<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InstitutionController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Public\DirectoryController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\SearchController;
use App\Http\Controllers\Public\SitemapController;
use App\Http\Controllers\Public\UniversityController;
use App\Http\Controllers\Public\UniversityProgramController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/schools', [DirectoryController::class, 'schools'])->name('schools.index');
Route::get('/universities', [UniversityController::class, 'index'])->name('universities.index');
Route::get('/universities/{university}', [UniversityController::class, 'show'])->name('universities.show');
Route::get('/university-programs', [UniversityProgramController::class, 'index'])->name('university-programs.index');
Route::get('/university-programs/{university}/programs', [UniversityProgramController::class, 'university'])->name('university-programs.university');
Route::get('/university-programs/{program}', [UniversityProgramController::class, 'show'])->name('university-programs.show');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/types', [DirectoryController::class, 'types'])->name('types.index');
Route::get('/types/{institutionType:slug}', [DirectoryController::class, 'type'])->name('types.show');
Route::get('/provinces', [DirectoryController::class, 'provinces'])->name('provinces.index');
Route::get('/provinces/{province:slug}', [DirectoryController::class, 'province'])->name('provinces.show');
Route::get('/districts/{district:slug}', [DirectoryController::class, 'district'])->name('districts.show');
Route::get('/districts/{district:slug}/local-governments/{municipality:slug}', [DirectoryController::class, 'municipality'])
    ->name('municipalities.show')
    ->scopeBindings();
Route::get('/institutions/{institution:slug}', [DirectoryController::class, 'show'])->name('institutions.show');
Route::get('/neb', [DirectoryController::class, 'neb'])->name('neb');
Route::get('/pabson', [DirectoryController::class, 'pabson'])->name('pabson');
Route::get('/about', [DirectoryController::class, 'about'])->name('about');
Route::get('/how-it-works', [DirectoryController::class, 'howItWorks'])->name('how-it-works');
Route::get('/contact', [DirectoryController::class, 'contact'])->name('contact');
Route::get('/blog', [DirectoryController::class, 'blog'])->name('blog');
Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::post('/logout', [LoginController::class, 'destroy'])->name('logout')->middleware('auth');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/institutions', [InstitutionController::class, 'index'])->name('institutions.index');
    Route::get('/institutions/create', [InstitutionController::class, 'create'])->name('institutions.create');
    Route::post('/institutions', [InstitutionController::class, 'store'])->name('institutions.store');
    Route::get('/institutions/{institution}', [InstitutionController::class, 'show'])->name('institutions.show');
    Route::get('/institutions/{institution}/edit', [InstitutionController::class, 'edit'])->name('institutions.edit');
    Route::put('/institutions/{institution}', [InstitutionController::class, 'update'])->name('institutions.update');
    Route::post('/institutions/{institution}/submit', [InstitutionController::class, 'submit'])->name('institutions.submit');
    Route::post('/institutions/{institution}/approve', [InstitutionController::class, 'approve'])->name('institutions.approve');
    Route::post('/institutions/{institution}/reject', [InstitutionController::class, 'reject'])->name('institutions.reject');
    Route::post('/institutions/{institution}/archive', [InstitutionController::class, 'archive'])->name('institutions.archive');
    Route::delete('/institutions/{institution}', [InstitutionController::class, 'destroy'])->name('institutions.destroy');
});
