<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        // Get all projects (pending, approved, archived)
        $projects = Project::with(['adviser', 'authors'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $totalSizeBytes = \App\Models\ProjectFile::sum('size');
        $totalStorage = number_format($totalSizeBytes / (1024 * 1024), 2) . ' MB';

        // Get statistics
        $stats = [
            'total_projects' => Project::count(),
            'pending_projects' => Project::whereIn('status', ['pending', 'returned'])->count(),
            'approved_projects' => Project::whereIn('status', ['approved', 'published'])->count(),
            'archived_projects' => Project::where('status', 'archived')->count(),
            'total_users' => User::count(),
            'students' => User::where('role', 'student')->count(),
            'faculty' => User::where('role', 'adviser')->count(),
            'total_storage' => $totalStorage,
            'open_tickets' => SupportTicket::where('status', 'pending')->count(),
            'pending_users' => User::where('is_active', false)->count(),
        ];

        // Check Security Engine Status
        $securityStatus = 'offline';
        if (config('repository.filescan_enabled')) {
            $clamscan = config('repository.clamscan_path');
            // Basic path resolution (check if it exists)
            if (file_exists($clamscan)) {
                if (str_contains(strtolower($clamscan), 'clamdscan')) {
                    // Ping the daemon to see if it's responding
                    $cmd = '"' . $clamscan . '" --ping 3 2>&1';
                    @exec($cmd, $output, $exit);
                    $securityStatus = ($exit === 0) ? 'online' : 'offline';
                } else {
                    // Standalone clamscan doesn't need a service
                    $securityStatus = 'ready';
                }
            }
        }
        $stats['security_status'] = $securityStatus;

        // Program Analysis Data (Including Zero Counts)
        $allPrograms = \App\Models\Program::pluck('abbreviation')->toArray();
        $programCounts = Project::select('program', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('program')
            ->pluck('total', 'program')
            ->toArray();

        $programStats = [];
        foreach ($allPrograms as $prog) {
            $programStats[$prog] = $programCounts[$prog] ?? 0;
        }

        // Add any existing project programs that might not be in the Program model
        foreach ($programCounts as $prog => $count) {
            if (!isset($programStats[$prog])) {
                $programStats[$prog] = $count;
            }
        }

        // Category Analysis Data (Including Zero Counts, Top 10)
        $categoryStats = \App\Models\Category::withCount('projects')
            ->orderBy('projects_count', 'desc')
            ->limit(10)
            ->get()
            ->pluck('projects_count', 'name')
            ->toArray();

        // Recent Activity (Filtered strictly for actions requiring Admin attention)
        $recentActivities = \App\Models\ActivityLog::with([
            'user' => function ($query) {
                $query->withTrashed();
            }
        ])
            ->whereIn('action', ['account_request', 'upload_project', 'project_submitted', 'security_threat_blocked'])
            ->whereNotNull('user_id')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', [
            'projects' => $projects,
            'stats' => $stats,
            'programStats' => $programStats,
            'categoryStats' => $categoryStats,
            'recentActivities' => $recentActivities,
        ]);
    }
}
