<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;

require __DIR__.'/auth.php';

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('invoices.index');
    }
    return redirect()->route('login');
});
// Buat route dashboard sederhana jika belum ada
Route::get('/dashboard', function () {
     // return view('dashboard'); // Buat view dashboard.blade.php jika perlu
    return redirect()->route('invoices.index');// Atau langsung ke invoice
})->middleware(['auth'])->name('dashboard');

// Kelompokkan route invoice di dalam middleware auth
Route::middleware('auth')->group(function () {
     // Jika Anda ingin halaman profile manual, buat controller dan route-nya di sini
    // Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::resource('invoices', InvoiceController::class);
});
Route::get('/dashboard', function () {
    // Arahkan ke halaman invoice jika sudah login
    return redirect()->route('invoices.index');
})->middleware(['auth'])->name('dashboard');


Route::get('/dashboard', function () {
    // Arahkan ke halaman invoice jika sudah login
    return redirect()->route('invoices.index');
})->middleware(['auth'])->name('dashboard');


Route::middleware('auth')->group(function () {
    // Route yang bisa diakses semua user yang login (admin dan user biasa)
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');

    // Route yang hanya bisa diakses oleh admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
        Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
        Route::get('/invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit');
        Route::put('/invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');
        Route::patch('/invoices/{invoice}', [InvoiceController::class, 'update']); // Untuk form PATCH
        Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
    });
});


require __DIR__.'/auth.php';