<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TradingAccountController;
use App\Http\Controllers\TradeController;
use App\Http\Controllers\DailyAnalysisController;
use App\Http\Controllers\TradingPlanController;
use App\Http\Controllers\SuccessController;
use App\Http\Controllers\ObjectiveController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DiscordAuthController;



Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    // Profil
    Route::get('/profile',          [ProfileController::class, 'show']);
    Route::put('/profile',          [ProfileController::class, 'update']);
    Route::put('/profile/password', [ProfileController::class, 'updatePassword']);
    Route::put('/profile/set-password', [ProfileController::class, 'setPassword']);



    // Comptes de trading
    Route::get('/accounts',         [TradingAccountController::class, 'index']);
    Route::post('/accounts',        [TradingAccountController::class, 'store']);
    Route::put('/accounts/{id}',    [TradingAccountController::class, 'update']);
    Route::delete('/accounts/{id}', [TradingAccountController::class, 'destroy']);

    // Trades par compte
    Route::get('/accounts/{accountId}/trades',              [TradeController::class, 'index']);
    Route::post('/accounts/{accountId}/trades',             [TradeController::class, 'store']);
    Route::post('/accounts/{accountId}/trades/import',   [TradeController::class, 'importBulk']);
        Route::put('/accounts/{accountId}/trades/{tradeId}',    [TradeController::class, 'update']);
    Route::delete('/accounts/{accountId}/trades/{tradeId}', [TradeController::class, 'destroy']);
    
    // Analyses du jour
    Route::get('/analyses',         [DailyAnalysisController::class, 'index']);
    Route::post('/analyses',        [DailyAnalysisController::class, 'store']);
    Route::put('/analyses/{id}',    [DailyAnalysisController::class, 'update']);
    Route::delete('/analyses/{id}', [DailyAnalysisController::class, 'destroy']);

    // Objectifs
    Route::get('/objectives',         [ObjectiveController::class, 'index']);
    Route::post('/objectives',        [ObjectiveController::class, 'store']);
    Route::put('/objectives/{id}',    [ObjectiveController::class, 'update']);
    Route::delete('/objectives/{id}', [ObjectiveController::class, 'destroy']);

    // Succès
    Route::get('/successes',         [SuccessController::class, 'index']);
    Route::post('/successes',        [SuccessController::class, 'store']);
    Route::put('/successes/{id}',    [SuccessController::class, 'update']);
    Route::delete('/successes/{id}', [SuccessController::class, 'destroy']);

    // Plan de trading
    Route::get('/plan/checklist/{date}', [TradingPlanController::class, 'getChecklist']);
    Route::post('/plan/checklist',       [TradingPlanController::class, 'saveChecklist']);
    Route::get('/plan',                  [TradingPlanController::class, 'index']);
    Route::post('/plan',                 [TradingPlanController::class, 'store']);
    Route::put('/plan/{id}',             [TradingPlanController::class, 'update']);
    Route::delete('/plan/{id}',          [TradingPlanController::class, 'destroy']);
});


