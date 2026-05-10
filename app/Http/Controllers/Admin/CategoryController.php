<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = \App\Models\Category::all();
        
        // Fetch unique custom categories and their usage count
        $emergingTopics = \App\Models\Project::whereNotNull('custom_category')
            ->where('custom_category', '!=', '')
            ->select('custom_category', \DB::raw('count(*) as count'))
            ->groupBy('custom_category')
            ->orderBy('count', 'desc')
            ->get();

        return view('admin.categories.index', compact('categories', 'emergingTopics'));
    }

    public function create()
    {
        // Not used, modal inside index instead
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string'
        ]);

        $category = \App\Models\Category::create($validated);

        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'category_created',
            'target_type' => 'category',
            'target_id' => $category->id,
            'ip' => $request->ip(),
            'meta' => ['name' => $category->name]
        ]);

        return redirect()->back()->with('success', 'Category added successfully.');
    }

    public function show(string $id)
    {
        // 
    }

    public function edit(string $id)
    {
        // Not used, edit modal in index
    }

    public function update(Request $request, string $id)
    {
        $category = \App\Models\Category::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,'.$category->id,
            'description' => 'nullable|string'
        ]);

        $category->update($validated);

        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'category_updated',
            'target_type' => 'category',
            'target_id' => $category->id,
            'ip' => $request->ip(),
            'meta' => ['name' => $category->name]
        ]);

        return redirect()->back()->with('success', 'Category updated successfully.');
    }

    public function destroy(string $id)
    {
        $category = \App\Models\Category::findOrFail($id);
        
        // Check if there are projects using this category
        $projectsCount = \App\Models\Project::where('specialization', $category->name)->count();
        if ($projectsCount > 0) {
            return redirect()->back()->withErrors(['category' => "Cannot delete '{$category->name}'. It is currently used by {$projectsCount} project(s)."]);
        }

        $categoryName = $category->name;
        $categoryId = $category->id;
        $category->delete();

        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'category_deleted',
            'target_type' => 'category',
            'target_id' => $categoryId,
            'ip' => request()->ip(),
            'meta' => ['name' => $categoryName]
        ]);

        return redirect()->back()->with('success', 'Category deleted successfully.');
    }
    public function promote(Request $request)
    {
        $name = $request->input('name');
        
        if (empty($name)) {
            return redirect()->back()->withErrors(['name' => 'Invalid category name.']);
        }

        // 1. Create the official category
        $category = \App\Models\Category::firstOrCreate([
            'name' => ucwords(strtolower(trim($name)))
        ]);

        // 2. Find all projects using this custom category
        $projects = \App\Models\Project::where('custom_category', $name)->get();
        
        foreach ($projects as $project) {
            // Attach the new official category
            $project->categories()->syncWithoutDetaching([$category->id]);
            
            // Clear the custom category field
            $project->update([
                'custom_category' => null,
                'specialization' => null
            ]);
        }

        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'category_promoted',
            'target_type' => 'category',
            'target_id' => $category->id,
            'ip' => $request->ip(),
            'meta' => ['name' => $name, 'projects_affected' => $projects->count()]
        ]);

        return redirect()->back()->with('success', "Topic '{$name}' has been promoted to an official category and {$projects->count()} projects were updated.");
    }
}
