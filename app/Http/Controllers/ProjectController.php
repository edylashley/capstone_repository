<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Models\Project;
use App\Models\ProjectFile;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource (API JSON listing).
     */
    public function index(Request $request)
    {
        $query = Project::query()->where('status', 'published');

        if ($request->filled('title')) {
            $query->where('title', 'like', '%'.$request->query('title').'%');
        }

        if ($request->filled('year')) {
            $query->where('year', $request->query('year'));
        }

        if ($request->filled('adviser')) {
            $query->whereHas('adviser', function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->query('adviser').'%');
            });
        }

        if ($request->filled('author')) {
            $query->whereHas('authors', function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->query('author').'%');
            });
        }

        if ($request->filled('keyword')) {
            $keyword = $request->query('keyword');
            $query->whereJsonContains('keywords', $keyword);
        }

        $projects = $query->with(['authors','adviser','files'])->paginate(15);

        return response()->json($projects);
    }

    /**
     * Return a blade page with published projects for browsing (web UI)
     */
    public function indexPage(Request $request)
    {
        $query = Project::with('adviser');

        // Administrators see the full lifecycle (pending, approved, published)
        // Students and Guests only see 'published' records
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            $query->where('status', 'published');
        }

        // Search by title
        if ($request->filled('title')) {
            $query->where('title', 'like', '%'.$request->query('title').'%');
        }

        // Search by year
        if ($request->filled('year')) {
            $query->where('year', $request->query('year'));
        }

        // Filter by Program (Mapping BSIT to BSInT, and BSCS to Com-Sci based on DB schema)
        if ($request->filled('program')) {
            $program = $request->query('program');
            if ($program === 'BSIT') {
                $query->where('program', 'BSInT');
            } elseif ($program === 'BSCS') {
                $query->where('program', 'Com-Sci');
            } else {
                $query->where('program', $program);
            }
        }

        // Search by category/specialization
        if ($request->filled('specialization')) {
            $query->where('specialization', $request->query('specialization'));
        }

        // Intelligent Multi-Term Search
        if ($request->filled('keyword')) {
            $rawKeyword = trim($request->query('keyword'));
            
            // Extract exact matches grouped by quotes or just spaces (e.g. "Mobile Apps" Android)
            preg_match_all('/"(?:\\\\.|[^\\\\"])*"|\S+/', $rawKeyword, $matches);
            $terms = array_map(function($term) {
                return trim($term, '"\'');
            }, $matches[0] ?? []);
            
            // Require ALL terms to be present SOMEWHERE in the project
            $query->where(function ($q) use ($terms) {
                foreach ($terms as $term) {
                    $q->where(function ($subQ) use ($term) {
                        $subQ->where('title', 'like', '%'.$term.'%')
                            ->orWhere('abstract', 'like', '%'.$term.'%')
                            ->orWhere('year', 'like', '%'.$term.'%')
                            ->orWhere('authors_list', 'like', '%'.$term.'%')
                            ->orWhere('adviser_name', 'like', '%'.$term.'%')
                            ->orWhereHas('authors', function ($authorQ) use ($term) {
                                $authorQ->where('name', 'like', '%'.$term.'%');
                            })
                            ->orWhereHas('adviser', function ($adviserQ) use ($term) {
                                $adviserQ->where('name', 'like', '%'.$term.'%');
                            })
                            ->orWhere('keywords', 'like', '%'.$term.'%')
                            ->orWhere('full_text', 'like', '%'.$term.'%');
                    });
                }
            });
        }

        $projects = $query->orderBy('year', 'desc')->paginate(10)->withQueryString();

        // Get all distinct years from the database for the filter dropdown
        $years = Project::select('year')->distinct()->orderBy('year', 'desc')->pluck('year');
        
        return view('projects.index', compact('projects', 'years'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $deadlineStr = \App\Models\Setting::get('submission_deadline');
        $isPastDeadline = $deadlineStr && \Carbon\Carbon::now()->greaterThan(\Carbon\Carbon::parse($deadlineStr));
        
        if (auth()->user()->isStudent() && (\App\Models\Setting::get('submissions_open', '1') == '0' || $isPastDeadline)) {
            return redirect()->route('student.home')->withErrors(['submissions' => 'Submissions are currently closed or the deadline has passed.']);
        }

        if (auth()->user()->isStudent() && auth()->user()->authoredProjects()->exists()) {
            return redirect()->route('student.home')->withErrors(['submissions' => 'You have already submitted a project. Students are limited to one project submission.']);
        }

        $advisers = \App\Models\User::where('role', \App\Models\User::ROLE_ADVISER)->active()->get();
        
        // Get all active students
        $students = \App\Models\User::where('role', \App\Models\User::ROLE_STUDENT)->active()->get();

        // If current user is a student, ensure they are in the list regardless of active scope (for robustness)
        if (auth()->user()->isStudent() && !$students->contains(auth()->user())) {
            $students->push(auth()->user());
        }

        // Fetch dynamic categories
        $categories = \App\Models\Category::all();

        return view('projects.create', compact('advisers', 'students', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        $deadlineStr = \App\Models\Setting::get('submission_deadline');
        $isPastDeadline = $deadlineStr && \Carbon\Carbon::now()->greaterThan(\Carbon\Carbon::parse($deadlineStr));

        if (auth()->user()->isStudent() && auth()->user()->authoredProjects()->exists()) {
            return redirect()->route('student.home')->withErrors(['submissions' => 'You have already submitted a project. Students are limited to one project submission.']);
        }

        \Log::debug('ProjectController@store called', ['user' => $request->user() ? $request->user()->toArray() : null, 'input_keys' => array_keys($request->all())]);

        $data = $request->validated();

        // Generate slug if not provided and ensure uniqueness
        $slug = $data['slug'] ?? Str::slug($data['title']);
        $originalSlug = $slug;
        $i = 1;
        while (Project::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $i++;
        }

        $project = Project::create([
            'title' => $data['title'],
            'slug' => $slug,
            'abstract' => $data['abstract'] ?? null,
            'year' => $data['year'],
            'adviser_id' => $data['adviser_id'],
            'status' => 'pending',
            'program' => $data['program'] ?? 'CSIT',
            'specialization' => $data['specialization'] ?? null,
            'authors_list' => implode(', ', array_map('trim', $data['authors'])),
        ]);

        // Attach ONLY the submitting author so they retain ownership to view/edit their submission
        $project->authors()->attach($request->user()->id, ['author_order' => 0]);

        // Store manuscript (PDF-only enforced by request)
        if ($request->hasFile('manuscript')) {
            $file = $request->file('manuscript');
            
            if (!$file->isValid() || !$file->getRealPath() || !file_exists($file->getRealPath())) {
                $project->delete(); // Rollback project creation
                return redirect()->back()->withInput()
                    ->withErrors(['manuscript' => 'Upload interrupted: The server\'s exact OS-level antivirus dynamically intercepted and deleted this file immediately before the backend engine could process it!']);
            }

            $year = $project->year ?: 'unknown';
            $dir = "projects/{$year}/{$project->slug}";
            $filename = 'manuscript.pdf';
            
            try {
                $path = $file->storeAs($dir, $filename, 'public');
            } catch (\Throwable $e) {
                $project->delete();
                return redirect()->back()->withInput()
                    ->withErrors(['manuscript' => 'Upload interrupted by OS Firewall: The host operating system locked or deleted the file because it matches a severe virus signature (e.g. EICAR).']);
            }

            $fullPath = Storage::disk('public')->path($path);
            $checksum = hash_file('sha256', $fullPath);
            $fileHash = hash_file('sha256', $fullPath); // Calculate content hash for duplicate detection

            // Check for duplicate content across ALL projects
            $duplicateFile = \App\Models\ProjectFile::where('file_hash', $fileHash)
                ->where('type', 'manuscript')
                ->with('project')
                ->first();

            if ($duplicateFile) {
                // Delete the just-uploaded file since it's a duplicate
                Storage::disk('public')->delete($path);
                
                // CRITICAL FIX: The project was already created in database, we MUST delete it now!
                // Since this happened inside the same request, we can safely force delete it.
                $project->delete(); // This cascades to authors pivot table automatically
                
                $duplicateProject = $duplicateFile->project;
                $errorMessage = "⚠️ Duplicate Content Detected!\n\n";
                $errorMessage .= "This PDF has identical content to a manuscript already submitted in:\n";
                $errorMessage .= "• Project: \"{$duplicateProject->title}\"\n";
                $errorMessage .= "• Submitted by: " . $duplicateProject->authors->pluck('name')->join(', ') . "\n";
                $errorMessage .= "• Year: {$duplicateProject->year}\n\n";
                $errorMessage .= "Please ensure you are submitting original work. If you believe this is an error, contact your adviser.";

                if (request()->wantsJson()) {
                    return response()->json(['error' => $errorMessage], 422);
                }

                return redirect()->back()->withInput()
                    ->withErrors(['manuscript' => $errorMessage]);
            }

            ProjectFile::create([
                'project_id' => $project->id,
                'type' => 'manuscript',
                'filename' => $filename,
                'path' => $path,
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'checksum' => $checksum,
                'file_hash' => $fileHash, // Store the hash for future duplicate checks
                'uploaded_by' => $request->user()->id,
                'is_primary' => true,
            ]);

            // Optional: run file scanner (ClamAV)
            $scanOk = true;
            $scanNotes = [];
            $scanner = app(\App\Services\FileScanner::class);
            $scanResult = $scanner->scan($fullPath);

            if (! $scanResult['ok']) {
                \App\Models\ActivityLog::create([
                    'user_id' => $request->user()->id,
                    'action' => 'manuscript_scan_failed',
                    'target_type' => 'project',
                    'target_id' => $project->id,
                    'ip' => $request->ip(),
                    'meta' => ['notes' => $scanResult['notes']],
                ]);

                // Cleanup stored file and DB records to block submission
                Storage::disk('public')->delete($path);
                \App\Models\ProjectFile::where('project_id', $project->id)->delete();
                $project->authors()->detach();
                $project->delete();

                $payload = ['message' => 'File scan failed: upload blocked', 'notes' => $scanResult['notes']];

                if ($request->expectsJson() || $request->wantsJson()) {
                    return response()->json($payload, 422);
                }

                return redirect()->back()->withInput()
                    ->withErrors(['manuscript' => 'File scan failed: upload blocked. Please contact support or try again later.']);
            }

            // Run PDF validation heuristics (page count, keywords)
            $validator = app(\App\Services\PDFValidator::class);
            $validation = $validator->validate($fullPath);

            // Determine specific validation message based on what failed
            $validatorMessage = 'Initial criteria met';
            if (!$validation['valid']) {
                if ($validation['page_count_failed'] && $validation['keywords_missing']) {
                    $validatorMessage = 'Critical issues detected';
                } elseif ($validation['keywords_missing']) {
                    $validatorMessage = 'Missing required elements';
                } elseif ($validation['page_count_failed']) {
                    $validatorMessage = 'Needs adviser verification';
                } else {
                    $validatorMessage = 'Warning: Manual review required';
                }
            }

            $combinedNotes = [
                '✓ Security Scan: Clean (No threats detected)',
                '✓ Validator: ' . $validatorMessage
            ];
            $combinedNotes = array_merge($combinedNotes, $validation['notes']);

            $project->manuscript_validated = $validation['valid'];
            $project->manuscript_validation_notes = implode("\n", $combinedNotes);
            if (isset($validation['text']) && !empty($validation['text'])) {
                $project->full_text = $validation['text'];
            }
            $project->save();

            // STRICT VERIFICATION: If validation failed, reject the submission entirely
            if (! $validation['valid']) {
                \App\Models\ActivityLog::create([
                    'user_id' => $request->user()->id,
                    'action' => 'manuscript_verification_failed',
                    'target_type' => 'project',
                    'target_id' => $project->id, // Note: ID will be of deleted record, but logs text
                    'ip' => $request->ip(),
                    'meta' => ['notes' => $validation['notes']],
                ]);

                // Cleanup: Delete file and project record
                Storage::disk('public')->delete($path);
                \App\Models\ProjectFile::where('project_id', $project->id)->delete();
                $project->authors()->detach();
                $project->delete();

                $errorMsg = "System Verification Report Failed:\n" . implode("\n", $validation['notes']);
                
                if ($request->expectsJson() || $request->wantsJson()) {
                    return response()->json(['message' => 'Verification failed', 'errors' => ['manuscript' => [$errorMsg]]], 422);
                }

                return redirect()->back()->withInput()
                    ->withErrors(['manuscript' => $errorMsg]);
            }

            \App\Models\ActivityLog::create([
                'user_id' => $request->user()->id,
                'action' => 'manuscript_validated',
                'target_type' => 'project',
                'target_id' => $project->id,
                'ip' => $request->ip(),
                'meta' => ['valid' => $project->manuscript_validated, 'notes' => $combinedNotes, 'scan_ok' => $scanOk],
            ]);



        }

        // Optional attachments (with security scanning)
        if ($request->has('attachments')) {
            $scanner = app(\App\Services\FileScanner::class);

            foreach ($request->file('attachments') as $attach) {
                if (!$attach->isValid() || !$attach->getRealPath() || !file_exists($attach->getRealPath())) {
                    // Rollback
                    foreach ($project->files as $existingFile) {
                        Storage::disk('public')->delete($existingFile->path);
                    }
                    $project->files()->delete();
                    $project->authors()->detach();
                    $project->delete();
                    return redirect()->back()->withInput()
                        ->withErrors(['attachments' => "Upload interrupted: The server's OS-level antivirus intercepted and shredded \"{$attach->getClientOriginalName()}\" immediately before the server could process it!"]);
                }

                try {
                    $attPath = $attach->storeAs($dir, $attach->getClientOriginalName(), 'public');
                } catch (\Throwable $e) {
                    foreach ($project->files as $existingFile) {
                        Storage::disk('public')->delete($existingFile->path);
                    }
                    $project->files()->delete();
                    $project->authors()->detach();
                    $project->delete();
                    return redirect()->back()->withInput()
                        ->withErrors(['attachments' => "OS Firewall Interception: The host operating system quarantined and locked \"{$attach->getClientOriginalName()}\" during transfer. Threat blocked."]);
                }
                $fullAttPath = Storage::disk('public')->path($attPath);

                // Security scan each attachment
                $attachScanResult = $scanner->scan($fullAttPath);

                if (! $attachScanResult['ok']) {
                    // Remove the flagged file
                    Storage::disk('public')->delete($attPath);

                    ActivityLog::create([
                        'user_id' => $request->user()->id,
                        'action' => 'attachment_scan_failed',
                        'target_type' => 'project',
                        'target_id' => $project->id,
                        'ip' => $request->ip(),
                        'meta' => [
                            'filename' => $attach->getClientOriginalName(),
                            'notes' => $attachScanResult['notes'],
                        ],
                    ]);

                    // Clean up entire submission (project + all files uploaded so far)
                    foreach ($project->files as $existingFile) {
                        Storage::disk('public')->delete($existingFile->path);
                    }
                    $project->files()->delete();
                    $project->authors()->detach();
                    $project->delete();

                    $errorMsg = "⚠️ Security Threat Detected in Attachment!\n\n";
                    $errorMsg .= "File: \"{$attach->getClientOriginalName()}\"\n";
                    $errorMsg .= "Reason: {$attachScanResult['notes']}\n\n";
                    $errorMsg .= "The entire submission has been blocked for your safety. Please remove the flagged file and try again.";

                    if ($request->expectsJson() || $request->wantsJson()) {
                        return response()->json(['message' => 'Attachment scan failed', 'error' => $errorMsg], 422);
                    }

                    return redirect()->back()->withInput()
                        ->withErrors(['attachments' => $errorMsg]);
                }

                $checksum = hash_file('sha256', $fullAttPath);
                $fileHash = hash_file('sha256', $fullAttPath);

                ProjectFile::create([
                    'project_id' => $project->id,
                    'type' => 'attachment',
                    'filename' => $attach->getClientOriginalName(),
                    'path' => $attPath,
                    'mime_type' => $attach->getClientMimeType(),
                    'size' => $attach->getSize(),
                    'checksum' => $checksum,
                    'file_hash' => $fileHash,
                    'uploaded_by' => $request->user()->id,
                ]);
            }
        }

        // Log activity
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'upload_project',
            'target_type' => 'project',
            'target_id' => $project->id,
            'ip' => $request->ip(),
            'meta' => ['title' => $project->title, 'slug' => $project->slug],
        ]);

        // Send email notification to adviser
        try {
            if ($project->adviser) {
                \Illuminate\Support\Facades\Mail::to($project->adviser)->queue(new \App\Mail\NewSubmission($project));
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send new submission email: ' . $e->getMessage());
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Project submitted and pending adviser verification', 'project_id' => $project->id], 201);
        }

        return redirect()->route('student.home')->with('success', 'Project submitted successfully and is pending adviser verification.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $project = Project::with(['authors','adviser','files','verification'])->findOrFail($id);

        // Access rules: published projects are public; unpublished require authorization
        if ($project->status !== 'published') {
            $user = auth()->user();
            if (! $user || (! $user->isAdmin() && ! $user->isAdviser() && ! $project->authors->contains($user))) {
                abort(403);
            }
        }

        // If request expects JSON, return JSON
        if (request()->wantsJson()) {
            return response()->json($project);
        }

        return view('projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified resource (student editing pending/rejected).
     */
    public function edit(string $id)
    {
        $project = Project::with(['authors', 'adviser', 'files'])->findOrFail($id);
        $user = auth()->user();

        // Only authors can edit
        if (! $project->authors->contains($user)) {
            abort(403, 'You are not authorized to edit this project.');
        }

        // Only pending or rejected projects can be edited
        if (! in_array($project->status, ['pending', 'rejected'])) {
            return redirect()->route('projects.show', $project)
                ->with('error', 'This project can no longer be edited because it has been ' . $project->status . '.');
        }

        $advisers = \App\Models\User::where('role', \App\Models\User::ROLE_ADVISER)->active()->get();
        $categories = \App\Models\Category::all();

        return view('projects.edit', compact('project', 'advisers', 'categories'));
    }

    /**
     * Update the specified resource in storage (student updating pending/rejected).
     */
    public function update(Request $request, string $id)
    {
        $project = Project::with(['authors', 'adviser'])->findOrFail($id);
        $user = $request->user();

        // Only authors can update
        if (! $project->authors->contains($user)) {
            abort(403, 'You are not authorized to update this project.');
        }

        // Only pending or rejected projects can be updated
        if (! in_array($project->status, ['pending', 'rejected'])) {
            return redirect()->route('projects.show', $project)
                ->with('error', 'This project can no longer be edited.');
        }

        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('projects')->ignore($project->id)->where(function ($query) {
                    return $query->where('status', '!=', 'rejected');
                })
            ],
            'abstract' => 'required|string',
            'year' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'specialization' => 'required|string',
            'program' => ['required', 'string', Rule::in(['BSInT', 'Com-Sci'])],
            'adviser_id' => 'required|exists:users,id',
            'keywords' => 'nullable|string',
            'authors' => 'required|array|min:1',
            'authors.*' => 'required|string|max:255',
        ], [
            'title.unique' => 'A project with this exact title already exists in the system. Please revise your title.',
        ]);

        // Parse keywords from comma-separated string
        $keywords = null;
        if (!empty($validated['keywords'])) {
            $keywords = array_map('trim', explode(',', $validated['keywords']));
            $keywords = array_filter($keywords);
            $keywords = array_values($keywords);
        }

        $wasRejected = $project->status === 'rejected';

        $project->update([
            'title' => $validated['title'],
            'abstract' => $validated['abstract'],
            'year' => $validated['year'],
            'specialization' => $validated['specialization'],
            'program' => $validated['program'],
            'adviser_id' => $validated['adviser_id'],
            'keywords' => $keywords,
            'authors_list' => implode(', ', array_map('trim', $validated['authors'])),
            'status' => 'pending', // Reset to pending for re-review
            'rejection_reason' => null, // Clear previous feedback
        ]);

        // Log the edit
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => $wasRejected ? 'project_resubmitted' : 'project_edited',
            'target_type' => 'project',
            'target_id' => $project->id,
            'ip' => $request->ip(),
            'meta' => [
                'title' => $project->title,
                'was_rejected' => $wasRejected,
            ],
        ]);

        // Notify adviser about the resubmission
        if ($wasRejected && $project->adviser) {
            try {
                \Illuminate\Support\Facades\Mail::to($project->adviser)->queue(new \App\Mail\NewSubmission($project));
            } catch (\Exception $e) {
                \Log::error('Failed to send resubmission email: ' . $e->getMessage());
            }
        }

        $message = $wasRejected
            ? 'Project updated and resubmitted for adviser review.'
            : 'Project updated successfully.';

        return redirect()->route('student.home')->with('success', $message);
    }

    /**
     * Allow specific authors (students) to cancel their pending project submission.
     */
    public function cancel(Request $request, Project $project)
    {
        $user = $request->user();

        // Check if user is an author
        if (! $project->authors->contains($user)) {
             abort(403, 'Unauthorized action.');
        }

        // Can only cancel if pending or rejected (not yet approved/archived)
        if (!in_array($project->status, ['pending', 'rejected'])) {
             return redirect()->back()->with('error', 'Cannot cancel submission. Project is already ' . $project->status . '.');
        }

        // Delete files
        foreach ($project->files as $file) {
             Storage::disk('public')->delete($file->path);
        }
        $project->files()->delete();

        // Detach authors
        $project->authors()->detach();

        // Delete project
        $project->delete();

        return redirect()->route('student.home')->with('success', 'Submission cancelled successfully.');
    }

    /**
     * Abort an in-progress submission (called via AJAX from the loading modal).
     * Cleans up any project created by this user in the last 5 minutes that is still pending.
     */
    public function abortSubmission(Request $request)
    {
        $user = $request->user();

        // Find the user's most recent pending project created in the last 5 minutes
        $recentProject = Project::whereHas('authors', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            })
            ->where('status', 'pending')
            ->where('created_at', '>=', now()->subMinutes(5))
            ->orderByDesc('created_at')
            ->first();

        if ($recentProject) {
            // Delete files from storage
            foreach ($recentProject->files as $file) {
                Storage::disk('public')->delete($file->path);
            }
            $recentProject->files()->delete();
            $recentProject->authors()->detach();
            $recentProject->delete();

            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'submission_aborted',
                'target_type' => 'project',
                'target_id' => 0,
                'ip' => $request->ip(),
                'meta' => ['title' => $recentProject->title],
            ]);

            return response()->json(['cleaned' => true, 'message' => 'Submission aborted and cleaned up.']);
        }

        return response()->json(['cleaned' => false, 'message' => 'No recent submission found to clean up.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
