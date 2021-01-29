<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/ping', function () {
    return response()->noContent(200);
});

Route::post('/amount', [TransactionController::class, 'amount']);
Route::get("/transaction/{transaction_id}", [TransactionController::class, 'show']);
Route::get("/balance/{account_id}", [TransactionController::class, 'balance']);
Route::get("/max_transaction_volume", [TransactionController::class, 'maxVolume']);
