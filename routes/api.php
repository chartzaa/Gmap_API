<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\MapController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('test', [MapController::class, 'test']);

Route::get('place-autocomplete', [MapController::class, 'placeAutocomplet']);
Route::get('place-search', [MapController::class, 'placeSearch']);
Route::get('place-detail', [MapController::class, 'placeDetail']);
Route::get('place-near-by-search', [MapController::class, 'nearBySearch']);
Route::get('get-data', [MapController::class, 'getData']);

Route::get('/',function(){
    return response()->json([
        'message' => 'Hello'
    ]);
});

