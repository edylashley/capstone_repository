<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        // Build query for approved projects (students can only see approved/published + their own pending)
        // Note: 'published' is treated as approved for backward compatibility
        $query = Project::whereIn('status', ['approved', 'published'])
            ->with(['adviser', 'authors']);

        // Search by title
        if ($request->filled('title')) {
            $query->where('title', 'like', '%'.$request->query('title').'%');
        }

        // Search by year
        if ($request->filled('year')) {
            $query->where('year', $request->query('year'));
        }

        // Search by keyword
        if ($request->filled('keyword')) {
            $keyword = $request->query('keyword');
            $query->where(function ($q) use ($keyword) {
                $q->whereJsonContains('keywords', $keyword)
                    ->orWhere('title', 'like', '%'.$keyword.'%')
                    ->orWhere('abstract', 'like', '%'.$keyword.'%');
            });
        }

        $approvedProjects = $query->orderBy('year', 'desc')
            ->orderBy('title')
            ->paginate(12)
            ->withQueryString();

        // Get user's own projects (all statuses)
        $myProjects = Project::whereHas('authors', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
            ->with(['adviser', 'authors'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('student.home', [
            'approvedProjects' => $approvedProjects,
            'myProjects' => $myProjects,
        ]);
    }
}
