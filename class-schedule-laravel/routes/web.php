<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ScheduleController;
use Illuminate\Support\Facades\Route;

// Home page - Schedule view
Route::get('/', [ScheduleController::class, 'index'])->name('home');

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Schedule routes (for all authenticated users)
    Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule.index');
    Route::get('/schedule/{classroom}', [ScheduleController::class, 'show'])->name('schedule.show');
    
    // Director routes
    Route::middleware(['role:director'])->group(function () {
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/', [ScheduleController::class, 'admin'])->name('dashboard');
            
            // Classrooms management
            Route::get('/classrooms', [ScheduleController::class, 'classrooms'])->name('classrooms');
            Route::post('/classrooms', [ScheduleController::class, 'storeClassroom'])->name('classrooms.store');
            Route::put('/classrooms/{classroom}', [ScheduleController::class, 'updateClassroom'])->name('classrooms.update');
            Route::delete('/classrooms/{classroom}', [ScheduleController::class, 'destroyClassroom'])->name('classrooms.destroy');
            
            // Subjects management
            Route::get('/subjects', [ScheduleController::class, 'subjects'])->name('subjects');
            Route::post('/subjects', [ScheduleController::class, 'storeSubject'])->name('subjects.store');
            Route::put('/subjects/{subject}', [ScheduleController::class, 'updateSubject'])->name('subjects.update');
            Route::delete('/subjects/{subject}', [ScheduleController::class, 'destroySubject'])->name('subjects.destroy');
            
            // Schedule management
            Route::get('/schedules', [ScheduleController::class, 'schedules'])->name('schedules');
            Route::post('/schedules', [ScheduleController::class, 'storeSchedule'])->name('schedules.store');
            Route::put('/schedules/{schedule}', [ScheduleController::class, 'updateSchedule'])->name('schedules.update');
            Route::delete('/schedules/{schedule}', [ScheduleController::class, 'destroySchedule'])->name('schedules.destroy');
            
            // Substitutions
            Route::get('/substitutions', [ScheduleController::class, 'substitutions'])->name('substitutions');
            Route::post('/substitutions', [ScheduleController::class, 'storeSubstitution'])->name('substitutions.store');
            Route::put('/substitutions/{substitution}', [ScheduleController::class, 'updateSubstitution'])->name('substitutions.update');
            Route::delete('/substitutions/{substitution}', [ScheduleController::class, 'destroySubstitution'])->name('substitutions.destroy');
        });
    });
    
    // Deputy (Зауч) routes
    Route::middleware(['role:deputy'])->group(function () {
        Route::get('/deputy', [ScheduleController::class, 'deputyDashboard'])->name('deputy.dashboard');
        Route::post('/substitutions/{substitution}/approve', [ScheduleController::class, 'approveSubstitution'])->name('substitutions.approve');
        Route::post('/substitutions/{substitution}/reject', [ScheduleController::class, 'rejectSubstitution'])->name('substitutions.reject');
    });
});
