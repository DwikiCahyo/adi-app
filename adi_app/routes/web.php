<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\ResourceFileController;
use App\Http\Controllers\MinistryController;
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

// User Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//Guest
//News
Route::prefix('news')->group(function() {
    Route::get('/', [NewsController::class, 'index'])->name('news.index');
    Route::get('/{news}', [NewsController::class, 'show'])->name('news.show');
});

//Event
Route::prefix('event')->group(function() {
    Route::get('/', [EventsController::class, 'indexUser'])->name('events.index');
    Route::get('/events/{event}', [EventsController::class, 'showUser'])->name('events.showUser');
});

//Ministry
Route::prefix('ministry')->group(function() {
    Route::get('/', [MinistryController::class, 'indexUser'])->name('ministry.index');
    Route::get('/ministry/{ministry}', [MinistryController::class, 'showUser'])->name('ministry.showUser');
});

//Resource
Route::prefix('resource')->group(function() {
    Route::get('/', [ResourceController::class, 'index'])->name('resource.index');
    // Show Latest Sermon
    Route::get('/LatestSermon/show', [ResourceController::class, 'show'])->name('resource.show');

    // Show Good News
    Route::get('/GoodNews/show', [ResourceFileController::class, 'show'])->name('resourcefile.show');
    Route::get('/GoodNews/{id}', [ResourceFileController::class, 'showfile'])->name('resourcefile.showfile');
    Route::get('/GoodNews/{id}/download', [ResourceFileController::class, 'download'])->name('resourcefile.download');
});

///////////////////////////////////////////////**************************************/////////////////////////////////////////////////////////////

// Admin
Route::middleware(['auth', 'verified'])->group(function () {
    // Halaman Admin Dashboard
    Route::get('/dashboard', [NewsController::class, 'NewsAdmin'])->name('admin.dashboard');
    // News Admin
    Route::post('/dashboard', [NewsController::class, 'store'])->name('admin.dashboard.store');
    Route::put('/dashboard/{news}', [NewsController::class, 'update'])->name('admin.dashboard.update');
    Route::delete('/dashboard/{news}', [NewsController::class, 'destroy'])->name('admin.dashboard.destroy');
    
    //Halaman Admin Event
    Route::get('/eventsAdmin', [EventsController::class, 'index'])->name('admin.event.index');
    // Events Admin
    Route::post('/eventsAdmin', [EventsController::class, 'store'])->name('admin.event.store');
    Route::put('/eventsAdmin/{event}', [EventsController::class, 'update'])->name('admin.event.update');
    Route::delete('/eventsAdmin/{event}', [EventsController::class, 'destroy'])->name('admin.event.destroy');

    //Halaman Admin Resource
    Route::get('/resourceAdmin', [ResourceController::class, 'ResourceAdmin'])->name('admin.resource.index');
    // Resource Admin
    Route::post('/resourceAdmin', [ResourceController::class, 'store'])->name('admin.resource.store');
    Route::put('/resourceAdmin/{resource}', [ResourceController::class, 'update'])->name('admin.resource.update');
    Route::delete('/resourceAdmin/{resource}', [ResourceController::class, 'destroy'])->name('admin.resource.destroy');

    //Halaman Admin ResourceFile
    Route::get('/resourcefileAdmin', [ResourceFileController::class, 'ResourceFileAdmin'])->name('admin.resourcefile.file');
    // Resource File Admin
    Route::post('/resourcefileAdmin', [ResourceFileController::class, 'store'])->name('admin.resourcefile.store');
    Route::put('/resourcefileAdmin/{resource}', [ResourceFileController::class, 'update'])->name('admin.resourcefile.update');
    Route::delete('/resourcefileAdmin/{resource}', [ResourceFileController::class, 'destroy'])->name('admin.resourcefile.destroy');

    //Halaman Admin Ministry
    Route::get('/ministryAdmin', [MinistryController::class, 'MinistryAdmin'])->name('admin.ministry.index');
    // Ministry Admin
    Route::post('/ministryAdmin', [MinistryController::class, 'store'])->name('admin.ministry.store');
    Route::put('/ministryAdmin/{ministry}', [MinistryController::class, 'update'])->name('admin.ministry.update');
    Route::delete('/ministryAdmin/{ministry}', [MinistryController::class, 'destroy'])->name('admin.ministry.destroy');

});
    
require __DIR__.'/auth.php';
