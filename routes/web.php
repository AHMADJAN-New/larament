<?php

declare(strict_types=1);

use App\Http\Controllers\MeetingPdfController;
use App\Http\Controllers\MeetingShareController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::middleware('auth')->group(function (): void {
    Route::get('/meetings/{meeting}/pdf/decisions', [MeetingPdfController::class, 'decisions'])
        ->name('meetings.pdf.decisions');
    Route::get('/meetings/{meeting}/pdf/followup', [MeetingPdfController::class, 'followup'])
        ->name('meetings.pdf.followup');
    Route::get('/meetings/{meeting}/pdf/complete', [MeetingPdfController::class, 'complete'])
        ->name('meetings.pdf.complete');
    Route::get('/meetings/{meeting}/share/create', [MeetingShareController::class, 'create'])
        ->name('meetings.share.create');
});

Route::get('/share/meeting/{token}', [MeetingShareController::class, 'show'])
    ->name('meetings.share.show');
Route::get('/share/meeting/{token}/pdf/decisions', [MeetingShareController::class, 'decisions'])
    ->name('meetings.share.pdf.decisions');
Route::get('/share/meeting/{token}/pdf/followup', [MeetingShareController::class, 'followup'])
    ->name('meetings.share.pdf.followup');
Route::get('/share/meeting/{token}/pdf/complete', [MeetingShareController::class, 'complete'])
    ->name('meetings.share.pdf.complete');
