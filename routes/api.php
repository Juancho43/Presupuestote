<?php

use App\Http\Controllers\V1\BudgetController;
use App\Http\Controllers\V1\CategoryController;
use App\Http\Controllers\V1\ClientController;
use App\Http\Controllers\V1\EmployeeController;
use App\Http\Controllers\V1\InvoiceController;
use App\Http\Controllers\V1\MaterialController;
use App\Http\Controllers\V1\MeasureController;
use App\Http\Controllers\V1\PaymentController;
use App\Http\Controllers\V1\PersonController;
use App\Http\Controllers\V1\PriceController;
use App\Http\Controllers\V1\SalaryController;
use App\Http\Controllers\V1\StockController;
use App\Http\Controllers\V1\SubCategoryController;
use App\Http\Controllers\V1\SupplierController;
use App\Http\Controllers\V1\WorkController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::group(['as' => 'public.'], function() {

        Route::resource('clients', ClientController::class)->only(['index', 'show'])->names('clients');
        Route::resource('people', PersonController::class)->only(['index', 'show'])->names('people');
        Route::resource('suppliers', SupplierController::class)->only(['index', 'show'])->names('suppliers');
        Route::resource('employees', EmployeeController::class)->only(['index', 'show'])->names('employees');
        Route::resource('salaries', SalaryController::class)->only(['index', 'show'])->names('salaries');
        Route::resource('invoices', InvoiceController::class)->only(['index', 'show'])->names('invoices');
        Route::resource('budgets', BudgetController::class)->only(['index', 'show'])->names('budgets');
        Route::resource('works', WorkController::class)->only(['index', 'show'])->names('works');
        Route::resource('payments', PaymentController::class)->only(['index', 'show'])->names('payments');
        Route::resource('materials', MaterialController::class)->only(['index', 'show'])->names('materials');
        Route::resource('prices', PriceController::class)->only(['index', 'show'])->names('prices');
        Route::resource('stocks', StockController::class)->only(['index', 'show'])->names('stocks');
        Route::resource('categories', CategoryController::class)->only(['index', 'show'])->names('categories');
        Route::resource('subcategories', SubCategoryController::class)->only(['index', 'show'])->names('subcategories');
        Route::resource('measures', MeasureController::class)->only(['index', 'show'])->names('measures');
    });


    Route::middleware('auth:sanctum')->group(function () {
        Route::group(['as' => 'private.'], function(){
            Route::resource('clients/private',ClientController::class)->only([ 'store', 'update', 'destroy']);
        });
    });

});

