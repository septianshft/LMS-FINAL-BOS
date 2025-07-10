<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Http\Controllers\{
    FrontController,
    ProfileController,
    DashboardController,
    CategoryController,
    CourseController,
    CourseVideoController,
    CourseMaterialController,
    CourseModuleController,
    ModuleVideoController,
    ModuleMaterialController,
    ModuleTaskController,
    TaskSubmissionController,
    TaskSubmissionManagementController,
    SubscribeTransactionController,
    TrainerController,
    FinalQuizController,
    CourseMeetingController,
    QuizAttemptController, // Pastikan ini sudah ada
    CertificateController,
    TalentAdminController,
    TalentController,
    RecruiterController,
    TalentDiscoveryController,
    ProjectController,
    ProjectAssignmentController,
    ProjectAdminController,
};
// ====================
// FRONTEND ROUTES
// ====================
Route::get('/', [FrontController::class, 'index'])->name('front.index');
Route::get('/courses', [CourseController::class, 'explore'])->name('courses.index');
Route::middleware('auth')->get('/my-courses', [CourseController::class, 'myCourses'])->name('courses.my');
Route::get('/details/{course:slug}', [FrontController::class, 'details'])->name('front.details');
Route::get('/category/{category:slug}', [FrontController::class, 'category'])->name('front.category');

Route::middleware(['auth', 'role:trainee'])->group(function () {
    Route::get('/cart', [\App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/{course:slug}', [\App\Http\Controllers\CartController::class, 'store'])->name('cart.store');
    Route::delete('/cart/{cartItem}', [\App\Http\Controllers\CartController::class, 'destroy'])->name('cart.destroy');
    Route::post('/courses/{course:slug}/join', [CourseController::class, 'join'])->name('courses.join');
});

Route::middleware(['auth', 'role:trainee'])->group(function () {
    Route::get('/checkout/{course:slug}', [FrontController::class, 'checkout'])->name('front.checkout');
    Route::post('/checkout/{course:slug}/store', [FrontController::class, 'checkout_store'])->name('front.checkout.store');

    // Route untuk menampilkan halaman kuis
    Route::get('/learning/course/{course}/quiz', [QuizAttemptController::class, 'show'])->name('front.quiz');

    // Route untuk submit kuis
    Route::post('/learning/quiz/{quiz}/submit', [QuizAttemptController::class, 'submit'])->name('learning.quiz.submit');
    Route::get('/certificate/{certificate}', [CertificateController::class, 'download'])->name('certificate.download');
});

Route::get('/pricing/{course:slug}', [FrontController::class, 'pricing'])
    ->where('course', '^(?!checkout$)[a-z0-9\-]+$')
    ->name('front.pricing');

// ====================
// AUTH ROUTES
// ====================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/talent', [ProfileController::class, 'updateTalent'])->name('profile.update-talent');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/learning/{course}/{courseVideoId}', [FrontController::class, 'learning'])
        ->name('front.learning')
        ->middleware('role:trainee|trainer|admin');

    Route::post('/learning/{course}/{item}/complete', [FrontController::class, 'markItemComplete'])
        ->name('learning.item.complete')
        ->middleware('role:trainee');

    Route::get('/task/{task}/submit', [TaskSubmissionController::class, 'create'])
        ->name('task.submit.create')
        ->middleware('role:trainee');
    Route::post('/task/{task}/submit', [TaskSubmissionController::class, 'store'])
        ->name('task.submit.store')
        ->middleware('role:trainee');

    Route::get('/materials/{material}/download', [CourseMaterialController::class, 'download'])
        ->name('materials.download');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dismiss-suggestion', [DashboardController::class, 'dismissSuggestion'])->name('dismiss.suggestion');

    // ====================
    // ADMIN ROUTES
    // ====================
    Route::prefix('admin')->name('admin.')->group(function () {

        // Admin Only
        Route::middleware('role:admin')->group(function () {
            Route::resource('categories', CategoryController::class);
            Route::resource('trainers', TrainerController::class);
            Route::resource('subscribe_transactions', SubscribeTransactionController::class);
        });

        // Admin + Trainer
        Route::middleware('role:admin|trainer')->group(function () {
            Route::resource('courses', CourseController::class);
            Route::resource('course_videos', CourseVideoController::class);
            Route::resource('course_modules', CourseModuleController::class);
            Route::resource('courses.meetings', CourseMeetingController::class)->except('show');

            Route::get('/add/video/{course:id}', [CourseVideoController::class, 'create'])->name('course.add_video');
            Route::post('/add/video/save/{course:id}', [CourseVideoController::class, 'store'])->name('course.add_video.save');

            Route::post('course-materials', [CourseMaterialController::class, 'store'])->name('course_materials.store');

            Route::prefix('curriculum')->name('curriculum.')->group(function () {
                Route::get('course/{course}', [CourseModuleController::class, 'index'])->name('index');
                Route::post('course/{course}', [CourseModuleController::class, 'store'])->name('store');
                Route::put('module/{courseModule}', [CourseModuleController::class, 'update'])->name('update');
                Route::delete('module/{courseModule}', [CourseModuleController::class, 'destroy'])->name('destroy');
                Route::post('course/{course}/modules/reorder', [CourseModuleController::class, 'reorder'])->name('modules.reorder');

                Route::get('module/{courseModule}/videos/create', [ModuleVideoController::class, 'create'])->name('videos.create');
                Route::get('module/{courseModule}/materials/create', [ModuleMaterialController::class, 'create'])->name('materials.create');
                Route::get('module/{courseModule}/tasks/create', [ModuleTaskController::class, 'create'])->name('tasks.create');

                Route::post('module/{courseModule}/videos', [ModuleVideoController::class, 'store'])->name('videos.store');
                Route::post('module/{courseModule}/materials', [ModuleMaterialController::class, 'store'])->name('materials.store');
                Route::post('module/{courseModule}/tasks', [ModuleTaskController::class, 'store'])->name('tasks.store');
                Route::delete('videos/{courseVideo}', [ModuleVideoController::class, 'destroy'])->name('videos.destroy');
                Route::delete('materials/{courseMaterial}', [ModuleMaterialController::class, 'destroy'])->name('materials.destroy');
                Route::delete('tasks/{moduleTask}', [ModuleTaskController::class, 'destroy'])->name('tasks.destroy');
            });

          // Final Quiz Management Routes
          Route::get('course-quiz', [FinalQuizController::class, 'index'])->name('course_quiz.index');
          Route::get('courses/{course}/quiz/create', [FinalQuizController::class, 'create'])->name('course_quiz.create');
          Route::post('courses/{course}/quiz', [FinalQuizController::class, 'store'])->name('course_quiz.store');
          Route::get('courses/{course}/quiz/edit', [FinalQuizController::class, 'edit'])->name('course_quiz.edit');
          Route::put('courses/{course}/quiz', [FinalQuizController::class, 'update'])->name('course_quiz.update');

          Route::get('courses/{course}/task-submissions', [TaskSubmissionManagementController::class, 'index'])->name('task_submissions.index');
          Route::patch('task-submissions/{submission}', [TaskSubmissionManagementController::class, 'update'])->name('task_submissions.update');
          Route::get('task-submissions/{submission}/download', [TaskSubmissionManagementController::class, 'download'])->name('task_submissions.download');

        });
    });

    // ====================
    // LEARNING QUIZ (Trainee)
    // ====================
    // Route::middleware('role:trainee')->group(function () { // Grup ini sudah ada di atas, jadi route submit sudah tercakup

        // Contoh route untuk halaman kuis (jika ada yang generik, mungkin tidak diperlukan jika sudah ada yang spesifik)
        // Route::get('/quiz', function () {
        //     return view('front.quiz'); // Pastikan nama view benar
        // })->name('front.quiz');
        // Route::post('/learning/quiz/{quiz}/submit', [QuizAttemptController::class, 'submit'])->name('learning.quiz.submit'); // Sudah didefinisikan di atas
    // });

    // ====================
    // TALENT SCOUTING ROUTES
    // ====================

    // Shared routes accessible by admin and talent_admin for project management
    Route::middleware(['auth', 'role:admin|talent_admin'])->group(function () {
        // Details endpoints for modal views (accessible from project management)
        Route::get('talent-admin/talents/{talent}/details', [TalentAdminController::class, 'getTalentDetails'])->name('talent_admin.talent_details');
        Route::get('talent-admin/users/{user}/talent-details', [TalentAdminController::class, 'getTalentDetailsByUserId'])->name('talent_admin.talent_details_by_user');
        Route::get('talent-admin/recruiters/{recruiter}/details', [TalentAdminController::class, 'getRecruiterDetails'])->name('talent_admin.recruiter_details');
    });

    // Talent Admin Routes
    Route::middleware('role:talent_admin')->group(function () {
        Route::patch('talent-admin/talent/{talent}/set-redflag', [TalentAdminController::class, 'setTalentRedflag'])->name('talent_admin.set_talent_redflag');
        Route::patch('talent-admin/talent/{talent}/unset-redflag', [TalentAdminController::class, 'unsetTalentRedflag'])->name('talent_admin.unset_talent_redflag');

        // New project-based redflag routes
        Route::get('talent-admin/talents/{talent}/completed-projects', [TalentAdminController::class, 'getTalentCompletedProjects'])->name('talent_admin.talent_completed_projects');
        Route::post('talent-admin/projects/redflag', [TalentAdminController::class, 'flagProject'])->name('talent_admin.flag_project');
        Route::get('talent-admin/talents/{talent}/redflag-history', [TalentAdminController::class, 'getTalentRedflagHistory'])->name('talent_admin.talent_redflag_history');
        Route::get('talent-admin/dashboard', [TalentAdminController::class, 'dashboard'])->name('talent_admin.dashboard');
        Route::get('talent-admin/manage-talents', [TalentAdminController::class, 'manageTalents'])->name('talent_admin.manage_talents');
        Route::get('talent-admin/manage-recruiters', [TalentAdminController::class, 'manageRecruiters'])->name('talent_admin.manage_recruiters');
        Route::get('talent-admin/manage-requests', [TalentAdminController::class, 'manageRequests'])->name('talent_admin.manage_requests');
        Route::get('talent-admin/request/{talentRequest}', [TalentAdminController::class, 'showRequest'])->name('talent_admin.show_request');
        Route::patch('talent-admin/request/{talentRequest}/status', [TalentAdminController::class, 'updateRequestStatus'])->name('talent_admin.update_request_status');
        Route::post('talent-admin/request/{talentRequest}/admin-accept', [TalentAdminController::class, 'adminAcceptRequest'])->name('talent_admin.admin_accept_request');
        Route::get('talent-admin/request/{talentRequest}/can-arrange-meeting', [TalentAdminController::class, 'canArrangeMeeting'])->name('talent_admin.can_arrange_meeting');
        Route::patch('talent-admin/talent/{talent}/toggle-status', [TalentAdminController::class, 'toggleTalentStatus'])->name('talent_admin.toggle_talent_status');
        Route::patch('talent-admin/recruiter/{recruiter}/toggle-status', [TalentAdminController::class, 'toggleRecruiterStatus'])->name('talent_admin.toggle_recruiter_status');

        // Export Routes for Talent Admin
        Route::get('talent-admin/export/requests-summary', [TalentAdminController::class, 'exportRequestsSummary'])->name('talent_admin.export_requests_summary');
        Route::get('talent-admin/export/requests-detailed', [TalentAdminController::class, 'exportRequestsDetailed'])->name('talent_admin.export_requests_detailed');

        // New Analytics Routes for Phase 1
        Route::get('talent-admin/analytics', [TalentAdminController::class, 'analytics'])->name('talent_admin.analytics');
        Route::get('talent-admin/api/conversion-analytics', [TalentAdminController::class, 'getConversionAnalytics'])->name('talent_admin.api.conversion_analytics');
        Route::get('talent-admin/api/conversion-candidates', [TalentAdminController::class, 'getConversionCandidates'])->name('talent_admin.api.conversion_candidates');
        Route::get('talent-admin/api/skill-analytics', [TalentAdminController::class, 'getSkillAnalytics'])->name('talent_admin.api.skill_analytics');
        Route::get('talent-admin/api/market-demand', [TalentAdminController::class, 'getMarketDemand'])->name('talent_admin.api.market_demand');

        // Cache Management Routes
        Route::post('talent-admin/clear-dashboard-cache', [TalentAdminController::class, 'clearDashboardCache'])->name('talent_admin.clear_dashboard_cache');
        Route::get('talent-admin/dashboard-data', [TalentAdminController::class, 'getDashboardData'])->name('talent_admin.dashboard_data');

        // Project Management Routes for Talent Admins
        Route::get('talent-admin/manage-projects', [TalentAdminController::class, 'manageProjects'])->name('talent_admin.manage_projects');

        // Talent Admin Management Routes
        Route::get('talent-admin/manage-admins', [TalentAdminController::class, 'manageTalentAdmins'])->name('talent_admin.manage_talent_admins');
        Route::get('talent-admin/admin/create', [TalentAdminController::class, 'createTalentAdmin'])->name('talent_admin.create_talent_admin');
        Route::post('talent-admin/admin', [TalentAdminController::class, 'storeTalentAdmin'])->name('talent_admin.store_talent_admin');
        Route::get('talent-admin/admin/{user}', [TalentAdminController::class, 'showTalentAdmin'])->name('talent_admin.show_talent_admin');
        Route::get('talent-admin/admin/{user}/edit', [TalentAdminController::class, 'editTalentAdmin'])->name('talent_admin.edit_talent_admin');
        Route::put('talent-admin/admin/{user}', [TalentAdminController::class, 'updateTalentAdmin'])->name('talent_admin.update_talent_admin');
        Route::delete('talent-admin/admin/{user}', [TalentAdminController::class, 'destroyTalentAdmin'])->name('talent_admin.destroy_talent_admin');
        Route::get('talent-admin/admin/{user}/details', [TalentAdminController::class, 'getTalentAdminDetails'])->name('talent_admin.talent_admin_details');

        // Recruiter Management Routes
        Route::post('talent-admin/recruiter', [TalentAdminController::class, 'storeRecruiter'])->name('talent_admin.store_recruiter');
        Route::get('talent-admin/recruiter/{recruiter}/edit', [TalentAdminController::class, 'editRecruiter'])->name('talent_admin.edit_recruiter');
        Route::put('talent-admin/recruiter/{recruiter}', [TalentAdminController::class, 'updateRecruiter'])->name('talent_admin.update_recruiter');
        Route::delete('talent-admin/recruiter/{recruiter}', [TalentAdminController::class, 'destroyRecruiter'])->name('talent_admin.destroy_recruiter');

        // Conversion suggestion endpoint
        Route::post('talent-admin/suggest-conversion/{user}', [TalentAdminController::class, 'suggestConversion'])->name('talent_admin.suggest_conversion');
    });

    // Talent Routes
    Route::middleware('role:talent')->group(function () {
        Route::get('talent/dashboard', [TalentController::class, 'dashboard'])->name('talent.dashboard');

        // Talent Request Management
        Route::post('talent/request/{talentRequest}/accept', [TalentController::class, 'acceptRequest'])->name('talent.accept_request');
        Route::post('talent/request/{talentRequest}/reject', [TalentController::class, 'rejectRequest'])->name('talent.reject_request');
        Route::get('talent/my-requests', [TalentController::class, 'myRequests'])->name('talent.my_requests');
        Route::get('talent/api/my-requests', [TalentController::class, 'getMyRequests'])->name('talent.api.my_requests');
        Route::get('talent/api/request/{talentRequest}', [TalentController::class, 'getRequestDetails'])->name('talent.api.request_details');
        Route::get('talent/export/my-requests-pdf', [TalentController::class, 'exportMyRequestsPDF'])->name('talent.export.my_requests_pdf');
    });

    // Recruiter Routes
    Route::middleware('role:recruiter')->group(function () {
        Route::get('recruiter/dashboard', [RecruiterController::class, 'dashboard'])->name('recruiter.dashboard');
        Route::post('recruiter/talent-request', [RecruiterController::class, 'submitTalentRequest'])->name('recruiter.submit_talent_request');
        Route::get('recruiter/my-requests', [RecruiterController::class, 'myRequests'])->name('recruiter.my_requests');
        Route::get('recruiter/request-details/{request}', [RecruiterController::class, 'requestDetails'])->name('recruiter.request_details');
        Route::get('recruiter/scouting-report/{talent}', [RecruiterController::class, 'getScoutingReport'])->name('recruiter.scouting_report');
        Route::get('recruiter/talent/{talent}/redflag-history', [RecruiterController::class, 'getTalentRedflagHistory'])->name('recruiter.talent_redflag_history');

        // Talent details routes for recruiters
    Route::get('recruiter/talents/{talent}/details', [RecruiterController::class, 'getTalentDetails'])->name('recruiter.talent_details');
    Route::get('recruiter/users/{user}/talent-details', [RecruiterController::class, 'getTalentDetailsByUserId'])->name('recruiter.talent_details_by_user');
    
    // AJAX search route for all talents
    Route::get('recruiter/search-talents', [RecruiterController::class, 'searchTalents'])->name('recruiter.search_talents');

        // Enhanced Analytics & Recommendations API
        Route::prefix('recruiter/api')->name('recruiter.api.')->group(function () {
            Route::get('/analytics', [RecruiterController::class, 'getAnalyticsData'])->name('analytics');
            Route::get('/recommendations', [RecruiterController::class, 'getTalentRecommendations'])->name('recommendations');
        });

        // PDF Export Routes
        Route::get('recruiter/exports/request-history', [RecruiterController::class, 'exportRequestHistory'])->name('recruiter.export_request_history');
        Route::get('recruiter/exports/onboarded-talents', [RecruiterController::class, 'exportOnboardedTalents'])->name('recruiter.export_onboarded_talents');

        // Talent Discovery Routes for Recruiters
        Route::prefix('recruiter/discovery')->name('recruiter.discovery.')->group(function () {
            Route::get('/', [TalentDiscoveryController::class, 'index'])->name('index');
            Route::post('/search', [TalentDiscoveryController::class, 'search'])->name('search');
            Route::post('/match', [TalentDiscoveryController::class, 'match'])->name('match');
            Route::get('/recommendations', [TalentDiscoveryController::class, 'recommendations'])->name('recommendations');
            Route::get('/talent/{talent}', [TalentDiscoveryController::class, 'show'])->name('show');
            Route::post('/advanced-search', [TalentDiscoveryController::class, 'advancedSearch'])->name('advanced');
            Route::get('/analytics', [TalentDiscoveryController::class, 'analytics'])->name('analytics');
        });
    });

    // Talent Admin Routes
    Route::middleware('role:talent_admin')->group(function () {
        // Talent Discovery Routes for Admins (full access)
        Route::prefix('admin/discovery')->name('admin.discovery.')->group(function () {
            Route::get('/', [TalentDiscoveryController::class, 'index'])->name('index');
            Route::post('/search', [TalentDiscoveryController::class, 'search'])->name('search');
            Route::post('/match', [TalentDiscoveryController::class, 'match'])->name('match');
            Route::get('/recommendations', [TalentDiscoveryController::class, 'recommendations'])->name('recommendations');
            Route::get('/talent/{talent}', [TalentDiscoveryController::class, 'show'])->name('show');
            Route::post('/advanced-search', [TalentDiscoveryController::class, 'advancedSearch'])->name('advanced');
            Route::get('/analytics', [TalentDiscoveryController::class, 'analytics'])->name('analytics');
        });
    });
});

// Data Integrity Check Routes (for system administrators)
Route::middleware(['auth', 'role:admin|talent_admin'])->group(function () {
    Route::get('/admin/data-integrity', [App\Http\Controllers\DataIntegrityController::class, 'dashboard'])->name('admin.data_integrity.dashboard');
    Route::post('/admin/data-integrity/run-checks', [App\Http\Controllers\DataIntegrityController::class, 'runChecks'])->name('admin.data_integrity.run_checks');
    Route::get('/admin/data-integrity/download-report', [App\Http\Controllers\DataIntegrityController::class, 'downloadReport'])->name('admin.data_integrity.download_report');
});

// Test routes for debugging
Route::get('/test-dashboard-data', function () {
    // Test data availability and integrity
    $results = [];

    try {
        // Test 1: Check if talents exist
        $totalTalents = \App\Models\Talent::count();
        $activeTalents = \App\Models\Talent::where('is_active', true)->count();
        $results['talents'] = [
            'total' => $totalTalents,
            'active' => $activeTalents,
            'status' => $totalTalents > 0 ? 'PASS' : 'FAIL'
        ];

        // Test 2: Check if users with talent role exist
        $talentUsers = \App\Models\User::whereHas('roles', function($query) {
            $query->where('name', 'talent');
        })->count();

        $scoutingReady = \App\Models\User::where('available_for_scouting', true)->count();
        $results['talent_users'] = [
            'total' => $talentUsers,
            'scouting_ready' => $scoutingReady,
            'status' => $scoutingReady > 0 ? 'PASS' : 'WARNING'
        ];

        // Test 3: Check if recruiters exist
        $totalRecruiters = \App\Models\Recruiter::count();
        $activeRecruiters = \App\Models\Recruiter::where('is_active', true)->count();
        $results['recruiters'] = [
            'total' => $totalRecruiters,
            'active' => $activeRecruiters,
            'status' => $totalRecruiters > 0 ? 'PASS' : 'FAIL'
        ];

        // Test 4: Check talent requests
        $totalRequests = \App\Models\TalentRequest::count();
        $pendingRequests = \App\Models\TalentRequest::where('status', 'pending')->count();
        $results['requests'] = [
            'total' => $totalRequests,
            'pending' => $pendingRequests,
            'status' => 'PASS'
        ];

        // Test 5: Check specific data for dashboard
        $talentsWithMetrics = \App\Models\Talent::with(['user'])
            ->where('is_active', true)
            ->whereHas('user', function($query) {
                $query->where('available_for_scouting', true);
            })
            ->limit(5)
            ->get();

        $results['dashboard_data'] = [
            'talents_for_display' => $talentsWithMetrics->count(),
            'sample_talent' => $talentsWithMetrics->first() ? [
                'id' => $talentsWithMetrics->first()->id,
                'name' => $talentsWithMetrics->first()->user->name,
                'email' => $talentsWithMetrics->first()->user->email,
                'scouting_ready' => $talentsWithMetrics->first()->user->available_for_scouting
            ] : null,
            'status' => $talentsWithMetrics->count() > 0 ? 'PASS' : 'WARNING'
        ];

        // Test 6: Check for potential issues
        $issues = [];

        if ($totalTalents === 0) {
            $issues[] = 'No talents in database - run seeders';
        }

        if ($scoutingReady === 0) {
            $issues[] = 'No talents available for scouting - check available_for_scouting field';
        }

        if ($totalRecruiters === 0) {
            $issues[] = 'No recruiters in database - run seeders';
        }

        $results['issues'] = $issues;
        $results['overall_status'] = empty($issues) ? 'HEALTHY' : 'NEEDS_ATTENTION';

    } catch (\Exception $e) {
        $results['error'] = $e->getMessage();
        $results['overall_status'] = 'ERROR';
    }

    return response()->json($results, 200, [], JSON_PRETTY_PRINT);
});

// Test routes (remove in production)
Route::get('/test-talent-details/{talent}', function(App\Models\Talent $talent) {
    $controller = new App\Http\Controllers\TalentAdminController(
        app(App\Services\AdvancedSkillAnalyticsService::class),
        app(App\Services\SmartConversionTrackingService::class),
        app(App\Services\TalentRequestNotificationService::class)
    );
    return $controller->getTalentDetails($talent);
})->middleware('auth');

Route::get('/test-user-talent-details/{user}', function(App\Models\User $user) {
    $controller = new App\Http\Controllers\TalentAdminController(
        app(App\Services\AdvancedSkillAnalyticsService::class),
        app(App\Services\SmartConversionTrackingService::class),
        app(App\Services\TalentRequestNotificationService::class)
    );
    return $controller->getProjectTalentDetailsByUserId($user);
})->middleware('auth');

// ===================================================
// PROJECT-CENTRIC SYSTEM ROUTES
// ===================================================

Route::middleware(['auth', 'verified'])->group(function () {

    // Project routes for recruiters
    Route::resource('projects', ProjectController::class);

    // Project export routes
    Route::get('projects/export/project-history', [ProjectController::class, 'exportProjectHistory'])
        ->name('projects.export_project_history');
    Route::get('projects/export/completed-projects', [ProjectController::class, 'exportCompletedProjects'])
        ->name('projects.export_completed_projects');
    Route::get('projects/export/talent-participation', [ProjectController::class, 'exportTalentParticipation'])
        ->name('projects.export_talent_participation');

    Route::post('projects/{project}/request-extension', [ProjectController::class, 'requestExtension'])
        ->name('projects.request-extension');
    Route::post('projects/{project}/request-closure', [ProjectController::class, 'requestClosure'])
        ->name('projects.request-closure');
    Route::post('projects/{project}/approve-closure', [ProjectController::class, 'approveClosure'])
        ->name('projects.approve-closure')
        ->middleware('role:talent_admin');
    Route::post('projects/{project}/reject-closure', [ProjectController::class, 'rejectClosure'])
        ->name('projects.reject-closure')
        ->middleware('role:talent_admin');

    // Talent details routes accessible from projects (for project members)
    Route::get('projects/talent/{talent}/details', [TalentAdminController::class, 'getProjectTalentDetails'])
        ->name('projects.talent_details');
    Route::get('projects/user/{user}/talent-details', [TalentAdminController::class, 'getProjectTalentDetailsByUserId'])
        ->name('projects.talent_details_by_user');

    // Project assignment routes
    Route::post('projects/{project}/assignments', [ProjectAssignmentController::class, 'store'])
        ->name('project-assignments.store');
    Route::patch('assignments/{assignment}/accept', [ProjectAssignmentController::class, 'accept'])
        ->name('project-assignments.accept');
    Route::patch('assignments/{assignment}/decline', [ProjectAssignmentController::class, 'decline'])
        ->name('project-assignments.decline');
    Route::patch('assignments/{assignment}', [ProjectAssignmentController::class, 'update'])
        ->name('project-assignments.update');
    Route::delete('assignments/{assignment}', [ProjectAssignmentController::class, 'destroy'])
        ->name('project-assignments.destroy');

    // Admin Project Management Routes
    Route::middleware(['auth', 'role:admin|talent_admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('projects', [ProjectAdminController::class, 'index'])->name('projects.index');
        Route::get('projects/all', [ProjectAdminController::class, 'allProjects'])->name('projects.all');
        Route::get('projects/analytics', [ProjectAdminController::class, 'analytics'])->name('projects.analytics');
        Route::get('projects/{project}', [ProjectAdminController::class, 'show'])->name('projects.show');

        // Project approval routes
        Route::patch('projects/{project}/approve', [ProjectAdminController::class, 'approve'])->name('projects.approve');
        Route::patch('projects/{project}/reject', [ProjectAdminController::class, 'reject'])->name('projects.reject');

        // Redirect GET requests to approve/reject back to project show page
        Route::get('projects/{project}/approve', function(Project $project) {
            return redirect()->route('admin.projects.show', $project);
        });
        Route::get('projects/{project}/reject', function(Project $project) {
            return redirect()->route('admin.projects.show', $project);
        });

        // Extension management routes
        Route::get('projects/{project}/extensions', [ProjectAdminController::class, 'extensionPartial'])->name('projects.extensions');
        Route::patch('extensions/{extension}/approve', [ProjectAdminController::class, 'approveExtension'])->name('extensions.approve');
        Route::patch('extensions/{extension}/reject', [ProjectAdminController::class, 'rejectExtension'])->name('extensions.reject');
        Route::put('extensions/{extension}/review', [ProjectAdminController::class, 'reviewExtension'])->name('extensions.review');

        // Closure request management routes
        Route::get('projects/{project}/closure-details', [ProjectAdminController::class, 'closureDetails'])->name('projects.closure-details');
    });

    // Talent Assignment Routes
    Route::middleware(['auth', 'role:talent'])->prefix('talent')->name('talent.')->group(function () {
        Route::get('assignments', [ProjectAssignmentController::class, 'talentIndex'])->name('assignments.index');
        Route::get('assignments/export-pdf', [ProjectAssignmentController::class, 'exportAssignmentsPDF'])->name('assignments.export-pdf');
        Route::get('assignments/{assignment}', [ProjectAssignmentController::class, 'talentShow'])->name('assignments.show');
        Route::put('assignments/{assignment}/respond', [ProjectAssignmentController::class, 'respond'])->name('assignments.respond');
        Route::put('assignments/{assignment}/progress', [ProjectAssignmentController::class, 'updateProgress'])->name('assignments.progress');
        Route::post('assignments/{assignment}/request-extension', [ProjectAssignmentController::class, 'requestExtension'])->name('assignments.request-extension');
    });
});


// Include custom routes for Talent Admin PDF export
require __DIR__.'/custom_talent_admin_export.php';
require __DIR__.'/auth.php';
