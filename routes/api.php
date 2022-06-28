<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/users', [UserController::class, 'index']);
Route::get('/wallets', [UserController::class, 'getAllWallets']);
Route::get('/user/wallet/transaction/{id}', [UserController::class, 'walletUserTransactionByWalletId']);
Route::get('/users/wallets/transactions/summaries', [UserController::class, 'countSummary']);
Route::get('/wallets/type', [UserController::class, 'getWalletType']);
Route::group(['middleware' => 'auth:sanctum'], function () {

    Route::post('/wallet', [UserController::class, 'createWallet']);
    Route::post('/wallets/wallet', [UserController::class, 'walletToWallet']);
    Route::get('/user-wallet-transaction', [UserController::class, 'userWalletTransaction']);
    Route::post('wallets/fund', [UserController::class, 'fundWallet']);
    Route::patch('/users/set-pin', [UserController::class, 'setPin']);
});
