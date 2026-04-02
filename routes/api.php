<?php

use App\Http\Controllers\Api\CompanyController;

Route::prefix('company')->group(function () {
    Route::get('{edrpou}/versions', [CompanyController::class, 'getVersionsByEdrpou']);
    Route::post('', [CompanyController::class, 'store']);
});

