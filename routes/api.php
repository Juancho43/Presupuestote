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
        // TODO: Controller refactor - Copy all actual files to generate new ones using Prueba.php as template
        //Crud entities routes
        Route::resource('clients', ClientController::class)->names('clients');
        Route::resource('people', PersonController::class)->names('people');
        Route::resource('suppliers', SupplierController::class)->names('suppliers');
        Route::resource('employees', EmployeeController::class)->names('employees');
        Route::resource('salaries', SalaryController::class)->names('salaries');
        Route::resource('invoices', InvoiceController::class)->names('invoices');
        Route::resource('budgets', BudgetController::class)->names('budgets');
        Route::resource('works', WorkController::class)->names('works');
        Route::resource('payments', PaymentController::class)->names('payments');
        Route::resource('materials', MaterialController::class)->names('materials');
        Route::resource('prices', PriceController::class)->names('prices');
        Route::resource('stocks', StockController::class)->names('stocks');
        Route::resource('categories', CategoryController::class)->names('categories');
        Route::resource('subcategories', SubCategoryController::class)->names('subcategories');
        Route::resource('measures', MeasureController::class)->names('measures');
        //Pricing
        Route::get('budgets/updatePrice/{id}', [BudgetController::class, 'updateBudgetPrice'])->name('budgets.updatePrice');
        Route::get('invoices/updateTotal/{id}', [InvoiceController::class, 'updateInvoiceTotal'])->name('invoices.updateTotal');
        //Works and invoices
        Route::post('works/materials', [WorkController::class, 'addMaterials'])->name('works.addMaterials');
        Route::post('invoices/materials', [InvoiceController::class, 'addMaterials'])->name('invoices.addMaterials');
        //Add Payments
        Route::post('payments/budget', [PaymentController::class, 'addPaymentToBudget'])->name('payments.toBudget');
        Route::post('payments/invoice', [PaymentController::class, 'addPaymentToInvoice'])->name('payments.toInvoice');
        Route::post('payments/salary', [PaymentController::class, 'addPaymentToSalary'])->name('payments.toSalary');
        //Get sorted payments
        Route::get('payments/client/{id}', [PaymentController::class, 'allClientPayments'])->name('indexClient');
        Route::get('payments/supplier/{id}', [PaymentController::class, 'allSupplierPayments'])->name('indexSupplier');
        Route::get('payments/employee/{id}', [PaymentController::class, 'allEmployeePayments'])->name('indexEmployee');

    });



});

