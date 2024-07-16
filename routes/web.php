<?php

use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\WeatherController;
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

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', [WeatherController::class, 'index']);
Route::get('/weather', [WeatherController::class, 'getWeather']);
Route::post('/subscribe', [SubscriptionController::class, 'subscribe']);
Route::get('/subscribe/confirm/{token}', [SubscriptionController::class, 'confirmSubscription']);
Route::post('/unsubscribe', [SubscriptionController::class, 'unsubscribe']);
Route::get('/unsubscribe/confirm/{token}', [SubscriptionController::class, 'confirmUnsubscription']);

