<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| WEB Routes
|--------------------------------------------------------------------------
|
| Here is where you can register WEB routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "web" middleware group. Enjoy building your WEB!
|
*/

Route::get('/', function () {
    return response()->json(['status'=>'Available', 'time'=>NOW()]);
});

Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

// Admin Routes
Route::prefix('admin')->group(function () {
    // Login routes (sin autenticación)
    Route::get('/', [AdminController::class, 'showLogin'])->name('admin.login');
    Route::get('/login', [AdminController::class, 'showLogin'])->name('admin.login.show');
    Route::post('/login', [AdminController::class, 'login'])->name('admin.login.post');
    
    // Protected routes (requieren autenticación y ser admin)
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/home', [AdminController::class, 'home'])->name('admin.home');
        Route::get('/nuevos-arribos', [AdminController::class, 'nuevosArribos'])->name('admin.nuevos-arribos');
        Route::get('/ejemplo', [AdminController::class, 'ejemplo'])->name('admin.ejemplo');
        Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout');
    });
});
