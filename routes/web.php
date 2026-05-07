<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\OrganizationalStructureController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\KanbanController;
use App\Http\Middleware\EnsureIsDirektur;
use App\Http\Controllers\GIS\SpatialDataController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/survey', function () {
    return Inertia::render('Survey/Index');
})->name('survey.index');


// ─── Public ───────────────────────────────────────────
Route::get('/', fn() => auth()->check() ? redirect()->route('dashboard') : redirect()->route('login'));
Route::get('/about', [AboutController::class, 'index'])->name('about');

// ─── Breeze Auth routes ────────────────────────────────
require __DIR__.'/auth.php';

// ─── Authenticated routes ─────────────────────────────
Route::middleware(['auth'])->group(function () {
    

// Dashboard — semua role yang login
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard/chart-data', [DashboardController::class, 'chartData'])->name('dashboard.chart');

// Profile (Breeze)
Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

// ─── Archives (dokumen) ───────────────────────────
Route::resource('archives', ArchiveController::class);

// Route tambahan: tolak dokumen (supervisor/direktur/admin)
Route::post('/archives/{archive}/tolak', [ArchiveController::class, 'tolak'])
        ->name('archives.tolak')
        ->middleware('permission:dokumen.tolak');

// ─── Admin routes ─────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
 
Route::get('/users',                 [AdminController::class, 'users'])->name('users');
Route::get('/users/create',          [AdminController::class, 'createUser'])->name('users.create');
Route::post('/users',                [AdminController::class, 'storeUser'])->name('users.store');
Route::get('/users/{user}/edit',     [AdminController::class, 'editUser'])->name('users.edit');
Route::put('/users/{user}',          [AdminController::class, 'updateUser'])->name('users.update');
Route::patch('/users/{user}/toggle', [AdminController::class, 'toggleUser'])->name('users.toggle');
Route::delete('/users/{user}',       [AdminController::class, 'destroyUser'])->name('users.destroy');
 
});
    
Route::get('/survey', [SurveyController::class, 'index'])->name('survey.index');
Route::get('/survey/create', [SurveyController::class, 'create'])->name('survey.create');
Route::post('/survey', [SurveyController::class, 'store'])->name('survey.store');
Route::get('/survey/{survey}', [SurveyController::class, 'show'])->name('survey.show');
Route::post('/survey/{survey}/submit', [SurveyController::class, 'submit'])->name('survey.submit');


Route::get('/s/{token}', [SurveyController::class, 'publicShow'])
    ->name('survey.public');

Route::post('/s/{token}', [SurveyController::class, 'publicSubmit'])
    ->name('survey.public.submit');
    
Route::get('/survey/{survey}/results', [SurveyController::class, 'results'])
    ->name('survey.results');
   
Route::get('/struktur-organisasi', function () {
    return view('struktur.index');
    })->name('struktur.index');
    
// ─── Organizational Structure ─────────────────────────────────
Route::resource('organizational-structure', OrganizationalStructureController::class)
    ->middleware(['auth']); // Add role middleware if needed: ->middleware(['auth', 'role:admin'])


// ─── Aset BMN ────────────────────────────────────────────────
Route::middleware(['auth'])->prefix('assets')->name('assets.')->group(function () {
    Route::get('/',              [AssetController::class, 'index'])->name('index');
    Route::get('/create',        [AssetController::class, 'create'])->name('create');
    Route::post('/',             [AssetController::class, 'store'])->name('store');
    Route::get('/{asset}',       [AssetController::class, 'show'])->name('show');
    Route::get('/{asset}/edit',  [AssetController::class, 'edit'])->name('edit');
    Route::put('/{asset}',       [AssetController::class, 'update'])->name('update');
    Route::delete('/{asset}',    [AssetController::class, 'destroy'])->name('destroy');

    // Mutasi aset
    Route::post('/{asset}/mutasi', [AssetController::class, 'mutationStore'])->name('mutation.store');

    // Export
    Route::get('/export/pdf',   [AssetController::class, 'exportPdf'])->name('export.pdf');
    Route::get('/export/excel', [AssetController::class, 'exportExcel'])->name('export.excel');
});
// ─── Kanban (khusus direktur) ─────────────────────────────────
Route::middleware(['auth', EnsureIsDirektur::class])
    ->prefix('kanban')
    ->name('kanban.')
    ->group(function () {
Route::get('/', [KanbanController::class, 'index'])->name('index');
    });

// ─── Update status (staff + direktur, validasi di controller) ──
Route::middleware(['auth'])
    ->patch('documents/{document}/status', [KanbanController::class, 'updateStatus'])
    ->name('documents.status.update');

// ─── CRUD Dokumen ─────────────────────────────────────────────
Route::middleware(['auth'])
    ->resource('documents', DocumentController::class);
});
// GIS Dashboard — proteksi dengan auth middleware yang sudah ada
Route::middleware(['auth'])->group(function () {
    Route::get('/gis', [SpatialDataController::class, 'index'])->name('gis.index');
});