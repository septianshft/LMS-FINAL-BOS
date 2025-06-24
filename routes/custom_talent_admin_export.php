<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TalentAdminController;

// ...existing routes...

// PDF Export Route for Talent Admin Analytics/Documentation
Route::middleware(['auth', 'role:talent_admin'])->group(function () {
    Route::get('talent-admin/exports/analytics', [TalentAdminController::class, 'exportAnalyticsPdf'])->name('talent_admin.export_analytics_pdf');
});
