<?php

use App\Http\Controllers\Companies;
use App\Http\Controllers\Staff;
use App\Http\Controllers\Products;
use App\Http\Controllers\Clients;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Sales;
use App\Http\Controllers\Expenses;
use App\Http\Controllers\Suppliers;
use App\Http\Controllers\Users;

// Companies
Route::middleware('auth:sanctum')->controller(Companies::class)->group(function () {
    Route::post('/admin/create_company', 'create')->name('create_company');
    Route::get('/admin/companies', 'read')->name('companiesManagement');
    Route::get('/admin/all_companies', 'readAll')->name('allCompaniesManagement');
    Route::get('/admin/active_company', 'activeCompany')->name('activeCompany');
    Route::post('/admin/set_active_company/{id}', 'setActive')->name('set_active_company');
    Route::get('/admin/edit_company/{id}', 'edit')->name('edit_company');
    Route::post('/admin/update_company', 'update')->name('update_company');
    Route::get('/admin/delete_company/{id}', 'delete')->name('delete_company');
});

// Clients
Route::middleware('auth:sanctum')->controller(Clients::class)->group(function () {
    Route::post('/admin/create_client', 'create')->name('create_client');
    Route::get('/admin/clients', 'read')->name('clientsManagement');
    Route::get('/admin/edit_client/{id}', 'edit')->name('edit_client');
    Route::post('/admin/update_client', 'update')->name('update_client');
    Route::get('/admin/delete_client/{id}', 'delete')->name('delete_client');
});

// Expenses
Route::middleware('auth:sanctum')->controller(Expenses::class)->group(function () {
    Route::post('/admin/create_expense', 'create')->name('create_expense');
    Route::get('/admin/expenses', 'read')->name('expensesManagement');
    Route::get('/admin/edit_expense/{id}', 'edit')->name('edit_expense');
    Route::post('/admin/update_expense', 'update')->name('update_expense');
    Route::get('/admin/delete_expense/{id}', 'delete')->name('delete_expense');
});


// Suppliers
Route::middleware('auth:sanctum')->controller(Suppliers::class)->group(function () {
    Route::post('/admin/create_supplier', 'create')->name('create_supplier');
    Route::get('/admin/suppliers', 'read')->name('suppliersManagement');
    Route::get('/admin/edit_supplier/{id}', 'edit')->name('edit_supplier');
    Route::post('/admin/update_supplier', 'update')->name('update_supplier');
    Route::get('/admin/delete_supplier/{id}', 'delete')->name('delete_supplier');
});

// Users
Route::controller(Users::class)->group(function () {
    Route::post('/login', 'login')->name('login');
});
Route::middleware('auth:sanctum')->controller(Users::class)->group(function () {
    Route::post('/admin/create_user', 'create')->name('create_user');
    Route::get('/admin/users', 'read')->name('usersManagement');
    Route::get('/admin/edit_user/{id}', 'edit')->name('edit_user');
    Route::post('/admin/update_user', 'update')->name('update_user');
    Route::get('/admin/delete_user/{id}', 'delete')->name('delete_user');
    Route::get('/me', 'me')->name('me');
    Route::post('/admin/update_account', 'updateAccount')->name('update_account');
    Route::post('/logout', 'logout')->name('logout');
});

// Staff
Route::middleware('auth:sanctum')->controller(Staff::class)->group(function () {
    Route::post('/admin/create_staff', 'create')->name('create_staff');
    Route::get('/admin/staff', 'read')->name('staffManagement');
    Route::get('/admin/edit_staff/{id}', 'edit')->name('edit_staff');
    Route::post('/admin/update_staff', 'update')->name('update_staff');
    Route::get('/admin/delete_staff/{id}', 'delete')->name('delete_staff');
});

// Products
Route::middleware('auth:sanctum')->controller(Products::class)->group(function () {
    Route::post('/admin/create_product', 'create')->name('create_product');
    Route::get('/admin/products', 'read')->name('productsManagement');
    Route::get('/admin/edit_product/{id}', 'edit')->name('edit_product');
    Route::post('/admin/update_product', 'update')->name('update_product');
    Route::get('/admin/delete_product/{id}', 'delete')->name('delete_product');
});

// Sales
Route::middleware('auth:sanctum')->controller(Sales::class)->group(function () {
    Route::post('/admin/create_sale', 'create')->name('create_sale');
    Route::get('/admin/sales', 'read')->name('salesManagement');
    Route::get('/admin/edit_sale/{id}', 'edit')->name('edit_sale');
    Route::post('/admin/update_sale', 'update')->name('update_sale');
    Route::get('/admin/delete_sale/{id}', 'delete')->name('delete_sale');
});
