<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DataController;
use App\Http\Controllers\API\FormController;
use App\Http\Controllers\API\ReportController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/data', [DataController::class, 'data']);
    Route::post('/locations', [DataController::class, 'locations']);
    Route::post('/summary', [DataController::class, 'summary']);

    // form - cattle registration
    Route::post('/form/store-cattle', [FormController::class, 'storeCattle']);
    Route::post('/form/store-milk-production', [FormController::class, 'storeMilkProduction']);
    Route::post('/form/store-vaccine-info', [FormController::class, 'storeVaccineInfo']);
    Route::post('/form/store-disease-info', [FormController::class, 'storeDiseaseInfo']);
    Route::post('/form/store-weight-info', [FormController::class, 'storeWeightInfo']);
    Route::post('/form/store-food-consumption', [FormController::class, 'storeFoodConsumption']);
    Route::post('/form/store-manual-hit', [FormController::class, 'storeManualHit']);
    Route::post('/form/store-impregnation', [FormController::class, 'storeImpregnation']);
    Route::post('/form/store-pregnancy-exam', [FormController::class, 'storePregnancyExam']);
    Route::post('/form/store-abortion-info', [FormController::class, 'storeAbortionInfo']);
    Route::post('/form/store-calf', [FormController::class, 'storeCalf']);
    Route::post('/form/store-account-entry', [FormController::class, 'storeMAccountEntry']);

    // report
    Route::post('/report/cattle', [ReportController::class, 'cattleReport']);
    Route::post('/report/milk-production', [ReportController::class, 'milkProductionReport']);
    Route::post('/report/vaccine', [ReportController::class, 'vaccineReport']);
    Route::post('/report/vaccine-calender', [ReportController::class, 'vaccineCalenderReport']);
    Route::post('/report/disease', [ReportController::class, 'diseaseReport']);
    Route::post('/report/impregnation', [ReportController::class, 'impregnationReport']);
    Route::post('/report/calf', [ReportController::class, 'calfReport']);
    Route::post('/report/pregnancy-exam', [ReportController::class, 'pregnancyExamReport']);
    Route::post('/report/abortion', [ReportController::class, 'abortionReport']);
    Route::post('/report/food-consumption-weight', [ReportController::class, 'foodConsumptionAndWeightReport']);
    Route::post('/report/account-entry', [ReportController::class, 'mAccountEntryReport']);
    Route::post('/report/account-entry-all', [ReportController::class, 'mAccountEntryReportByDateRange']);
    Route::post('/report/farm', [ReportController::class, 'farmReport']);

});
