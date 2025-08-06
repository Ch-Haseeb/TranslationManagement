<?php

use App\Http\Controllers\api\TranslationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::prefix('v1')->middleware(['api', 'auth.api'])->group(function () {
    Route::apiResource('translations', TranslationController::class);
    Route::get('translations/export/{locale}', [TranslationController::class, 'expyesort']);
});
