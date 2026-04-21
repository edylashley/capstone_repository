<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $programs = Program::all();
        return view('admin.programs.index', compact('programs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:programs,name',
            'abbreviation' => 'required|string|max:50|unique:programs,abbreviation',
            'description' => 'nullable|string'
        ]);

        $program = Program::create($validated);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'program_created',
            'target_type' => 'program',
            'target_id' => $program->id,
            'ip' => $request->ip(),
            'meta' => ['name' => $program->name, 'abbreviation' => $program->abbreviation]
        ]);

        return redirect()->back()->with('success', 'Program added successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $program = Program::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:programs,name,'.$program->id,
            'abbreviation' => 'required|string|max:50|unique:programs,abbreviation,'.$program->id,
            'description' => 'nullable|string'
        ]);

        $program->update($validated);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'program_updated',
            'target_type' => 'program',
            'target_id' => $program->id,
            'ip' => $request->ip(),
            'meta' => ['name' => $program->name, 'abbreviation' => $program->abbreviation]
        ]);

        return redirect()->back()->with('success', 'Program updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $program = Program::findOrFail($id);
        
        // Optional: Check if there are projects or users using this program
        $projectsCount = \App\Models\Project::where('program', $program->abbreviation)->count();
        $usersCount = \App\Models\User::where('program', $program->abbreviation)->count();
        
        if ($projectsCount > 0 || $usersCount > 0) {
            return redirect()->back()->withErrors(['program' => "Cannot delete '{$program->abbreviation}'. It is currently used by {$projectsCount} project(s) and {$usersCount} user(s)."]);
        }

        $programName = $program->name;
        $programId = $program->id;
        $program->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'program_deleted',
            'target_type' => 'program',
            'target_id' => $programId,
            'ip' => request()->ip(),
            'meta' => ['name' => $programName]
        ]);

        return redirect()->back()->with('success', 'Program deleted successfully.');
    }
}
