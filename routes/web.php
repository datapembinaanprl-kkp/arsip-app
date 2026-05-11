<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\{
    AboutController,
    AdminController,
    ArchiveController,
    AssetController,
    DashboardController,
    DocumentController,
    KanbanController,
    OrganizationalStructureController,
    ProfileController,
    SurveyController,
    TimKerjaController,
    UserController,
};
use App\Http\Controllers\GIS\SpatialDataController;
use App\Http\Middleware\EnsureIsDirektur;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::get('/about', [AboutController::class, 'index'])->name('about');

/*
|--------------------------------------------------------------------------
| Survey Public
|--------------------------------------------------------------------------
*/

Route::get('/s/{token}', [SurveyController::class, 'publicShow'])->name('survey.public');
Route::post('/s/{token}', [SurveyController::class, 'publicSubmit'])->name('survey.public.submit');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    // ── Dashboard ──────────────────────────────────────────────────────────
    // FIX: gunakan controller agar props dikirim ke komponen
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/chart-data', [DashboardController::class, 'chartData'])->name('dashboard.chart');

    // ── Profile ────────────────────────────────────────────────────────────
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── Documents ──────────────────────────────────────────────────────────
    Route::resource('documents', DocumentController::class);
    Route::patch('documents/{document}/status', [KanbanController::class, 'updateStatus'])
        ->name('documents.status.update');

    // ── Archives ───────────────────────────────────────────────────────────
    Route::resource('archives', ArchiveController::class);
    Route::post('/archives/{archive}/tolak', [ArchiveController::class, 'tolak'])
        ->name('archives.tolak')
        ->middleware('permission:dokumen.tolak');

    // ── Survey ─────────────────────────────────────────────────────────────
    Route::resource('survey', SurveyController::class);
    Route::post('/survey/{survey}/submit', [SurveyController::class, 'submit'])->name('survey.submit');
    Route::get('/survey/{survey}/results', [SurveyController::class, 'results'])->name('survey.results');

    // ── Organizational Structure ───────────────────────────────────────────
    Route::resource('organizational-structure', OrganizationalStructureController::class);

    // ── Assets ─────────────────────────────────────────────────────────────
    Route::prefix('assets')->name('assets.')->group(function () {
        Route::get('/', [AssetController::class, 'index'])->name('index');
        Route::get('/create', [AssetController::class, 'create'])->name('create');
        Route::post('/', [AssetController::class, 'store'])->name('store');
        Route::get('/export/pdf', [AssetController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/export/excel', [AssetController::class, 'exportExcel'])->name('export.excel');
        Route::get('/{asset}', [AssetController::class, 'show'])->name('show');
        Route::get('/{asset}/edit', [AssetController::class, 'edit'])->name('edit');
        Route::put('/{asset}', [AssetController::class, 'update'])->name('update');
        Route::delete('/{asset}', [AssetController::class, 'destroy'])->name('destroy');
        Route::post('/{asset}/mutasi', [AssetController::class, 'mutationStore'])->name('mutation.store');
    });

    // ── Kanban ─────────────────────────────────────────────────────────────
    Route::middleware([EnsureIsDirektur::class])->prefix('kanban')->name('kanban.')->group(function () {
        Route::get('/', [KanbanController::class, 'index'])->name('index');
    });

    // ── GIS ────────────────────────────────────────────────────────────────
    Route::get('/gis', [SpatialDataController::class, 'index'])->name('gis.index');

    // ── Role-gated: Admin + Kepala Tim Kerja ───────────────────────────────
    // FIX: hapus duplikat — users & tim-kerja cukup didefinisikan SEKALI
    Route::middleware('role:admin,kepala_tim_kerja')->group(function () {
        Route::resource('users', UserController::class);
        Route::patch('users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle');
        Route::resource('tim-kerja', TimKerjaController::class);
    });

    // ── Admin only ─────────────────────────────────────────────────────────
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/settings', fn() => inertia('Admin/Settings'))->name('settings');
    });
});

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';