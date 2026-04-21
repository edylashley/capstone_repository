<?php

use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Adviser\VerificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SupportTicketController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::any('/', function () {
    return view('home');
})->name('home');

// Public access to browse projects (for title validation)
Route::get('/projects', [\App\Http\Controllers\ProjectController::class, 'indexPage'])->name('projects.index');

Route::get('/dashboard', function (Request $request) {
    if ($request->user()->isAdviser()) {
        return (new \App\Http\Controllers\Adviser\DashboardController())->index($request);
    }
    
    return match ($request->user()->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'student' => redirect()->route('student.home'),
        default => view('dashboard'),
    };
})->middleware(['auth', 'verified', \App\Http\Middleware\UpdateLastActivity::class])->name('dashboard');

// Role-based dashboards
Route::middleware(['auth', \App\Http\Middleware\UpdateLastActivity::class, 'role:student'])->group(function () {
    Route::get('/student/home', [\App\Http\Controllers\Student\HomeController::class, 'index'])->name('student.home');
});

Route::middleware(['auth', \App\Http\Middleware\UpdateLastActivity::class, 'role:adviser'])->group(function () {
    Route::get('/faculty/dashboard', [\App\Http\Controllers\Adviser\DashboardController::class, 'index'])->name('faculty.dashboard');
    Route::get('/faculty/review', [\App\Http\Controllers\Faculty\ReviewController::class, 'index'])->name('faculty.review');
    Route::post('/faculty/projects/{project}/approve', [\App\Http\Controllers\Faculty\ReviewController::class, 'approve'])->name('faculty.projects.approve');
    Route::post('/faculty/projects/{project}/cancel', [\App\Http\Controllers\Faculty\ReviewController::class, 'cancel'])->name('faculty.projects.cancel');
    Route::post('/faculty/projects/{project}/reject', [\App\Http\Controllers\Faculty\ReviewController::class, 'reject'])->name('faculty.projects.reject');
    Route::post('/faculty/projects/{project}/reject-advisory', [\App\Http\Controllers\Faculty\ReviewController::class, 'rejectAdvisory'])->name('faculty.projects.reject-advisory');
});

Route::middleware(['auth', \App\Http\Middleware\UpdateLastActivity::class, 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    Route::post('/users/{user}/approve', [\App\Http\Controllers\Admin\UserController::class, 'approve'])->name('users.approve');
    Route::get('/logs', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('logs');
    
    // System Settings
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
    
    // Manage Categories
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class)->except(['create', 'show', 'edit']);

    // Manage Programs
    Route::resource('programs', \App\Http\Controllers\Admin\ProgramController::class)->except(['create', 'show', 'edit']);

    // Security Scan Demo
    Route::get('/security-scan-demo', [\App\Http\Controllers\Admin\SecurityScanDemoController::class, 'index'])->name('security-demo.index');
    Route::post('/security-scan-demo/scan', [\App\Http\Controllers\Admin\SecurityScanDemoController::class, 'scan'])->name('security-demo.scan');
    Route::get('/security-scan-demo/test-file/{type}', [\App\Http\Controllers\Admin\SecurityScanDemoController::class, 'downloadTestFile'])->name('security-demo.test-file');

    // Support Ticket Management
    Route::get('/support', [SupportTicketController::class, 'index'])->name('support.index');
    Route::get('/support/{ticket}', [SupportTicketController::class, 'show'])->name('support.show');
    Route::patch('/support/{ticket}/status', [SupportTicketController::class, 'updateStatus'])->name('support.status');
    Route::delete('/support/{ticket}', [SupportTicketController::class, 'destroy'])->name('support.destroy');
});

// Apply last.activity to protected routes that require auth
// PROJECT SUBMISSIONS: auth + last activity (role check temporarily removed for testing)
Route::middleware(['auth', \App\Http\Middleware\UpdateLastActivity::class])->group(function () {
    Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{project}/cancel', [ProjectController::class, 'cancel'])->name('projects.cancel');
    Route::post('/projects/abort-submission', [ProjectController::class, 'abortSubmission'])->name('projects.abort-submission');
    // Forced-download for any project file (bypasses browser inline rendering)
    Route::get('/files/{file}/download', function (\App\Models\ProjectFile $file) {
        $fullPath = \Illuminate\Support\Facades\Storage::disk('public')->path($file->path);
        abort_unless(file_exists($fullPath), 404);

        if ($file->type === 'attachment') {
            $user = auth()->user();
            $project = $file->project;
            $isOwner   = $project && $project->authors->contains($user);
            $isPrivileged = $user->isAdmin() || $user->isAdviser();

            abort_unless($isOwner || $isPrivileged, 403, 'Attachments are restricted to faculty and administrators.');
        }

        return response()->download($fullPath, $file->filename);
    })->name('files.download');

    // Streaming view for project files (supports seeking/skipping in videos)
    Route::get('/files/{file}/view', function (\App\Models\ProjectFile $file) {
        $fullPath = \Illuminate\Support\Facades\Storage::disk('public')->path($file->path);
        abort_unless(file_exists($fullPath), 404);

        if ($file->type === 'attachment') {
            $user = auth()->user();
            $project = $file->project;
            $isOwner   = $project && ($project->authors->contains($user) || $project->adviser_id == $user->id);
            $isPrivileged = $user->isAdmin();

            abort_unless($isOwner || $isPrivileged, 403, 'Attachments are restricted to faculty and administrators.');
        }

        // Force PDF to be inline for better browser/mobile support (Streaming Option)
        if (strtolower(pathinfo($fullPath, PATHINFO_EXTENSION)) === 'pdf') {
            return response()->stream(function () use ($fullPath) {
                readfile($fullPath);
            }, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline',
                'Content-Length' => filesize($fullPath),
                'X-Content-Type-Options' => 'nosniff',
                'Cache-Control' => 'private, max-age=0, must-revalidate',
            ]);
        }

        return response()->file($fullPath);
    })->name('files.view');
});

// Single project view (placed after /projects/create to avoid shadowing)
Route::get('/projects/{project}', [\App\Http\Controllers\ProjectController::class, 'show'])->name('projects.show');

// Dedicated Full-Screen Viewer (Bypasses mobile download issues)
Route::get('/projects/{project}/viewer', function (\App\Models\Project $project) {
    return view('projects.viewer', compact('project'));
})->middleware(['auth'])->name('projects.viewer');

// Adviser verification
Route::middleware(['auth', \App\Http\Middleware\UpdateLastActivity::class, 'role:adviser'])->group(function () {
    Route::post('/projects/{project}/verify', [VerificationController::class, 'store'])->name('projects.verify');
    Route::post('/projects/{project}/reverify-pdf', [VerificationController::class, 'revalidatePdf'])->name('projects.reverify-pdf');
});

// Admin project management
Route::middleware(['auth', \App\Http\Middleware\UpdateLastActivity::class, 'role:admin'])->group(function () {
    Route::get('/admin/projects', [AdminProjectController::class, 'index'])->name('admin.projects.index');
    Route::get('/admin/projects/create', [AdminProjectController::class, 'create'])->name('admin.projects.create');
    Route::post('/admin/projects', [AdminProjectController::class, 'store'])->name('admin.projects.store');
    Route::get('/admin/projects/bulk-create', [AdminProjectController::class, 'bulkCreate'])->name('admin.projects.bulk-create');
    Route::post('/admin/projects/bulk-store', [AdminProjectController::class, 'bulkStore'])->name('admin.projects.bulk-store');
    Route::get('/admin/projects/{project}/edit', [AdminProjectController::class, 'edit'])->name('admin.projects.edit');
    Route::put('/admin/projects/{project}', [AdminProjectController::class, 'update'])->name('admin.projects.update');
    Route::post('/admin/projects/bulk', [AdminProjectController::class, 'bulkAction'])->name('admin.projects.bulk');
    Route::post('/admin/projects/{project}/verify-pdf', [AdminProjectController::class, 'verifyPdf'])->name('admin.projects.verify-pdf');
    Route::post('/admin/projects/{project}/publish', [AdminProjectController::class, 'publish'])->name('admin.projects.publish');
    Route::delete('/admin/projects/{project}', [AdminProjectController::class, 'destroy'])->name('admin.projects.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Support Ticket Submission (authenticated users)
Route::middleware(['auth', \App\Http\Middleware\UpdateLastActivity::class])->group(function () {
    Route::post('/support/submit', [SupportTicketController::class, 'store'])->name('support.store');
    Route::get('/my-tickets', [SupportTicketController::class, 'myTickets'])->name('support.my-tickets');
});

require __DIR__.'/auth.php';
