<?php

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

Route::get('/tiktok-profile-scraper/{userId}', 'TikTokScraperController@profile');

Route::get('/tiktok-video-scraper/{profileId}/{videoId}', 'TikTokScraperController@video');
