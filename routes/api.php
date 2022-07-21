<?php

use App\Http\Controllers;
use App\Models\Category;
use Illuminate\Http\Request;
// use Illuminate\Routing\Route;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::controller(CategoryController::class)->group(function(){
//     Route::get('/catego/{id}', 'show');
//     Route::post('/orders', 'store');
// });

Route::group(['namespace' => 'Api'], function () {
    $exceptCreateAndEdit = [
        'except' => ['create', 'edit']
    ];
    Route::resource('categories', 'CategoryController', $exceptCreateAndEdit);
    Route::resource('genres', 'GenreController', $exceptCreateAndEdit);
});