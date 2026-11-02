<?php
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// UC01: Trang hero lặn xuống biển
Route::get('/', [LandingController::class, 'index'])->name('landing');

// Route tạm thời cho /home và /admin (sẽ gắn Controller thật ở Đợt 3)
Route::middleware('auth')->group(function () {
    Route::get('/home', function () {
        return redirect('/');
    })->name('home');

    Route::get('/dashboard', function () {
        return redirect()->route('home');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', function () {
        return 'Khu vực Admin OceanQuiz';
    })->name('dashboard');
});

require __DIR__.'/auth.php';