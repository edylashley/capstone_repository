<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        // Get projects where this user is the adviser
        $projects = Project::where('adviser_id', $user->id)
            ->with(['authors', 'files'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('faculty.review', [
            'projects' => $projects,
        ]);
    }

    public function approve(Request $request, Project $project): RedirectResponse
    {
        $user = $request->user();

        // Verify the user is the adviser for this project
        if ($project->adviser_id !== $user->id) {
            abort(403, 'You are not authorized to confirm this project.');
        }

        $project->update(['status' => 'approved']);

        // Log the approval action
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'project_confirmed_final',
            'target_type' => Project::class,
            'target_id' => $project->id,
            'ip' => $request->ip(),
            'meta' => [
                'project_title' => $project->title,
                'project_id' => $project->id,
            ],
        ]);

        // Send email notification to students and admins
        try {
            // Email Students
            foreach ($project->authors as $author) {
                \Illuminate\Support\Facades\Mail::to($author)->queue(new \App\Mail\ProjectApproved($project));
            }
            
            // Email Admins (Notify that project is ready for publication)
            $admins = \App\Models\User::where('role', \App\Models\User::ROLE_ADMIN)->active()->get();
            foreach ($admins as $admin) {
                \Illuminate\Support\Facades\Mail::to($admin)->queue(new \App\Mail\ReadyForPublication($project));
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send project approval/notification emails: ' . $e->getMessage());
        }

        return redirect()->route('faculty.review')
            ->with('success', 'Final version confirmed successfully.');
    }

    public function cancel(Request $request, Project $project): RedirectResponse
    {
        $user = $request->user();

        // Verify the user is the adviser for this project
        if ($project->adviser_id !== $user->id) {
            abort(403, 'You are not authorized to modify this project.');
        }

        // Only allow cancellation if status is 'approved'
        if ($project->status !== 'approved') {
            return redirect()->back()->withErrors(['project' => 'Only confirmed projects can be cancelled.']);
        }

        $project->update(['status' => 'pending']);

        // Log the cancellation action
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'project_confirmation_cancelled',
            'target_type' => Project::class,
            'target_id' => $project->id,
            'ip' => $request->ip(),
            'meta' => [
                'project_title' => $project->title,
                'project_id' => $project->id,
                'reason' => 'adviser_reverted',
            ],
        ]);

        return redirect()->route('faculty.review')
            ->with('success', 'Project confirmation cancelled. It is now back to pending status.');
    }
    public function reject(Request $request, Project $project): RedirectResponse
    {
        $user = $request->user();

        // Verify the user is the adviser for this project
        if ($project->adviser_id !== $user->id) {
            abort(403, 'You are not authorized to return this project.');
        }

        // Only allow rejection if status is 'pending'
        if ($project->status !== 'pending') {
            return redirect()->back()->withErrors(['project' => 'Only pending projects can be returned.']);
        }

        // Validate rejection reason
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:2000',
        ], [
            'rejection_reason.required' => 'Please provide a reason for returning this project.',
        ]);

        $project->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        // Log the rejection action
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'project_returned_for_revision',
            'target_type' => Project::class,
            'target_id' => $project->id,
            'ip' => $request->ip(),
            'meta' => [
                'project_title' => $project->title,
                'project_id' => $project->id,
                'reason' => $validated['rejection_reason'],
            ],
        ]);

        // Send email notification to students
        try {
            foreach ($project->authors as $author) {
                \Illuminate\Support\Facades\Mail::to($author)->queue(new \App\Mail\ProjectReturned($project));
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send project returned email: ' . $e->getMessage());
        }

        return redirect()->route('faculty.review')
            ->with('success', 'Project returned to student for corrections.');
    }
}
