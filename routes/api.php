<?php

use App\Http\Controllers\Api\AlbumController;
use App\Http\Controllers\Api\SongController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('songArtwork/{song}', [SongController::class,'getArtwork'])->name('song.artwork');
Route::get('getSong/{song}', [SongController::class,'getSong'])->name('song.file');

Route::post('songUpload', [SongController::class,'upload']);
Route::post('addSong', [AlbumController::class,'addSong']);
Route::post('songPlayed/{id}', [SongController::class,'playedSong']);
Route::get('statistic', [SongController::class,'statistic']);
Route::get('albumSongs/{id}', [AlbumController::class,'albumSongs']);

Route::apiResource('songs', SongController::class);
Route::apiResource('albums', AlbumController::class);
