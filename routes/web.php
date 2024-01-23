<?php

use App\Pojo\Player;
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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/play', [App\Http\Controllers\PlayController::class, 'play'])->name('play');
Route::get('/kick/{player}', [App\Http\Controllers\PlayController::class, 'voteToKick'])->name('kick')->whereIn('player', Player::NAMES);
    