<?php

use App\Http\Controllers\V1\AuthController;
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

//use App\Http\Controllers\V1\PersonController;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('register', [AuthController::class, 'register'])->name('register');
    Route::post('logout', [AuthController::class, 'logout'])->middleware(['auth:sanctum'])->name('logout');
    Route::get('user', [AuthController::class, 'user'])->middleware(['auth:sanctum'])->name('user');
    Route::post('authorize', [AuthController::class, 'authorize'])->middleware(['auth:sanctum'])->name('authorize');
});

Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {

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
    //States
    Route::post('works/states/{id}/{state}', [WorkController::class, 'changeState'])->name('works.changeState');
    Route::post('budgets/states/{id}/{state}', [BudgetController::class, 'changeState'])->name('budgets.changeState');
    //Works and invoices
    Route::post('works/materials', [WorkController::class, 'addMaterials'])->name('works.addMaterials');
    Route::post('invoices/materials', [InvoiceController::class, 'addMaterials'])->name('invoices.addMaterials');
    //Materials
    Route::get('materials/invoices/{id}', [MaterialController::class, 'getWithInvoices'])->name('materials.getWithInvoices');
    Route::get('materials/works/{id}', [MaterialController::class, 'getWithWorks'])->name('materials.getWithWorks');
    Route::get('materials/prices/{id}', [MaterialController::class, 'getWithPrices'])->name('materials.getWithPrices');
    Route::get('materials/stocks/{id}', [MaterialController::class, 'getWithStocks'])->name('materials.getWithStocks');
    //Get sorted payments
    Route::get('payments/client/{id}', [PaymentController::class, 'allClientPayments'])->name('indexClient');
    Route::get('payments/supplier/{id}', [PaymentController::class, 'allSupplierPayments'])->name('indexSupplier');
    Route::get('payments/employee/{id}', [PaymentController::class, 'allEmployeePayments'])->name('indexEmployee');
    //Search
    Route::post('people/search/{entity}/{search}', [PersonController::class, 'search'])->name('people.search');
    //States
   Route::get('budgets/states/get',[BudgetController::class, 'getStates'])->name('budgets.states');
   Route::get('works/states/get',[WorkController::class, 'getStates'])->name('works.states');

});

