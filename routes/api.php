<?php

use App\Http\Controllers\Companies;
use App\Http\Controllers\Staff;
use App\Http\Controllers\Users;
use Illuminate\Support\Facades\Route;

// Companies
Route::middleware('auth:sanctum')->controller(Companies::class)->group(function () {
    Route::post('/admin/create_company', 'create')->name('create_company');
    Route::get('/admin/companies', 'read')->name('companiesManagement');
    Route::get('/admin/edit_company/{id}', 'edit')->name('edit_company');
    Route::post('/admin/update_company', 'update')->name('update_company');
    Route::get('/admin/delete_company/{id}', 'delete')->name('delete_company');
});

// Users
Route::post('/login', [Users::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [Users::class, 'me']);
    Route::post('/logout', [Users::class, 'logout']);
});

// Staff — table `staff`, scoped by company_id
Route::controller(Staff::class)->group(function () {
    Route::post('/admin/create_staff', 'create')->name('create_staff');
    Route::get('/admin/staff', 'read')->name('staffManagement');
    Route::get('/admin/edit_staff/{id}', 'edit')->name('edit_staff');
    Route::post('/admin/update_staff', 'update')->name('update_staff');
    Route::get('/admin/delete_staff/{id}', 'delete')->name('delete_staff');
});
