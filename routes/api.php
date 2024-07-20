<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;

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

Route::post('login', [AuthController::class, 'login']);

// user
Route::post('users', [UserController::class, 'createUser'])->middleware(['auth:sanctum']);
Route::get('users', [UserController::class, 'getUsers'])->middleware(['auth:sanctum']);
Route::get('users/{id}', [UserController::class, 'getUser'])->middleware(['auth:sanctum']);
Route::patch('users/{id}', [UserController::class, 'updateUser'])->middleware(['auth:sanctum']);
Route::delete('users/{id}', [UserController::class, 'deleteUser'])->middleware(['auth:sanctum']);

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
