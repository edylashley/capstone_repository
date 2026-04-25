<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;

class ArchiveController extends Controller
{
    public function index(Request $request)
    {
        $trashedProjects = Project::onlyTrashed()->with('adviser')->latest('deleted_at')->get();
        $trashedUsers = User::onlyTrashed()->latest('deleted_at')->get();
        $trashedCategories = Category::onlyTrashed()->latest('deleted_at')->get();

        return view('admin.archive.index', compact(
            'trashedProjects',
            'trashedUsers',
            'trashedCategories'
        ));
    }

    public function restore(Request $request, $type, $id)
    {
        $model = $this->getModel($type, $id);
        
        if (!$model) {
            return back()->with('error', 'Item not found in archive.');
        }

        // --- CONFLICT PREVENTION ---
        if ($type === 'project') {
            $conflict = Project::where('title', $model->title)
                ->orWhere('slug', $model->slug)
                ->exists();
                
            if ($conflict) {
                return back()->with('error', 'Cannot restore. A project with the same title or URL slug already exists in the active library.');
            }
        }

        if ($type === 'user') {
            $conflict = User::where('email', $model->email)
                ->orWhere('student_id', $model->student_id)
                ->exists();

            if ($conflict) {
                return back()->with('error', 'Cannot restore. A user with this email or Student ID already exists.');
            }
        }
        // ---------------------------

        $model->restore();

        return redirect()->route('admin.archive.index', ['tab' => $request->get('tab', 'projects')])
            ->with('success', 'Item has been successfully restored.');
    }

    public function forceDelete(Request $request, $type, $id)
    {
        $model = $this->getModel($type, $id);
        
        if (!$model) {
            return back()->with('error', 'Item not found in archive.');
        }

        if ($type === 'project') {
            foreach ($model->files as $file) {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($file->path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($file->path);
                }
            }
            $model->files()->delete();
        }

        $model->forceDelete();

        return redirect()->route('admin.archive.index', ['tab' => $request->get('tab', 'projects')])
            ->with('success', 'Item has been permanently purged from the system.');
    }

    /**
     * Handle bulk/all actions for archived items
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:restore,delete',
            'type' => 'required|in:project,user,category',
            'all' => 'nullable|boolean',
            'ids' => 'nullable|array',
        ]);

        $action = $validated['action'];
        $type = $validated['type'];
        $count = 0;

        // Get the target items
        if ($request->has('all') && $request->all) {
            $query = match($type) {
                'project' => Project::onlyTrashed(),
                'user' => User::onlyTrashed(),
                'category' => Category::onlyTrashed(),
            };
            $items = $query->get();
        } else {
            $ids = $request->input('ids', []);
            $items = match($type) {
                'project' => Project::onlyTrashed()->whereIn('id', $ids)->get(),
                'user' => User::onlyTrashed()->whereIn('id', $ids)->get(),
                'category' => Category::onlyTrashed()->whereIn('id', $ids)->get(),
            };
        }

        foreach ($items as $model) {
            if ($action === 'restore') {
                // Conflict skip for bulk
                if ($type === 'project' && Project::where('title', $model->title)->exists()) continue;
                if ($type === 'user' && User::where('email', $model->email)->exists()) continue;
                
                $model->restore();
                $count++;
            } else {
                if ($type === 'project') {
                    foreach ($model->files as $file) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($file->path);
                    }
                    $model->files()->delete();
                }
                $model->forceDelete();
                $count++;
            }
        }

        $message = $action === 'restore' 
            ? "Successfully restored $count items." 
            : "Successfully purged $count items permanently.";

        return redirect()->route('admin.archive.index', ['tab' => $request->get('tab', 'projects')])
            ->with($count > 0 ? 'success' : 'error', $message);
    }

    protected function getModel($type, $id)
    {
        return match($type) {
            'project'  => Project::onlyTrashed()->find($id),
            'user'     => User::onlyTrashed()->find($id),
            'category' => Category::onlyTrashed()->find($id),
            default    => null,
        };
    }
}
