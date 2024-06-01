<?php 

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::post('testSearch', [SettingsController::class, 'testSearch'])->name('testSearch');

Route::post('fetchAllData', [SettingsController::class, 'fetchAllData'])->middleware('checkHeader');