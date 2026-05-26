<?php

use App\Http\Controllers\Companies;
use App\Http\Controllers\Staff;
use App\Http\Controllers\Products;
use App\Http\Controllers\Materials;
use App\Http\Controllers\MaterialsStock;
use App\Http\Controllers\Production;
use App\Http\Controllers\Clients;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Sales;
use App\Http\Controllers\Expenses;
use App\Http\Controllers\Suppliers;
use App\Http\Controllers\Users;
use App\Http\Controllers\Machines;
use App\Http\Controllers\Warehouses;
use App\Http\Controllers\Planification;
use App\Http\Controllers\Maintenances;
use App\Http\Controllers\Vacations;
use App\Http\Controllers\Contracts;
use App\Http\Controllers\Dashboard;
use App\Http\Controllers\Reports;
use App\Http\Controllers\Orders;
use App\Http\Controllers\Salaries;

// Dashboard
Route::middleware(['auth:sanctum', 'role:admin,manager'])->get('/admin/dashboard', [Dashboard::class, 'index'])->name('dashboard');

// Companies
Route::controller(Companies::class)->group(function () {
    Route::post('/admin/create_company', 'create')->middleware(['auth:sanctum', 'role:admin'])->name('create_company');
    Route::get('/admin/companies', 'read')->middleware(['auth:sanctum', 'role:admin,manager'])->name('companiesManagement');
    Route::get('/admin/all_companies', 'readAll')->middleware(['auth:sanctum', 'role:admin,manager'])->name('allCompaniesManagement');
    Route::get('/admin/active_company', 'activeCompany')->middleware(['auth:sanctum', 'role:admin,manager'])->name('activeCompany');
    Route::post('/admin/set_active_company/{id}', 'setActive')->middleware(['auth:sanctum', 'role:admin'])->name('set_active_company');
    Route::get('/admin/edit_company/{id}', 'edit')->middleware(['auth:sanctum', 'role:admin'])->name('edit_company');
    Route::post('/admin/update_company', 'update')->middleware(['auth:sanctum', 'role:admin'])->name('update_company');
    Route::get('/admin/delete_company/{id}', 'delete')->middleware(['auth:sanctum', 'role:admin'])->name('delete_company');
});

// Clients
Route::middleware(['auth:sanctum', 'role:admin,manager'])->controller(Clients::class)->group(function () {
    Route::post('/admin/create_client', 'create')->name('create_client');
    Route::get('/admin/clients', 'read')->name('clientsManagement');
    Route::get('/admin/allClients', 'readAll')->name('allClientsManagement');
    Route::get('/admin/edit_client/{id}', 'edit')->name('edit_client');
    Route::post('/admin/update_client', 'update')->name('update_client');
    Route::get('/admin/delete_client/{id}', 'delete')->name('delete_client');
});

// Expenses
Route::middleware(['auth:sanctum', 'role:admin'])->controller(Expenses::class)->group(function () {
    Route::post('/admin/create_expense', 'create')->name('create_expense');
    Route::get('/admin/expenses', 'read')->name('expensesManagement');
    Route::get('/admin/edit_expense/{id}', 'edit')->name('edit_expense');
    Route::post('/admin/update_expense', 'update')->name('update_expense');
    Route::get('/admin/delete_expense/{id}', 'delete')->name('delete_expense');
    Route::get('/admin/expenses_report', 'report')->name('expenses_report');

});

// Suppliers
Route::middleware(['auth:sanctum', 'role:admin,manager'])->controller(Suppliers::class)->group(function () {
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
Route::controller(Users::class)->group(function () {
    Route::post('/admin/create_user', 'create')->middleware(['auth:sanctum', 'role:admin'])->name('create_user');
    Route::get('/admin/users', 'read')->middleware(['auth:sanctum', 'role:admin'])->name('usersManagement');
    Route::get('/admin/edit_user/{id}', 'edit')->middleware(['auth:sanctum', 'role:admin'])->name('edit_user');
    Route::post('/admin/update_user', 'update')->middleware(['auth:sanctum', 'role:admin'])->name('update_user');
    Route::get('/admin/delete_user/{id}', 'delete')->middleware(['auth:sanctum', 'role:admin'])->name('delete_user');
    Route::get('/me', 'me')->middleware(['auth:sanctum', 'role:admin,manager'])->name('me');
    Route::post('/admin/update_account', 'updateAccount')->middleware(['auth:sanctum', 'role:admin,manager'])->name('update_account');
    Route::post('/logout', 'logout')->middleware(['auth:sanctum', 'role:admin,manager'])->name('logout');
});

// Staff
Route::middleware(['auth:sanctum', 'role:admin,manager'])->controller(Staff::class)->group(function () {
    Route::post('/admin/create_staff', 'create')->name('create_staff');
    Route::get('/admin/staff', 'read')->name('staffManagement');
    Route::get('/admin/edit_staff/{id}', 'edit')->name('edit_staff');
    Route::post('/admin/update_staff', 'update')->name('update_staff');
    Route::get('/admin/delete_staff/{id}', 'delete')->name('delete_staff');
});

// Products
Route::middleware(['auth:sanctum', 'role:admin,manager'])->controller(Products::class)->group(function () {
    Route::post('/admin/create_product', 'create')->name('create_product');
    Route::get('/admin/products', 'read')->name('productsManagement');
    Route::get('/admin/edit_product/{id}', 'edit')->name('edit_product');
    Route::post('/admin/update_product', 'update')->name('update_product');
    Route::get('/admin/delete_product/{id}', 'delete')->name('delete_product');
});

// Materials
Route::middleware(['auth:sanctum', 'role:admin,manager'])->controller(Materials::class)->group(function () {
    Route::post('/admin/create_material', 'create')->name('create_material');
    Route::get('/admin/materials', 'read')->name('materialsManagement');
    Route::get('/admin/edit_material/{id}', 'edit')->name('edit_material');
    Route::post('/admin/update_material', 'update')->name('update_material');
    Route::get('/admin/delete_material/{id}', 'delete')->name('delete_material');
});

// Production
Route::middleware(['auth:sanctum', 'role:admin,manager'])->controller(Production::class)->group(function () {
    Route::post('/admin/create_production', 'create')->name('create_production');
    Route::get('/admin/production', 'read')->name('productionManagement');
    Route::get('/admin/edit_production/{id}', 'edit')->name('edit_production');
    Route::post('/admin/update_production', 'update')->name('update_production');
    Route::get('/admin/delete_production/{id}', 'delete')->name('delete_production');
});

// Materials Stock
Route::middleware(['auth:sanctum', 'role:admin,manager'])->controller(MaterialsStock::class)->group(function () {
    Route::post('/admin/create_materials_stock', 'create')->name('create_materials_stock');
    Route::get('/admin/materials_stock', 'read')->name('materials_stockManagement');
    Route::get('/admin/edit_materials_stock/{id}', 'edit')->name('edit_materials_stock');
    Route::post('/admin/update_materials_stock', 'update')->name('update_materials_stock');
    Route::get('/admin/delete_materials_stock/{id}', 'delete')->name('delete_materials_stock');
});

// Sales
Route::middleware(['auth:sanctum', 'role:admin,manager'])->controller(Sales::class)->group(function () {
    Route::post('/admin/create_sale', 'create')->name('create_sale');
    Route::get('/admin/sales', 'read')->name('salesManagement');
    Route::get('/admin/edit_sale/{sale_number}', 'edit')->name('edit_sale');
    Route::post('/admin/update_sale/{sale_number}', 'update')->name('update_sale');
    Route::get('/admin/delete_sale/{sale_number}', 'delete')->name('delete_sale');
    Route::get('/admin/invoice/{sale_number}', 'invoice')->name('invoice');
});

// Orders
Route::middleware(['auth:sanctum', 'role:admin,manager'])->controller(Orders::class)->group(function () {
    Route::post('/admin/create_order', 'create')->name('create_order');
    Route::get('/admin/orders', 'read')->name('ordersManagement');
    Route::get('/admin/edit_order/{order_number}', 'edit')->name('edit_order');
    Route::post('/admin/update_order', 'update')->name('update_order');
    Route::get('/admin/delete_order/{order_number}', 'delete')->name('delete_order');
    Route::post('/admin/convert_order_to_sale/{order_number}', 'convertToSale')->name('convert_order_to_sale');
});

// Machines
Route::middleware(['auth:sanctum', 'role:admin,manager'])->controller(Machines::class)->group(function () {
    Route::post('/admin/create_machine', 'create')->name('create_machine');
    Route::get('/admin/machines', 'read')->name('machinesManagement');
    Route::get('/admin/edit_machine/{id}', 'edit')->name('edit_machine');
    Route::post('/admin/update_machine', 'update')->name('update_machine');
    Route::get('/admin/delete_machine/{id}', 'delete')->name('delete_machine');
});

// Warehouses
Route::middleware(['auth:sanctum', 'role:admin,manager'])->controller(Warehouses::class)->group(function () {
    Route::post('/admin/create_warehouse', 'create')->name('create_warehouse');
    Route::get('/admin/warehouses', 'read')->name('warehousesManagement');
    Route::get('/admin/edit_warehouse/{id}', 'edit')->name('edit_warehouse');
    Route::post('/admin/update_warehouse', 'update')->name('update_warehouse');
    Route::get('/admin/delete_warehouse/{id}', 'delete')->name('delete_warehouse');
});

// Planification
Route::middleware(['auth:sanctum', 'role:admin,manager'])->controller(Planification::class)->group(function () {
    Route::post('/admin/create_planification', 'create')->name('create_planification');
    Route::get('/admin/planification', 'read')->name('planificationManagement');
    Route::get('/admin/edit_planification/{id}', 'edit')->name('edit_planification');
    Route::post('/admin/update_planification', 'update')->name('update_planification');
    Route::get('/admin/delete_planification/{id}', 'delete')->name('delete_planification');
});

// Maintenances
Route::middleware(['auth:sanctum', 'role:admin,manager'])->controller(Maintenances::class)->group(function () {
    Route::post('/admin/create_maintenance', 'create')->name('create_maintenance');
    Route::get('/admin/maintenances', 'read')->name('maintenancesManagement');
    Route::get('/admin/edit_maintenance/{id}', 'edit')->name('edit_maintenance');
    Route::post('/admin/update_maintenance', 'update')->name('update_maintenance');
    Route::get('/admin/delete_maintenance/{id}', 'delete')->name('delete_maintenance');
});

// Vacations
Route::middleware(['auth:sanctum', 'role:admin,manager'])->controller(Vacations::class)->group(function () {
    Route::post('/admin/create_vacation', 'create')->name('create_vacation');
    Route::get('/admin/vacations', 'read')->name('vacationsManagement');
    Route::get('/admin/edit_vacation/{id}', 'edit')->name('edit_vacation');
    Route::post('/admin/update_vacation', 'update')->name('update_vacation');
    Route::get('/admin/delete_vacation/{id}', 'delete')->name('delete_vacation');
});

// Contracts
Route::middleware(['auth:sanctum', 'role:admin,manager'])->controller(Contracts::class)->group(function () {
    Route::post('/admin/create_contract', 'create')->name('create_contract');
    Route::get('/admin/contracts', 'read')->name('contractsManagement');
    Route::get('/admin/edit_contract/{id}', 'edit')->name('edit_contract');
    Route::post('/admin/update_contract', 'update')->name('update_contract');
    Route::get('/admin/delete_contract/{id}', 'delete')->name('delete_contract');
});

// Salaries
Route::middleware(['auth:sanctum', 'role:admin,manager'])->controller(Salaries::class)->group(function () {
    Route::post('/admin/create_salary', 'create')->name('create_salary');
    Route::get('/admin/salaries', 'read')->name('salariesManagement');
    Route::get('/admin/edit_salary/{id}', 'edit')->name('edit_salary');
    Route::post('/admin/update_salary', 'update')->name('update_salary');
    Route::get('/admin/delete_salary/{id}', 'delete')->name('delete_salary');
});

// Reports
Route::middleware(['auth:sanctum', 'role:admin'])->controller(Reports::class)->group(function () {
    Route::get('/admin/products_stock', 'productsStock')->name('productsStock');
});

<<<<<<< Updated upstream
=======
// Production Report
Route::middleware(['auth:sanctum', 'role:admin'])->controller(ProductionReport::class)->group(function () {
    Route::get('/admin/reports/production/summary',            'summary')->name('production_report_summary');
    Route::get('/admin/reports/production/trends',             'trends')->name('production_report_trends');
    Route::get('/admin/reports/production/machines',           'machines')->name('production_report_machines');
    Route::get('/admin/reports/production/top-products',       'topProducts')->name('production_report_top_products');
    Route::get('/admin/reports/production/status-distribution','statusDistribution')->name('production_report_status');
});

// Sales Report
Route::middleware(['auth:sanctum', 'role:admin'])->controller(SalesReport::class)->group(function () {
    Route::get('/admin/reports/sales/summary',        'summary')->name('sales_report_summary');
    Route::get('/admin/reports/sales/trends',         'trends')->name('sales_report_trends');
    Route::get('/admin/reports/sales/top-products',   'topProducts')->name('sales_report_top_products');
    Route::get('/admin/reports/sales/top-clients',    'topClients')->name('sales_report_top_clients');
    Route::get('/admin/reports/sales/orders-overview','ordersOverview')->name('sales_report_orders_overview');
});

>>>>>>> Stashed changes
// OPEN AI
Route::post('/ai/chat', [App\Http\Controllers\AiController::class, 'chat']);
Route::post('/ai/chat-data', [App\Http\Controllers\AiController::class, 'chatWithData']);
Route::post('/ai/analyze-text', [App\Http\Controllers\AiController::class, 'analyzeText']);
Route::post('/ai/alerts', [App\Http\Controllers\AiController::class, 'alerts']);
