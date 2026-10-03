<?php

use App\Http\Controllers\ActionPlanController;
use App\Http\Controllers\Admin\AssessmentController as AdminAssessmentController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\SummaryReportController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TourController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/two-factor', [ProfileController::class, 'updateTwoFactor'])->name('profile.two-factor');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/notifications/{notificationId}/read', [NotificationController::class, 'read'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.readAll');

    Route::get('/help', [HelpController::class, 'index'])->name('help.index');
    Route::post('/tour/complete', [TourController::class, 'complete'])->name('tour.complete');
});

// SME Owner routes
Route::middleware(['auth', 'verified', 'role:sme_owner'])->group(function () {
    Route::get('/assessment/start', [AssessmentController::class, 'start'])->name('assessment.start');
    Route::get('/assessment/history', [AssessmentController::class, 'history'])->name('assessment.history');
    Route::get('/assessment/{assessment}/questionnaire', [AssessmentController::class, 'questionnaire'])->name('assessment.questionnaire');
    Route::post('/assessment/{assessment}/questionnaire', [AssessmentController::class, 'submit'])->name('assessment.submit');
    Route::post('/assessment/{assessment}/answer', [AssessmentController::class, 'saveAnswer'])->name('assessment.answer');
    Route::get('/assessment/{assessment}/result', [AssessmentController::class, 'result'])->name('assessment.result');
    Route::get('/assessment/{assessment}/report', [ReportController::class, 'download'])->name('assessment.report');
    Route::post('/assessment/{assessment}/explain', [AssessmentController::class, 'explain'])
        ->middleware('throttle:10,1')
        ->name('assessment.explain');

    Route::get('/action-plan', [ActionPlanController::class, 'index'])->name('action-plan.index');
    Route::patch('/action-plan/{item}', [ActionPlanController::class, 'update'])->name('action-plan.update');
    Route::patch('/action-plan/{item}/steps', [ActionPlanController::class, 'toggleStep'])->name('action-plan.steps');
});

// Administrator routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
    Route::get('/clients/create', [ClientController::class, 'create'])->name('clients.create');
    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
    Route::get('/clients/{client}', [ClientController::class, 'show'])->name('clients.show');
    Route::get('/clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
    Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
    Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');
    Route::post('/clients/{client}/notify', [ClientController::class, 'notify'])->name('clients.notify');

    Route::get('/assessments', [AdminAssessmentController::class, 'index'])->name('assessments.index');
    Route::get('/assessments/{assessment}', [AdminAssessmentController::class, 'show'])->name('assessments.show');
    Route::patch('/assessments/{assessment}/notes', [AdminAssessmentController::class, 'saveNotes'])->name('assessments.notes');
    Route::get('/assessments/{assessment}/report', [ReportController::class, 'download'])->name('assessments.report');

    Route::get('/reports/summary', [SummaryReportController::class, 'download'])->name('reports.summary');
});

require __DIR__.'/auth.php';
