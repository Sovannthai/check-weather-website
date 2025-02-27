<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\WeatherController;

Route::get('/',[WeatherController::class, 'index'])->name('weather.index');
Route::post('/filter-weather',[WeatherController::class, 'filterWeather'])->name('weather.filter');
Route::get('/send-chat-gpt', [ChatController::class, 'sendChat'])->name('chat-gpt.send');
Auth::routes();
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

