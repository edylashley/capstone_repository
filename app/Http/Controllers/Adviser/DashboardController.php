<?php

namespace App\Http\Controllers\Adviser;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        // 1. Calculate Stats
        $stats = [
            'total_advising' => Project::where('adviser_id', $user->id)->count(),
            'pending_confirmation' => Project::where('adviser_id', $user->id)->where('status', 'pending')->count(),
            'confirmed_projects' => Project::where('adviser_id', $user->id)->whereIn('status', ['approved', 'published'])->count(),
        ];

        // 2. Get Recent Submissions (Limit 5)
        $recentProjects = Project::where('adviser_id', $user->id)
            ->with(['authors'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('adviser.dashboard', [
            'stats' => $stats,
            'recentProjects' => $recentProjects,
        ]);
    }
}
