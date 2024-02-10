<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\EventController;
use App\Http\Controllers\API\UserEventController;
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

  /* Actions */
  Route::get("service", [EventController::class, "index"]);
  Route::post('service', [EventController::class, "store"]);
  Route::put('service/{id}', [EventController::class, "update"]);
  Route::delete('service/{id}', [EventController::class, "destroy"]);

  /* Events-User */
  Route::get("event", [UserEventController::class, "index"]);
  Route::post("event", [UserEventController::class, "store"]);
  Route::delete("event/{id}", [UserEventController::class, "destroy"]);

  /* Logout */
  Route::post('logout', [AuthController::class, "logout"]);

  /* Renew */
  Route::get('renew', [AuthController::class, "renew"]);
});
