<?php

use App\Http\Controllers\ApiGame\PgsoftController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('/pgsoft/get_launch_url', [PgsoftController::class, 'GetLaunchUrl']);
Route::middleware('pgsoft')->group(function () {
    Route::post('/pgsoft/verifysession', [PgsoftController::class, 'VerifyToken']);
    Route::post('/pgsoft/cash/get', [PgsoftController::class, 'GetBalance']);
    Route::post('/pgsoft/cash/TransferInOut', [PgsoftController::class, 'TransferInOut']);
});
