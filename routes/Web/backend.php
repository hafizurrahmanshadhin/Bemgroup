<?php

use App\Http\Controllers\Web\Backend\DashboardController;
use App\Http\Controllers\Web\Backend\TodoController;
use Illuminate\Support\Facades\Route;

// Route for Admin Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Route for Todo
Route::controller(TodoController::class)->group(function () {
    Route::get('/todos', 'index')->name('todos.index');
    Route::get('/todos/show/{todo}', 'show')->name('todos.show');
    Route::get('/todos/create', 'create')->name('todos.create');
    Route::post('/todos/store', 'store')->name('todos.store');
    Route::get('/todos/edit/{todo}', 'edit')->name('todos.edit');
    Route::put('/todos/update/{todo}', 'update')->name('todos.update');
    Route::get('/todos/status/{todo}', 'status')->name('todos.status');
    Route::delete('/todos/destroy/{todo}', 'destroy')->name('todos.destroy');
});
