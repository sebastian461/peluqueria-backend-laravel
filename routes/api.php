<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\EventController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
  return $request->user();
});

/* Authentication */
Route::post('register', [AuthController::class, "register"]);
Route::post('login', [AuthController::class, "login"]);

Route::middleware(['auth:sanctum'])->group(function () {

  /* Events */
  Route::get("action", [EventController::class, "index"]);
  Route::post('action', [EventController::class, "store"]);
  Route::put('action/{id}', [EventController::class, "update"]);
  Route::delete('action/{id}', [EventController::class, "destroy"]);

  /* Logout */
  Route::post('logout', [AuthController::class, "logout"]);
});
