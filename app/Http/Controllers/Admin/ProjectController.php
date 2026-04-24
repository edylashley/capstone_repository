<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Publish a verified project (admin only)
     */
    public function verifyPdf(Request $request, $id)
    {
        $project = \App\Models\Project::with('files')->findOrFail($id);
        
        // Find manuscript
        $manuscript = $project->files->where('type', 'manuscript')->first();
        if (!$manuscript) {
            return redirect()->back()->with('error', 'No manuscript found to verify');
        }

        $fullPath = \Storage::disk('public')->path($manuscript->path);
        
        if (!file_exists($fullPath)) {
            return redirect()->back()->with('error', 'Manuscript file missing from storage');
        }

        // Run validation
        $validator = app(\App\Services\PDFValidator::class);
        $validation = $validator->validate($fullPath);

        // Update project
        $combinedNotes = [
            '✓ Validator: ' . ($validation['valid'] ? 'Passed' : 'Failed')
        ];
        $combinedNotes = array_merge($combinedNotes, $validation['notes']);

        $project->manuscript_validated = $validation['valid'];
        $project->manuscript_validation_notes = implode("\n", $combinedNotes);
        $project->save();

        // Log it
        \App\Models\ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'manuscript_revalidated',
            'target_type' => 'project',
            'target_id' => $project->id,
            'ip' => $request->ip(),
            'meta' => ['valid' => $validation['valid'], 'notes' => $combinedNotes],
        ]);

        $msg = $validation['valid'] ? 'Project Passed Verification' : 'Project Failed Verification';
        return redirect()->back()->with($validation['valid'] ? 'success' : 'error', $msg);
    }

    /**
     * Publish a verified project (admin only)
     */
    public function publish(Request $request, $id)
    {
        $project = \App\Models\Project::findOrFail($id);

        $project->status = 'published';
        $project->is_published = true;
        $project->published_at = now();
        $project->save();

        \App\Models\ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'publish_project',
            'target_type' => 'project',
            'target_id' => $project->id,
            'ip' => $request->ip(),
            'meta' => ['published_at' => $project->published_at->toDateTimeString()],
        ]);

        // Send email notification to students
        try {
            foreach ($project->authors as $author) {
                \Illuminate\Support\Facades\Mail::to($author)->queue(new \App\Mail\ProjectPublished($project));
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send project publication email: ' . $e->getMessage());
        }

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Project published']);
        }

        return redirect()->route('admin.projects.index')->with('status', 'Project published');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = \App\Models\Project::query();

        if ($request->filled('status')) {
            $status = $request->query('status');
            if ($status === 'pending') {
                $query->whereIn('status', ['pending', 'approved', 'verified']);
            } else {
                $query->where('status', $status);
            }
        }
        
        if ($request->filled('program')) {
            $query->where('program', $request->query('program'));
        }

        $projects = $query->with(['authors', 'files'])->paginate(20)->withQueryString();
        $programs = \App\Models\Program::all();

        return view('admin.projects.index', compact('projects', 'programs'));
    }

    /**
     * Show the form for creating a new resource (Archiving past projects).
     */
    public function create()
    {
        $categories = \App\Models\Category::all();
        $programs = \App\Models\Program::all();
        return view('admin.projects.create', compact('categories', 'programs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('projects')->where(function ($query) {
                    return $query->where('status', '!=', 'rejected');
                })
            ],
            'authors_list' => 'required|string|max:1000',
            'year' => 'required|integer|min:1900',
            'adviser_id' => 'nullable|exists:users,id',
            'adviser_name' => 'nullable|string|max:255',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id',
            'abstract' => 'nullable|string',
            'program' => ['required', 'string', \Illuminate\Validation\Rule::in(\App\Models\Program::pluck('abbreviation')->toArray())],
            'manuscript' => [
                'required', 'file', 'mimes:pdf', 'max:51200',
                function ($attribute, $value, $fail) {
                    $hash = hash_file('sha256', $value->path());
                    if (\App\Models\ProjectFile::where('file_hash', $hash)->exists()) {
                        $fail('This manuscript exactly matches one already present in the system.');
                    }
                }
            ],
            'attachments.*' => [
                'nullable', 'file', 'max:204800',
                function ($attribute, $value, $fail) {
                    $hash = hash_file('sha256', $value->path());
                    static $attachHashes = [];
                    if (isset($attachHashes[$hash]) || \App\Models\ProjectFile::where('file_hash', $hash)->exists()) {
                        $fail('An attachment exactly matches a file already present in the system.');
                    }
                    $attachHashes[$hash] = true;
                }
            ], // max 200MB per file
        ]);

        if (empty($validated['adviser_id']) && empty($validated['adviser_name'])) {
            return back()->withErrors(['adviser_id' => 'Please select an internal adviser or type an external adviser\'s name.'])->withInput();
        }

        $program = $validated['program'];
        $slug = \Illuminate\Support\Str::slug($validated['title']);
        $originalSlug = $slug;
        $i = 1;
        while (\App\Models\Project::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $i++;
        }

        // Direct archive bypasses pending/verified statuses
        $project = \App\Models\Project::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'abstract' => $validated['abstract'] ?? null,
            'year' => $validated['year'],
            'adviser_name' => $validated['adviser_name'] ?? null,
            'program' => $program,
            'specialization' => null, // Deprecated
            'authors_list' => $validated['authors_list'],
            'status' => 'published', // INSTANT PUBLISH
            'is_published' => true,
            'published_at' => now(),
            // Automatically mark manuscript as validated since it's an admin archive
            'manuscript_validated' => true,
            'manuscript_validation_notes' => 'Archived by Administrator. Manuscript assumed valid.',
        ]);

        // Sync categories
        $project->categories()->sync($validated['categories']);

        // Upload manuscript
        $file = $request->file('manuscript');
        $dir = "projects/{$project->year}/{$project->slug}";
        $filename = 'manuscript.pdf';
        $path = $file->storeAs($dir, $filename, 'public');

        $fullPath = \Illuminate\Support\Facades\Storage::disk('public')->path($path);
        $fileHash = hash_file('sha256', $fullPath);

        \App\Models\ProjectFile::create([
            'project_id' => $project->id,
            'type' => 'manuscript',
            'filename' => $filename,
            'path' => $path,
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'checksum' => $fileHash,
            'file_hash' => $fileHash,
            'uploaded_by' => $request->user()->id,
            'is_primary' => true,
        ]);

        $fullPath = \Illuminate\Support\Facades\Storage::disk('public')->path($path);
        $validator = app(\App\Services\PDFValidator::class);
        $validation = $validator->validate($fullPath);
        if (isset($validation['text']) && !empty($validation['text'])) {
            $project->full_text = $validation['text'];
            $project->save();
        }

        // Process attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $attachment) {
                if ($attachment->isValid()) {
                    $attachFilename = time() . '_' . \Illuminate\Support\Str::slug(pathinfo($attachment->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $attachment->getClientOriginalExtension();
                    $attachPath = $attachment->storeAs($dir, $attachFilename, 'public');

                    $attachFullPath = \Illuminate\Support\Facades\Storage::disk('public')->path($attachPath);
                    $attachHash = hash_file('sha256', $attachFullPath);

                    \App\Models\ProjectFile::create([
                        'project_id' => $project->id,
                        'type' => 'attachment',
                        'filename' => $attachment->getClientOriginalName(),
                        'path' => $attachPath,
                        'mime_type' => $attachment->getClientMimeType(),
                        'size' => $attachment->getSize(),
                        'checksum' => $attachHash,
                        'file_hash' => $attachHash,
                        'uploaded_by' => $request->user()->id,
                        'is_primary' => false,
                    ]);
                }
            }
        }

        \App\Models\ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'project_manually_uploaded',
            'target_type' => 'project',
            'target_id' => $project->id,
            'ip' => $request->ip(),
            'meta' => ['title' => $project->title, 'year' => $project->year],
        ]);

        return redirect()->route('admin.projects.index')->with('success', 'Past project successfully uploaded and published.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $project = \App\Models\Project::withTrashed()->findOrFail($id);
        $categories = \App\Models\Category::all();
        $programs = \App\Models\Program::all();
        
        return view('admin.projects.edit', compact('project', 'categories', 'programs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $project = \App\Models\Project::withTrashed()->findOrFail($id);
        
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('projects')->ignore($project->id)->where(function ($query) {
                    return $query->where('status', '!=', 'rejected');
                })
            ],
            'year' => 'required|integer|min:2000',
            'authors_list' => 'nullable|string|max:1000',
            'adviser_name' => 'required|string|max:255',
            'abstract' => 'nullable|string',
            'status' => 'required|in:pending,verified,approved,published,archived',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id',
            'program' => ['required', 'string', \Illuminate\Validation\Rule::in(\App\Models\Program::pluck('abbreviation')->toArray())],
        ]);

        $project->update([
            'title' => $validated['title'],
            'year' => $validated['year'],
            'authors_list' => $validated['authors_list'],
            'adviser_id' => null, // Explicitly null as we are moving away from adviser roles
            'adviser_name' => $validated['adviser_name'],
            'abstract' => $validated['abstract'],
            'status' => $validated['status'],
            'specialization' => null, // Deprecated
            'program' => $validated['program'],
        ]);

        $project->categories()->sync($validated['categories']);

        return redirect()->route('admin.projects.index')->with('success', 'Project metadata updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $project = \App\Models\Project::findOrFail($id);
        
        // Store project info for logging before deletion
        $projectTitle = $project->title;
        $projectId = $project->id;
        
        // We no longer delete files here because we want them to be restorable 
        // from the Central Archive. Physical file deletion now happens only 
        // during a "Force Delete/Purge" in the Archive Center.
        
        // Log the deletion before removing the record
        \App\Models\ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'delete_project',
            'target_type' => 'project',
            'target_id' => $projectId,
            'ip' => $request->ip(),
            'meta' => [
                'title' => $projectTitle,
                'status' => $project->status,
                'deleted_at' => now()->toDateTimeString()
            ],
        ]);
        
        // Delete the project (cascade will handle related records)
        $project->delete();
        
        return redirect()->route('admin.projects.index')->with('status', 'Project deleted successfully');
    }

    /**
     * Handle bulk actions for projects.
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:publish,delete,archive,pending',
            'project_ids' => 'required|array',
            'project_ids.*' => 'exists:projects,id',
        ]);

        $action = $validated['action'];
        $projectIds = $validated['project_ids'];

        if ($action === 'publish') {
            $projects = \App\Models\Project::whereIn('id', $projectIds)->get();

            $publishedCount = 0;
            foreach ($projects as $project) {
                $project->status = 'published';
                $project->is_published = true;
                $project->published_at = now();
                $project->save();

                \App\Models\ActivityLog::create([
                    'user_id' => $request->user()->id,
                    'action' => 'publish_project',
                    'target_type' => 'project',
                    'target_id' => $project->id,
                    'ip' => $request->ip(),
                    'meta' => ['bulk' => true, 'published_at' => $project->published_at->toDateTimeString()],
                ]);

                // Send email notification to students
                try {
                    foreach ($project->authors as $author) {
                        \Illuminate\Support\Facades\Mail::to($author)->queue(new \App\Mail\ProjectPublished($project));
                    }
                } catch (\Exception $e) {
                    \Log::error('Failed to send project publication email during bulk action: ' . $e->getMessage());
                }

                $publishedCount++;
            }
            return redirect()->back()->with('success', "{$publishedCount} selected projects published successfully.");
        } elseif ($action === 'archive') {
            $projects = \App\Models\Project::whereIn('id', $projectIds)->get();
            $archivedCount = 0;
            foreach ($projects as $project) {
                $project->status = 'archived';
                $project->is_published = false;
                $project->save();
                
                \App\Models\ActivityLog::create([
                    'user_id' => $request->user()->id,
                    'action' => 'archive_project',
                    'target_type' => 'project',
                    'target_id' => $project->id,
                    'ip' => $request->ip(),
                    'meta' => ['bulk' => true],
                ]);
                $archivedCount++;
            }
            return redirect()->back()->with('success', "{$archivedCount} selected projects archived successfully.");
        } elseif ($action === 'pending') {
            $projects = \App\Models\Project::whereIn('id', $projectIds)->get();
            $pendingCount = 0;
            foreach ($projects as $project) {
                $project->status = 'pending';
                $project->is_published = false;
                $project->save();
                
                \App\Models\ActivityLog::create([
                    'user_id' => $request->user()->id,
                    'action' => 'project_set_to_pending_bulk',
                    'target_type' => 'project',
                    'target_id' => $project->id,
                    'ip' => $request->ip(),
                    'meta' => ['bulk' => true],
                ]);
                $pendingCount++;
            }
            return redirect()->back()->with('success', "{$pendingCount} selected projects moved back to pending review.");
        } elseif ($action === 'delete') {
            $projects = \App\Models\Project::whereIn('id', $projectIds)->get();
            foreach ($projects as $project) {
                foreach ($project->files as $file) {
                    if (\Storage::disk('public')->exists($file->path)) {
                        \Storage::disk('public')->delete($file->path);
                    }
                }
                $project->delete();
            }
            return redirect()->back()->with('success', 'Selected projects deleted successfully.');
        }
        
        return redirect()->back()->with('error', 'Invalid action');
    }

    /**
     * Show the bulk create form for adding multiple past projects.
     */
    public function bulkCreate()
    {
        $categories = \App\Models\Category::all();
        $programs = \App\Models\Program::all();
        return view('admin.projects.bulk-create', compact('categories', 'programs'));
    }

    /**
     * Store multiple projects submitted via the bulk create form.
     */
    public function bulkStore(Request $request)
    {
        $request->validate([
            'projects' => 'required|array|min:1',
            'projects.*.title' => [
                'required',
                'string',
                'max:255',
                'distinct',
                \Illuminate\Validation\Rule::unique('projects', 'title')->where(function ($query) {
                    return $query->where('status', '!=', 'rejected');
                })
            ],
            'projects.*.authors_list' => 'required|string|max:1000',
            'projects.*.year' => 'required|integer|min:1900',
            'projects.*.adviser_name' => 'required|string|max:255',
            'projects.*.categories' => 'required|array|min:1',
            'projects.*.categories.*' => 'exists:categories,id',
            'projects.*.program' => ['required', 'string', \Illuminate\Validation\Rule::in(\App\Models\Program::pluck('abbreviation')->toArray())],
            'projects.*.abstract' => 'nullable|string',
            'projects.*.manuscript' => [
                'required', 'file', 'mimes:pdf', 'max:51200',
                function ($attribute, $value, $fail) {
                    $parts = explode('.', $attribute);
                    $entryNum = isset($parts[1]) ? (int)$parts[1] + 1 : 1;
                    $hash = hash_file('sha256', $value->path());
                    static $bulkMsHashes = [];
                    if (isset($bulkMsHashes[$hash]) || \App\Models\ProjectFile::where('file_hash', $hash)->exists()) {
                        $fail("Duplicate manuscript content detected in Project Entry #{$entryNum}. This file already exists in the system.");
                    }
                    $bulkMsHashes[$hash] = true;
                }
            ],
            'projects.*.attachments.*' => [
                'nullable', 'file', 'max:204800',
                function ($attribute, $value, $fail) {
                    $parts = explode('.', $attribute);
                    $entryNum = isset($parts[1]) ? (int)$parts[1] + 1 : 1;
                    $hash = hash_file('sha256', $value->path());
                    static $bulkAttHashes = [];
                    if (isset($bulkAttHashes[$hash]) || \App\Models\ProjectFile::where('file_hash', $hash)->exists()) {
                        $fail("Duplicate attachment content detected in Project Entry #{$entryNum}. This file already exists in the system.");
                    }
                    $bulkAttHashes[$hash] = true;
                }
            ],
        ]);

        $projectsData = $request->input('projects');
        $createdCount = 0;

        foreach ($projectsData as $idx => $data) {
            // Validate adviser
            if (empty($data['adviser_id']) && empty($data['adviser_name'])) {
                continue; // Skip entries without adviser info
            }

            // Generate unique slug
            $slug = \Illuminate\Support\Str::slug($data['title']);
            $originalSlug = $slug;
            $i = 1;
            while (\App\Models\Project::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $i++;
            }

            // Create project record (instant publish)
            $project = \App\Models\Project::create([
                'title' => $data['title'],
                'slug' => $slug,
                'abstract' => $data['abstract'] ?? null,
                'year' => $data['year'],
                'adviser_id' => null,
                'adviser_name' => $data['adviser_name'],
                'program' => $data['program'],
                'specialization' => null, // Deprecated
                'authors_list' => $data['authors_list'],
                'status' => 'published',
                'is_published' => true,
                'published_at' => now(),
                'manuscript_validated' => true,
                'manuscript_validation_notes' => 'Bulk archived by Administrator. Manuscript assumed valid.',
            ]);

            // Sync categories
            $project->categories()->sync($data['categories']);

            // Upload manuscript
            $manuscriptFile = $request->file("projects.{$idx}.manuscript");
            if ($manuscriptFile && $manuscriptFile->isValid()) {
                $dir = "projects/{$project->year}/{$project->slug}";
                $filename = 'manuscript.pdf';
                $path = $manuscriptFile->storeAs($dir, $filename, 'public');

                $fullPath = \Illuminate\Support\Facades\Storage::disk('public')->path($path);
                $fileHash = hash_file('sha256', $fullPath);

                \App\Models\ProjectFile::create([
                    'project_id' => $project->id,
                    'type' => 'manuscript',
                    'filename' => $filename,
                    'path' => $path,
                    'mime_type' => $manuscriptFile->getClientMimeType(),
                    'size' => $manuscriptFile->getSize(),
                    'checksum' => $fileHash,
                    'file_hash' => $fileHash,
                    'uploaded_by' => $request->user()->id,
                    'is_primary' => true,
                ]);

                // Extract full text if possible
                $fullPath = \Illuminate\Support\Facades\Storage::disk('public')->path($path);
                $validator = app(\App\Services\PDFValidator::class);
                $validation = $validator->validate($fullPath);
                if (isset($validation['text']) && !empty($validation['text'])) {
                    $project->full_text = $validation['text'];
                    $project->save();
                }
            }

            // Upload attachments
            $attachments = $request->file("projects.{$idx}.attachments");
            if ($attachments) {
                $dir = "projects/{$project->year}/{$project->slug}";
                foreach ($attachments as $attachment) {
                    if ($attachment->isValid()) {
                        $attachFilename = time() . '_' . \Illuminate\Support\Str::slug(pathinfo($attachment->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $attachment->getClientOriginalExtension();
                        $attachPath = $attachment->storeAs($dir, $attachFilename, 'public');

                        $attachFullPath = \Illuminate\Support\Facades\Storage::disk('public')->path($attachPath);
                        $attachHash = hash_file('sha256', $attachFullPath);

                        \App\Models\ProjectFile::create([
                            'project_id' => $project->id,
                            'type' => 'attachment',
                            'filename' => $attachment->getClientOriginalName(),
                            'path' => $attachPath,
                            'mime_type' => $attachment->getClientMimeType(),
                            'size' => $attachment->getSize(),
                            'checksum' => $attachHash,
                            'file_hash' => $attachHash,
                            'uploaded_by' => $request->user()->id,
                            'is_primary' => false,
                        ]);
                    }
                }
            }

            \App\Models\ActivityLog::create([
                'user_id' => $request->user()->id,
                'action' => 'project_bulk_uploaded',
                'target_type' => 'project',
                'target_id' => $project->id,
                'ip' => $request->ip(),
                'meta' => ['title' => $project->title, 'year' => $project->year],
            ]);

            $createdCount++;
        }

        return redirect()->route('admin.projects.index')->with('success', "{$createdCount} past project(s) successfully uploaded and published.");
    }

    /**
     * Confirm and Publish a project (replaces adviser confirmation and intermediate approval step)
     */
    public function approve(Request $request, $id)
    {
        $project = \App\Models\Project::findOrFail($id);

        if (!in_array($project->status, ['pending', 'approved', 'verified'])) {
            return redirect()->back()->with('error', 'Project is not in a confirmable state');
        }

        $project->update([
            'status' => 'published',
            'is_published' => true,
            'published_at' => now(),
        ]);

        \App\Models\ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'project_confirmed_published_admin',
            'target_type' => 'project',
            'target_id' => $project->id,
            'ip' => $request->ip(),
            'meta' => ['project_title' => $project->title, 'published_at' => $project->published_at->toDateTimeString()],
        ]);

        try {
            foreach ($project->authors as $author) {
                \Illuminate\Support\Facades\Mail::to($author)->queue(new \App\Mail\ProjectPublished($project));
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send admin approval/publication email: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Project confirmed and published successfully.');
    }

    /**
     * Reject a project (replaces adviser return)
     */
    public function reject(Request $request, $id)
    {
        $project = \App\Models\Project::findOrFail($id);

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:2000',
        ]);

        $project->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        \App\Models\ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'project_rejected_admin',
            'target_type' => 'project',
            'target_id' => $project->id,
            'ip' => $request->ip(),
            'meta' => ['reason' => $validated['rejection_reason']],
        ]);

        try {
            foreach ($project->authors as $author) {
                \Illuminate\Support\Facades\Mail::to($author)->queue(new \App\Mail\ProjectReturned($project));
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send admin rejection email: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Project returned to student for revision.');
    }
}
