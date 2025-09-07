<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\EventsController;
use Illuminate\Support\Facades\Route;


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

Route::get('/login', function () {
    return view('auth/login');
});

Route::get('/location', function () {
    return view('location/location');
})->name('location');

Route::get('/giving', function () {
    return view('giving/giving');
})->name('giving');

Route::get('/resource', function () {
    return view('resource');
})->middleware(['auth', 'verified'])->name('resource');

Route::get('/ministry', function () {
    return view('ministry');
})->middleware(['auth', 'verified'])->name('ministry');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//User
Route::prefix('news')->group(function() {
    Route::get('/', [NewsController::class, 'index'])->name('news.index');
    Route::get('/{news}', [NewsController::class, 'show'])->name('news.show');
});

//Event
Route::prefix('event')->group(function() {
    Route::get('/events', [EventsController::class, 'indexUser'])->name('events.index');
    Route::get('/events/{event}', [EventsController::class, 'showUser'])->name('events.showUser');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Halaman Admin Dashboard
    Route::get('/dashboard', [NewsController::class, 'NewsAdmin'])->name('admin.dashboard');
    
    // CRUD News Admin
    Route::post('/dashboard', [NewsController::class, 'store'])->name('admin.dashboard.store');
    Route::put('/dashboard/{news}', [NewsController::class, 'update'])->name('admin.dashboard.update');
    Route::delete('/dashboard/{news}', [NewsController::class, 'destroy'])->name('admin.dashboard.destroy');
    
    //Halaman Admin Event
    Route::get('/eventsAdmin', [EventsController::class, 'index'])->name('admin.event.index');
    // Events
    Route::post('/eventsAdmin', [EventsController::class, 'store'])->name('admin.event.store');
    Route::put('/eventsAdmin/{news}', [EventsController::class, 'update'])->name('admin.event.update');
    Route::delete('/eventsAdmin/{news}', [EventsController::class, 'destroy'])->name('admin.event.destroy');

    // Route::resource('/eventsAdmin', EventsController::class);
});
    
require __DIR__.'/auth.php';
