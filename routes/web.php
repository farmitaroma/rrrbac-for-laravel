<?php

use Farmit\RrrbacForLaravel\Controllers\PermissionsController;
use Farmit\RrrbacForLaravel\Controllers\RolesController;
use Farmit\RrrbacForLaravel\Controllers\UserController;
use Farmit\RrrbacForLaravel\Livewire\MenuItemsTable;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::group(['middleware' => ['auth', 'web'], 'prefix' => 'rbac'], function () {
    Route::resource('roles', RolesController::class)->only(['index', 'edit']);
    Route::resource('permissions', PermissionsController::class)->only(['index']);
    Route::resource('users', UserController::class)->only(['index', 'edit']);
    Route::get('menu-items', MenuItemsTable::class)->name('menu-items');
});
