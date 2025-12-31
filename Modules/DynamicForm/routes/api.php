<?php

use Illuminate\Support\Facades\Route;
use Modules\DynamicForm\Http\Controllers\ExportController;
use Modules\DynamicForm\Http\Controllers\FormController;
use Modules\DynamicForm\Http\Controllers\FormFieldController;
use Modules\DynamicForm\Http\Controllers\ImportController;
use Modules\DynamicForm\Http\Controllers\SubmissionController;


Route::middleware(["auth:api"])->group(function() {
    Route::middleware(['role:Employee'])->group(function () {
        Route::post('forms/{form}/submissions', [SubmissionController::class, 'store']);
        Route::get('submissions/{submission}', [SubmissionController::class, 'show']);
        Route::patch('forms/{form}/submissions/{submission}', [SubmissionController::class, 'update']);
    });
    Route::get("forms/{form}/fields", [FormFieldController::class, "index"]);
    Route::middleware(['role:Admin|HR'])->group(function() {
        Route::post("forms/{form}/publish", [FormController::class, "publish"]);
        Route::apiResource("forms/{form}/fields", FormFieldController::class)->except(["index", "show"]);
        Route::get("forms/{form}/submissions", [SubmissionController::class, "index"]);
        Route::get("forms/{form}/export", [ExportController::class, "export"]);
        Route::post("forms/{form}/import", [ImportController::class, "import"]);
        Route::apiResource("forms", FormController::class)->except(['index', 'show']);
    });

    Route::get("forms", [FormController::class, "index"]);
    Route::get("forms/{form}", [FormController::class, "show"]);

    
});