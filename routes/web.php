<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\FormResponseController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => auth()->check() ? redirect()->route('forms.index') : view('landing'))->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'create'])->name('register');
    Route::post('/register', [AuthController::class, 'store'])->middleware('throttle:10,1');
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->middleware('throttle:10,1');
});

Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => redirect()->route('forms.index'))->name('dashboard');
    Route::get('/forms', [FormController::class, 'index'])->name('forms.index');
    Route::get('/forms/create', [FormController::class, 'create'])->name('forms.create');
    Route::post('/forms', [FormController::class, 'store'])->name('forms.store');
    Route::get('/forms/{form}/edit', [FormController::class, 'edit'])->name('forms.edit');
    Route::put('/forms/{form}', [FormController::class, 'update'])->name('forms.update');
    Route::post('/forms/{form}/publish', [FormController::class, 'togglePublished'])->name('forms.publish');
    Route::delete('/forms/{form}', [FormController::class, 'destroy'])->name('forms.destroy');
    Route::get('/forms/{form}/responses/export', [FormResponseController::class, 'export'])->name('forms.responses.export');
    Route::get('/forms/{form}/responses', [FormResponseController::class, 'index'])->name('forms.responses.index');
});

Route::get('/s/{form:slug}', [FormResponseController::class, 'show'])->name('forms.fill');
Route::post('/s/{form:slug}/responses', [FormResponseController::class, 'store'])
    ->middleware('throttle:30,1')->name('forms.responses.store');
Route::get('/s/{form:slug}/thanks', [FormResponseController::class, 'thanks'])->name('forms.thanks');
