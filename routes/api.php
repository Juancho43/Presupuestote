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
    Route::resource('clients', ClientController::class)->names('clients')->except('index');
    Route::resource('people', PersonController::class)->names('people')->except('index');
    Route::resource('suppliers', SupplierController::class)->names('suppliers')->except('index');
    Route::resource('employees', EmployeeController::class)->names('employees')->except('index');
    Route::resource('salaries', SalaryController::class)->names('salaries')->except('index');
    Route::resource('invoices', InvoiceController::class)->names('invoices')->except('index');
    Route::resource('budgets', BudgetController::class)->names('budgets')->except('index');
    Route::resource('works', WorkController::class)->names('works')->except('index');
    Route::resource('payments', PaymentController::class)->names('payments')->except('index');
    Route::resource('materials', MaterialController::class)->names('materials')->except('index');
    Route::resource('prices', PriceController::class)->names('prices')->except('index');
    Route::resource('stocks', StockController::class)->names('stocks')->except('index');
    Route::resource('categories', CategoryController::class)->names('categories')->except('index');
    Route::resource('subcategories', SubCategoryController::class)->names('subcategories')->except('index');
    Route::resource('measures', MeasureController::class)->names('measures')->except('index');
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
    Route::post('subcategories/search/{search}', [SubCategoryController::class, 'search'])->name('subcategories.search');
    Route::post('categories/search/{search}', [CategoryController::class, 'search'])->name('categories.search');
    Route::post('measures/search/{search}', [MeasureController::class, 'search'])->name('measures.search');
    Route::post('materials/search/{search}', [MaterialController::class, 'search'])->name('materials.search');
    //States
   Route::get('budgets/states/get',[BudgetController::class, 'getStates'])->name('budgets.states');
   Route::get('works/states/get',[WorkController::class, 'getStates'])->name('works.states');

   //Pagination
    Route::get('people/paginate/{page}', [PersonController::class, 'index'])->name('people.index');
    Route::get('suppliers/paginate/{page}', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::get('employees/paginate/{page}', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('salaries/paginate/{page}', [SalaryController::class, 'index'])->name('salaries.index');
    Route::get('invoices/paginate/{page}', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('budgets/paginate/{page}', [BudgetController::class, 'index'])->name('budgets.index');
    Route::get('clients/paginate/{page}', [ClientController::class, 'index'])->name('clients.index');
    Route::get('works/paginate/{page}', [WorkController::class, 'index'])->name('works.index');
    Route::get('payments/paginate/{page}', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('materials/paginate/{page}', [MaterialController::class, 'index'])->name('materials.index');
    Route::get('prices/paginate/{page}', [PriceController::class, 'index'])->name('prices.index');
    Route::get('stocks/paginate/{page}', [StockController::class, 'index'])->name('stocks.index');
    Route::get('categories/paginate/{page}', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('subcategories/paginate/{page}', [SubCategoryController::class, 'index'])->name('subcategories.index');
    Route::get('measures/paginate/{page}', [MeasureController::class, 'index'])->name('measures.index');


});

