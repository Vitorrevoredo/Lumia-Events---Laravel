<?php

use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ProductController;
use App\Models\Event;

Route::get('/', [EventController::class, 'index'] );
Route::get('/events/create', [EventController::class, 'create'])->middleware('auth');
Route::post('/events', [EventController::class, 'store'] );
Route::get('/events/{id}', [EventController::class, 'show'] );
Route::get('/events/{id}/edit', [EventController::class, 'edit'] );
Route::put('/events/{id}', [EventController::class, 'update'] );
Route::delete('/events/{id}', [EventController::class, 'destroy'] );
Route::get('/contact', function () {
    return view('contact');
});

Route::get('/dashboard', [EventController::class, 'dashboard'])->middleware('auth');


