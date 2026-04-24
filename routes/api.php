<?php

use App\Http\Controllers\Companies;
use App\Http\Controllers\Staff;
use Illuminate\Support\Facades\Route;

// Companies
Route::controller(Companies::class)->group(function () {
    Route::post('/admin/create_company', 'create')->name('create_company');
    Route::get('/admin/companies', 'read')->name('companiesManagement');
    Route::get('/admin/edit_company/{id}', 'edit')->name('edit_company');
    Route::post('/admin/update_company', 'update')->name('update_company');
    Route::get('/admin/delete_company/{id}', 'delete')->name('delete_company');
});

// Staff 
Route::controller(Staff::class)->group(function () {
    Route::post('/admin/create_staff', 'create')->name('create_staff');
    Route::get('/admin/staff', 'read')->name('staffManagement');
    Route::get('/admin/edit_staff/{id}', 'edit')->name('edit_staff');
    Route::post('/admin/update_staff', 'update')->name('update_staff');
    Route::get('/admin/delete_staff/{id}', 'delete')->name('delete_staff');
});

// Products
Route::controller(Products::class)->group(function () {
    Route::post('/admin/create_product', 'create')->name('create_product');
    Route::get('/admin/products', 'read')->name('productsManagement');
    Route::get('/admin/edit_product/{id}', 'edit')->name('edit_product');
    Route::post('/admin/update_product', 'update')->name('update_product');
    Route::get('/admin/delete_product/{id}', 'delete')->name('delete_product');
});
