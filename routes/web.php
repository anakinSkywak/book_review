<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;

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


Route::group(['prefix' => 'account'], function() {
    Route::group(['middleware' => 'guest'], function() {
        // dang ky
        Route::get('register', [AccountController::class,'register'])->name('account.register');
        Route::post('register', [AccountController::class,'processRegister'])->name('account.processRegister');

        // dang nhap
        Route::get('login', [AccountController::class,'login'])->name('account.login');
        Route::post('login', [AccountController::class,'authenticate'])->name('account.authenticate');
    });
    Route::group(['middleware' => 'auth'], function() {
        // profile
        Route::get('profile', [AccountController::class,'profile'])->name('account.profile');
        // update profile
        Route::post('updateProfile', [AccountController::class,'updateProfile'])->name('account.updateProfile');
        // dang xuat
        Route::get('logout', [AccountController::class,'logout'])->name('account.logout');
    });
});