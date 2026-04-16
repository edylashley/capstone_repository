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
        return view('admin.categories.index', compact('categories'));
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
}
