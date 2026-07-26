<?php

use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\Client\Index as ClientIndex;
use App\Livewire\Admin\Client\Create as ClientCreate;
use App\Livewire\Admin\Client\Edit as ClientEdit;
use App\Livewire\Admin\Charges\Index as ChargesIndex;
use App\Livewire\Auth\Login;
use App\Livewire\Home;
use Illuminate\Support\Facades\Route;

Route::get('/', Home::class)->name('home');
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
    Route::get('/client/index', ClientIndex::class)->name('client.index');
    Route::get('/client/create', ClientCreate::class)->name('client.create');
    Route::get('/client/edit/{id}', ClientEdit::class)->name('client.edit');
    Route::get('/charges/index', ChargesIndex::class)->name('charges.index');
    Route::get('/charges/create', \App\Livewire\Admin\Charges\Create::class)->name('charges.create');
    Route::get('/charges/edit/{id}', \App\Livewire\Admin\Charges\Edit::class)->name('charges.edit');
    Route::get('/verifikasi/index', App\Livewire\Admin\Verifikasi\Index::class)->name('verifikasi.index');

    Route::get('/riwayat/index', App\Livewire\Admin\Riwayat\Index::class)->name('riwayat.index');
    Route::get('/riwayat/show/{id}', App\Livewire\Admin\Riwayat\Show::class)->name('riwayat.show');
    Route::get('/laporan/index', App\Livewire\Admin\Laporan\Index::class)->name('laporan.index');
    Route::get('/dokumen/index', App\Livewire\Admin\Dokumen\Index::class)->name('dokumen.index');
    Route::get('/dokumen/terbitkan/{id}', App\Livewire\Admin\Dokumen\Terbitkan::class)->name('dokumen.terbitkan');
});
Route::middleware(['auth', 'client'])->prefix('client')->name('client.')->group(function () {
    Route::get('/dashboard', App\Livewire\Client\Dashboard::class)->name('dashboard');
    Route::get('/pengajuan/index', App\Livewire\Client\Pengajuan\Index::class)->name('pengajuan.index');
    Route::get('/pengajuan/create', App\Livewire\Client\Pengajuan\Create::class)->name('pengajuan.create');
    Route::get('/pengajuan/show/{id}', App\Livewire\Client\Pengajuan\Show::class)->name('pengajuan.show');
    Route::get('/tagihan/bayar/{id}', App\Livewire\Client\Tagihan\Show::class)->name('tagihan.bayar');
});
Route::middleware(['auth'])->group(function () {
    Route::get('/download/edo/{id}', [App\Http\Controllers\DownloadController::class, 'edo'])->name('download.edo');
    Route::get('/download/invoice/{id}', [App\Http\Controllers\DownloadController::class, 'invoice'])->name('download.invoice');
    Route::get('/download/awb/{id}', [App\Http\Controllers\DownloadController::class, 'awb'])->name('download.awb');
});
Route::get('/verify-edo/{qr_string}', \App\Livewire\Public\VerifyEdo::class)->name('verify.edo');
