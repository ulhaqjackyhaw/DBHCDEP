<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataKaryawanController;
use App\Http\Controllers\DataPgsController;
use App\Http\Controllers\DataPenugasanController;
use App\Http\Controllers\FormasiController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\VersionController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RealisasiController;
use App\Http\Controllers\AuditLogController;

/*
|--------------------------------------------------------------------------
| Guest Routes (Tidak Perlu Login)
|--------------------------------------------------------------------------
*/

// Redirect root ke login jika belum login
Route::get('/', function () {
    return redirect()->route('login');
})->middleware('guest');

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->name('login.store');
});

// Logout route (bisa diakses semua user yang sudah login)
Route::post('logout', [LoginController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Perlu Login)
|--------------------------------------------------------------------------
*/

// Route yang bisa diakses semua user yang sudah login
Route::middleware('auth', )->group(function () {
    // Redirect root ke dashboard setelah login
    Route::get('/', function () {
        return redirect()->route('dashboard.index');
    });

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
    Route::post('/profile/upload-photo', [ProfileController::class, 'uploadPhoto'])->name('profile.upload-photo');
    Route::post('/profile/select-avatar', [ProfileController::class, 'selectAvatar'])->name('profile.select-avatar');
    Route::delete('/profile/delete-photo', [ProfileController::class, 'deletePhoto'])->name('profile.delete-photo');

    // Dashboard dasar yang bisa diakses semua user
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // Analitik yang bisa diakses semua user
    Route::get('/analitikorganic', [EmployeeController::class, 'analitikOrganic'])->name('analitik.organic');
    Route::get('/analitikoutsourcing', [EmployeeController::class, 'analitikOutsourcing'])->name('analitik.outsourcing');

    // Data Karyawan - Basic access routes (view only)
    Route::prefix('data-karyawan')->name('karyawan.')->group(function () {
        Route::get('/', [DataKaryawanController::class, 'index'])->name('index');
        Route::get('/template/download', [DataKaryawanController::class, 'downloadTemplate'])->name('template.download');
        Route::get('/export', [DataKaryawanController::class, 'export'])->name('export');
        Route::get('/import-progress/{sessionId}', [DataKaryawanController::class, 'checkImportProgress'])->name('import.progress');
    });

    // Formasi - Basic access routes (view only) - accessible by all authenticated users
    Route::get('/formasi', [FormasiController::class, 'index'])->name('formasi.index');
    Route::get('/formasi/template/download', [FormasiController::class, 'downloadTemplate'])->name('formasi.template.download');
    Route::get('/formasi/export', [FormasiController::class, 'export'])->name('formasi.export');
    Route::get('/formasi/export-semua-lengkap', [FormasiController::class, 'exportSemuaJabatanLengkap'])->name('formasi.export.semua.lengkap');

    // Realisasi - Basic access (view list)
    Route::get('/realisasi', [RealisasiController::class, 'index'])->name('realisasi.index');

    // Data PGS - Basic access (view list)
    Route::get('/data-pgs', [DataPgsController::class, 'index'])->name('data-pgs.index');
    Route::get('/data-pgs/template/download', [DataPgsController::class, 'downloadTemplate'])->name('data-pgs.template.download');
    Route::get('/data-pgs/export', [DataPgsController::class, 'export'])->name('data-pgs.export');

    // Data Penugasan - Basic access (view list)
    Route::get('/data-penugasan', [DataPenugasanController::class, 'index'])->name('data-penugasan.index');
    Route::get('/data-penugasan/template', [DataPenugasanController::class, 'downloadTemplate'])->name('data-penugasan.template');
    Route::get('/data-penugasan/export', [DataPenugasanController::class, 'export'])->name('data-penugasan.export');

    // Dashboard - Detail jabatan lowong bisa diakses semua user
    Route::get('/dashboard/jabatan-lowong-detail', [DashboardController::class, 'getJabatanLowongDetail'])->name('dashboard.jabatan.detail');

    // Export Jabatan Lowong - Accessible by all authenticated users
    Route::get('/jabatan-lowong/export', [DashboardController::class, 'exportJabatanLowong'])->name('jabatan-lowong.export');

    // Dashboard Assignment Feature
    Route::get('/dashboard/karyawan-search', [DashboardController::class, 'searchKaryawan'])->name('dashboard.karyawan.search');
    Route::post('/dashboard/assign-definitif', [DashboardController::class, 'assignDefinitif'])->name('dashboard.assign.definitif');
    Route::post('/dashboard/assign-pgs', [DashboardController::class, 'assignPgs'])->name('dashboard.assign.pgs');
    Route::post('/dashboard/assign-penugasan', [DashboardController::class, 'assignPenugasan'])->name('dashboard.assign.penugasan');
});

/*
|--------------------------------------------------------------------------
| Admin Only Routes
|--------------------------------------------------------------------------
*/

// Add debug logging for admin routes
Route::middleware(['auth', 'can:admin'])->group(function () {
    \Log::info('Accessing admin route', [
        'user' => auth()->check() ? auth()->user()->email : 'guest',
        'role' => auth()->check() ? auth()->user()->role : 'none',
        'url' => request()->url()
    ]);

    // Dashboard admin-only features  
    Route::get('/dashboard/jabatan-lowong-export', [DashboardController::class, 'exportJabatanLowongDetail'])->name('dashboard.jabatan.export');

    // Data Karyawan - Admin features
    Route::prefix('data-karyawan')->name('karyawan.')->group(function () {
        // Create operation - must come before the {dataKaryawan} route
        Route::get('/create', [DataKaryawanController::class, 'create'])->name('create');
        Route::post('/', [DataKaryawanController::class, 'store'])->name('store');


        // Import operations with large.import middleware
        Route::middleware('large.import')->group(function () {
            Route::post('import-add', [DataKaryawanController::class, 'importAdd'])->name('import.add');
            Route::post('import-replace', [DataKaryawanController::class, 'importReplace'])->name('import.replace');
        });

        // Resource operations that use {dataKaryawan} parameter must come last
        Route::get('{dataKaryawan}/edit', [DataKaryawanController::class, 'edit'])->name('edit');
        Route::put('{dataKaryawan}', [DataKaryawanController::class, 'update'])->name('update');
        Route::delete('{dataKaryawan}', [DataKaryawanController::class, 'destroy'])->name('destroy');
    });

    // Formasi - Admin-only operations
    Route::prefix('formasi')->name('formasi.')->group(function () {
        Route::get('/create', [FormasiController::class, 'create'])->name('create');
        Route::post('/', [FormasiController::class, 'store'])->name('store');
        Route::get('/{formasi}/edit', [FormasiController::class, 'edit'])->name('edit');
        Route::put('/{formasi}', [FormasiController::class, 'update'])->name('update');
        Route::delete('/{formasi}', [FormasiController::class, 'destroy'])->name('destroy');

        // Import operations with large.import middleware
        Route::middleware('large.import')->group(function () {
            Route::post('/import-add', [FormasiController::class, 'importAdd'])->name('import.add');
            Route::post('/import-replace', [FormasiController::class, 'importReplace'])->name('import.replace');
        });
    });

    // User Management - Admin only
    Route::resource('users', \App\Http\Controllers\UserController::class)->except(['create', 'store', 'show', 'edit', 'update', 'destroy']);
    Route::get('users/create', [\App\Http\Controllers\UserController::class, 'create'])->name('users.create');
    Route::post('users', [\App\Http\Controllers\UserController::class, 'store'])->name('users.store');
    Route::get('users/{user}', [\App\Http\Controllers\UserController::class, 'show'])->name('users.show');
    Route::get('users/{user}/edit', [\App\Http\Controllers\UserController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [\App\Http\Controllers\UserController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');

    // Realisasi - Admin only CRUD
    Route::prefix('realisasi')->name('realisasi.')->group(function () {
        Route::get('/create', [RealisasiController::class, 'create'])->name('create');
        Route::post('/', [RealisasiController::class, 'store'])->name('store');
        Route::get('/{realisasi}/edit', [RealisasiController::class, 'edit'])->name('edit');
        Route::put('/{realisasi}', [RealisasiController::class, 'update'])->name('update');
        Route::delete('/{realisasi}', [RealisasiController::class, 'destroy'])->name('destroy');

        // Import routes (Admin only)
        Route::get('/template', [RealisasiController::class, 'downloadTemplate'])->name('template');
        Route::post('/import', [RealisasiController::class, 'import'])->name('import');
    });

    // Data PGS - Admin only CRUD
    Route::prefix('data-pgs')->name('data-pgs.')->group(function () {
        Route::get('/create', [DataPgsController::class, 'create'])->name('create');
        Route::post('/', [DataPgsController::class, 'store'])->name('store');
        Route::get('/{dataPgs}/edit', [DataPgsController::class, 'edit'])->name('edit');
        Route::put('/{dataPgs}', [DataPgsController::class, 'update'])->name('update');
        Route::delete('/{dataPgs}', [DataPgsController::class, 'destroy'])->name('destroy');

        // Import operations with large.import middleware
        Route::middleware('large.import')->group(function () {
            Route::post('import-add', [DataPgsController::class, 'importAdd'])->name('import.add');
            Route::post('import-replace', [DataPgsController::class, 'importReplace'])->name('import.replace');
        });
    });

    // Data Penugasan - Admin only CRUD
    Route::prefix('data-penugasan')->name('data-penugasan.')->group(function () {
        Route::get('/create', [DataPenugasanController::class, 'create'])->name('create');
        Route::post('/', [DataPenugasanController::class, 'store'])->name('store');
        Route::get('/{dataPenugasan}/edit', [DataPenugasanController::class, 'edit'])->name('edit');
        Route::put('/{dataPenugasan}', [DataPenugasanController::class, 'update'])->name('update');
        Route::delete('/{dataPenugasan}', [DataPenugasanController::class, 'destroy'])->name('destroy');

        // Import operations with large.import middleware
        Route::middleware('large.import')->group(function () {
            Route::post('import-add', [DataPenugasanController::class, 'importAdd'])->name('import.add');
            Route::post('import-replace', [DataPenugasanController::class, 'importReplace'])->name('import.replace');
        });

        // Import operations
        Route::post('/import', [DataPenugasanController::class, 'import'])->name('import');
    });

    // Audit Logs - Admin only
    Route::prefix('audit-logs')->name('audit-logs.')->group(function () {
        Route::get('/', [AuditLogController::class, 'index'])->name('index');
        Route::get('/{auditLog}', [AuditLogController::class, 'show'])->name('show');
        Route::get('/export', [AuditLogController::class, 'export'])->name('export');
    });
});

// Realisasi Export - Accessible by all authenticated users
Route::middleware('auth')->get('/realisasi/export', [RealisasiController::class, 'export'])->name('realisasi.export');

// Debug route untuk test authentication
Route::middleware('auth')->get('/debug-auth', function () {
    $user = auth()->user();
    return response()->json([
        'authenticated' => auth()->check(),
        'user' => $user ? [
            'id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'isAdmin' => $user->isAdmin()
        ] : null,
        'can_admin' => auth()->check() ? auth()->user()->can('admin') : false,
    ]);
})->name('debug.auth');

// Resource show routes - harus paling akhir agar tidak conflict dengan route lain
Route::middleware('auth')->get('/data-karyawan/{dataKaryawan}', [DataKaryawanController::class, 'show'])->name('karyawan.show');
Route::middleware('auth')->get('/formasi/{formasi}', [FormasiController::class, 'show'])->name('formasi.show');
Route::middleware('auth')->get('/realisasi/{realisasi}', [RealisasiController::class, 'show'])->name('realisasi.show');
Route::middleware('auth')->get('/data-pgs/{dataPgs}', [DataPgsController::class, 'show'])->name('data-pgs.show');
Route::middleware('auth')->get('/data-penugasan/{dataPenugasan}', [DataPenugasanController::class, 'show'])->name('data-penugasan.show');

// Version control routes (accessible by all authenticated users)
Route::middleware('auth')->prefix('versions')->name('versions.')->group(function () {
    Route::get('/', [VersionController::class, 'index'])->name('index');
    Route::post('/', [VersionController::class, 'store'])->name('store');
    Route::post('/{version}/restore', [VersionController::class, 'restore'])->name('restore');
    Route::get('/{version}/download', [VersionController::class, 'download'])->name('download');
    Route::delete('/{version}', [VersionController::class, 'destroy'])->name('destroy');
});

