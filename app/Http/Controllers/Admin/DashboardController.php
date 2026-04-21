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
            'pending_projects' => Project::where('status', 'pending')->count(),
            'approved_projects' => Project::where('status', 'approved')->count(),
            'archived_projects' => Project::where('status', 'archived')->count(),
            'total_users' => User::count(),
            'students' => User::where('role', 'student')->count(),
            'faculty' => User::where('role', 'adviser')->count(),
            'total_storage' => $totalStorage,
            'open_tickets' => SupportTicket::where('status', 'pending')->count(),
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

        return view('admin.dashboard', [
            'projects' => $projects,
            'stats' => $stats,
        ]);
    }
}
