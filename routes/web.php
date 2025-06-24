<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SuratKeluarController;
use App\Http\Controllers\SuratMasukController;
use App\Http\Controllers\DisposisiController;

use App\Http\Controllers\pimpinan\DashboardController as PimpinanDashboardController;
use App\Http\Controllers\staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\mahasiswa\DashboardController as MahasiswaDashboardController;
use App\Http\Controllers\admin\DashboardController as AdminDashboardController;

use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\admin\UserTypeController;
use App\Http\Controllers\admin\StatusSuratController;
use App\Http\Controllers\admin\SifatSuratController;
use App\Http\Controllers\admin\TemplateController;

use App\Http\Controllers\pimpinan\SuratKeluarController as PimpinanSuratKeluarController;
use App\Http\Controllers\pimpinan\SuratMasukController as PimpinanSuratMasukController;
use App\Http\Controllers\pimpinan\DisposisiController as PimpinanDisposisiController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'role:mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::get('/dashboard', [MahasiswaDashboardController::class, 'index'])->name('dashboard');

    // Rute untuk Surat Keluar (menggunakan controller bersama)
    Route::get('surat-keluar', [SuratKeluarController::class, 'index'])->name('surat-keluar.index');
    Route::post('surat-keluar', [SuratKeluarController::class, 'store'])->name('surat-keluar.store');
    Route::get('surat-keluar/{surat_keluar}', [SuratKeluarController::class, 'show'])->name('surat-keluar.show');
    Route::put('surat-keluar/{surat_keluar}', [SuratKeluarController::class, 'update'])->name('surat-keluar.update');
    Route::delete('surat-keluar/{surat_keluar}', [SuratKeluarController::class, 'destroy'])->name('surat-keluar.destroy');
    // Rute untuk mendapatkan detail template (AJAX)
    Route::get('/get-template-details', [SuratKeluarController::class, 'getTemplateDetails'])->name('surat-keluar.get-template-details');

    Route::get('surat-masuk', [SuratMasukController::class, 'index'])->name('surat-masuk.index');
    Route::get('surat-masuk/{id}', [SuratMasukController::class, 'show'])->name('surat-masuk.show');
    Route::get('surat-masuk/{id}/disposisi', [SuratMasukController::class, 'getDisposisi'])->name('surat-masuk.disposisi');

    // Rute untuk Disposisi yang diterima Mahasiswa (sebagai pengganti "Surat Masuk" yang relevan untuk Mahasiswa)
    Route::get('disposisi', [DisposisiController::class, 'indexMahasiswa'])->name('disposisi.index'); // Atau nama lain yang lebih spesifik
    Route::get('disposisi/{disposisi}', [DisposisiController::class, 'show'])->name('disposisi.show');
});

// Staff TU Middleware
Route::middleware(['auth', 'role:staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');

    Route::get('surat-masuk', [SuratMasukController::class, 'index'])->name('surat-masuk.index'); // Daftar SM
    Route::post('surat-masuk', [SuratMasukController::class, 'store'])->name('surat-masuk.store'); // Tambah SM (via modal)
    Route::get('surat-masuk/{surat_masuk}', [SuratMasukController::class, 'show'])->name('surat-masuk.show'); // Lihat SM (via modal)
    Route::delete('surat-masuk/{surat_masuk}', [SuratMasukController::class, 'destroy'])->name('surat-masuk.destroy'); // Hapus SM (via modal)

    // Rute untuk Surat Keluar (menggunakan controller bersama, untuk proses persetujuan/penomoran)
    Route::get('surat-keluar', [SuratKeluarController::class, 'indexStaffTU'])->name('surat-keluar.index'); // Daftar SK untuk Staff TU
    Route::get('surat-keluar/{surat_keluar}/show-process', [SuratKeluarController::class, 'showProcessFormStaffTU'])->name('surat-keluar.show-process-form'); // Tampilkan form proses
    Route::post('surat-keluar/{surat_keluar}/process', [SuratKeluarController::class, 'processStaffTU'])->name('surat-keluar.process'); // Proses penomoran/verifikasi oleh Staff TU
    Route::post('surat-keluar/{surat_keluar}/reject-staff', [SuratKeluarController::class, 'rejectByStaffTU'])->name('surat-keluar.reject-staff'); // Tolak oleh Staff TU
    Route::get('surat-keluar/{surat_keluar}/download', [PimpinanSuratKeluarController::class, 'download'])->name('surat-keluar.download'); // Download Surat Keluar
    Route::post('surat-keluar', [SuratKeluarController::class, 'store'])->name('surat-keluar.store');
    Route::get('surat-keluar/{surat_keluar}', [SuratKeluarController::class, 'show'])->name('surat-keluar.show');
    Route::put('surat-keluar/{surat_keluar}', [SuratKeluarController::class, 'update'])->name('surat-keluar.update');
    Route::delete('surat-keluar/{surat_keluar}', [SuratKeluarController::class, 'destroy'])->name('surat-keluar.destroy');
    // Rute untuk mendapatkan detail template (AJAX)
    Route::get('/get-template-details', [SuratKeluarController::class, 'getTemplateDetails'])->name('surat-keluar.get-template-details');

    // Rute untuk Disposisi (menggunakan controller bersama)
    Route::get('disposisi/{surat_masuk}/create', [DisposisiController::class, 'create'])->name('disposisi.create');
    Route::post('disposisi/{surat_masuk}', [DisposisiController::class, 'store'])->name('disposisi.store');
    Route::get('disposisi-sent', [DisposisiController::class, 'indexSentByStaffTU'])->name('disposisi.sent.index'); // Disposisi yang dibuat Staff TU
    Route::get('disposisi-received', [DisposisiController::class, 'indexReceivedByStaffTU'])->name('disposisi.received.index'); // Disposisi yang diterima Staff TU
});

// Pimpinan/Rektorat Middleware
Route::prefix('pimpinan')->middleware(['auth', 'role:pimpinan'])->name('pimpinan.')->group(function () {
    Route::get('/dashboard', [PimpinanDashboardController::class, 'index'])->name('dashboard');

    // Persetujuan Surat Keluar
    Route::get('surat-keluar', [PimpinanSuratKeluarController::class, 'index'])->name('surat-keluar.index');
    Route::get('surat-keluar/{surat_keluar}', [PimpinanSuratKeluarController::class, 'show'])->name('surat-keluar.show');
    Route::post('surat-keluar/{surat_keluar}/approve', [PimpinanSuratKeluarController::class, 'approve'])->name('surat-keluar.approve');
    Route::post('surat-keluar/{surat_keluar}/reject', [PimpinanSuratKeluarController::class, 'reject'])->name('surat-keluar.reject');
    Route::get('/surat-keluar/{suratKeluar}/download', [PimpinanSuratKeluarController::class, 'download'])->name('surat-keluar.download');
    // Melihat Surat Masuk (read-only)
    Route::get('surat-masuk', [PimpinanSuratMasukController::class, 'index'])->name('surat-masuk.index');
    Route::get('surat-masuk/{surat_masuk}', [PimpinanSuratMasukController::class, 'show'])->name('surat-masuk.show');

    // Melihat Disposisi (read-only, mungkin yang ditujukan kepadanya)
    Route::get('disposisi', [PimpinanDisposisiController::class, 'index'])->name('disposisi.index');
    Route::get('disposisi/{disposisi}', [PimpinanDisposisiController::class, 'show'])->name('disposisi.show');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // CRUD untuk User
    Route::resource('users', UserController::class);
    Route::post('users/import', [\App\Http\Controllers\Admin\UserController::class, 'import'])->name('admin.users.import');
    // CRUD untuk UserType (Role)
    Route::resource('roles', UserTypeController::class)->except(['show']); // Tidak perlu show untuk detail role

    // CRUD untuk Status Surat
    Route::resource('status-surat', StatusSuratController::class)->except(['show']);

    // CRUD untuk Sifat Surat
    Route::resource('sifat-surat', SifatSuratController::class)->except(['show']);

    // CRUD untuk Template Surat    
    Route::resource('templates', TemplateController::class)->only([
        'index',
        'store',
        'update',
        'destroy'
    ]);
});

require __DIR__ . '/auth.php';
