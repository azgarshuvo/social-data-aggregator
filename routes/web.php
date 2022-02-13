<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::any('/edit/profile', [App\Http\Controllers\HomeController::class, 'editProfile'])->name('edit-profile');
Route::get('/dashboard/v3', [App\Http\Controllers\HomeController::class, 'dashboard_v3'])->name('dashboard-v3');
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/create', [App\Http\Controllers\AuthorController::class, 'create'])->name('create-author');
Route::post('/store', [App\Http\Controllers\AuthorController::class, 'store'])->name('store-author');

#Dashboard
Route::get('/dashboard/v1', [App\Http\Controllers\DashboardController::class, 'dashboard'])->name('dashboard-v1');
Route::get('/dashboard/v2', [App\Http\Controllers\DashboardController::class, 'dashboard_v2'])->name('dashboard-v2');
Route::get('/dashboard/v4', [App\Http\Controllers\DashboardController::class, 'dashboard_v4'])->name('dashboard-v4');
Route::get('/dashboard/chart', [App\Http\Controllers\DashboardController::class, 'chart'])->name('dashboard-chart');


#API
Route::get('/twitter/data', [App\Http\Controllers\ApiController::class, 'twitterData'])->name('twitter-data');
Route::get('/reddit/data', [App\Http\Controllers\ApiController::class, 'redditData'])->name('reddit-data');
Route::get('/linkedin/data', [App\Http\Controllers\ApiController::class, 'linkedInData'])->name('linkedin-data');
Route::get('/delete/data', [App\Http\Controllers\ApiController::class, 'deleteData'])->name('delete-data');
Route::get('/insert/topic', [App\Http\Controllers\ApiController::class, 'insertTopics'])->name('insert-topic');
