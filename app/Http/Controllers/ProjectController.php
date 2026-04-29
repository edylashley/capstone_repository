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
            $query->where('title', 'like', '%' . $request->query('title') . '%');
        }

        if ($request->filled('year')) {
            $query->where('year', $request->query('year'));
        }

        if ($request->filled('adviser')) {
            $query->whereHas('adviser', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->query('adviser') . '%');
            });
        }

        if ($request->filled('author')) {
            $query->whereHas('authors', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->query('author') . '%');
            });
        }

        if ($request->filled('keyword')) {
            $keyword = $request->query('keyword');
            $query->whereJsonContains('keywords', $keyword);
        }

        $projects = $query->with(['authors', 'adviser', 'files'])->paginate(15);

        return response()->json($projects);
    }

    /**
     * Return a blade page with published projects for browsing (web UI)
     */
    public function indexPage(Request $request)
    {
        $query = Project::with('adviser');

        // Administrators see the full lifecycle (pending, approved, published) EXCEPT archived
        // Students and Guests only see 'published' records
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            $query->where('status', 'published');
        } else {
            $query->where('status', '!=', 'archived');
        }

        // Search by title
        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->query('title') . '%');
        }

        // Search by year
        if ($request->filled('year')) {
            $query->where('year', $request->query('year'));
        }

        // Filter by Program (Mapping BSInT and Com-Sci based on DB schema)
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

        // Search by category (Dropdown + Text Search)
        if ($request->filled('specialization') || $request->filled('category_text')) {
            $catDropdown = $request->query('specialization');
            $catText = $request->query('category_text');

            $query->where(function ($q) use ($catDropdown, $catText) {
                if ($catDropdown) {
                    $q->whereHas('categories', function ($sub) use ($catDropdown) {
                        $sub->where('name', $catDropdown);
                    })->orWhere('custom_category', 'like', '%' . $catDropdown . '%');
                }

                if ($catText) {
                    $q->whereHas('categories', function ($sub) use ($catText) {
                        $sub->where('name', 'like', '%' . $catText . '%');
                    })->orWhere('custom_category', 'like', '%' . $catText . '%');
                }
            });
        }

        // Intelligent Multi-Term Search
        if ($request->filled('keyword')) {
            $rawKeyword = trim($request->query('keyword'));

            // Extract exact matches grouped by quotes or just spaces (e.g. "Mobile Apps" Android)
            preg_match_all('/"(?:\\\\.|[^\\\\"])*"|\S+/', $rawKeyword, $matches);
            $terms = array_map(function ($term) {
                return trim($term, '"\'');
            }, $matches[0] ?? []);

            // Require ALL terms to be present SOMEWHERE in the project
            $query->where(function ($q) use ($terms) {
                foreach ($terms as $term) {
                    $q->where(function ($subQ) use ($term) {
                        $subQ->where('title', 'like', '%' . $term . '%')
                            ->orWhere('abstract', 'like', '%' . $term . '%')
                            ->orWhere('year', 'like', '%' . $term . '%')
                            ->orWhere('authors_list', 'like', '%' . $term . '%')
                            ->orWhere('adviser_name', 'like', '%' . $term . '%')
                            ->orWhereHas('authors', function ($authorQ) use ($term) {
                                $authorQ->where('name', 'like', '%' . $term . '%');
                            })
                            ->orWhereHas('adviser', function ($adviserQ) use ($term) {
                                $adviserQ->where('name', 'like', '%' . $term . '%');
                            })
                            ->orWhere('keywords', 'like', '%' . $term . '%')
                            ->orWhere('full_text', 'like', '%' . $term . '%');
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

        // Get all active students for author selection
        $students = \App\Models\User::where('role', \App\Models\User::ROLE_STUDENT)->active()->get();

        if (auth()->user()->isStudent() && !$students->contains(auth()->user())) {
            $students->push(auth()->user());
        }

        $categories = \App\Models\Category::all();
        $programs = \App\Models\Program::all();

        return view('projects.create', compact('students', 'categories', 'programs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        $deadlineStr = \App\Models\Setting::get('submission_deadline');
        $isPastDeadline = $deadlineStr && \Carbon\Carbon::now()->greaterThan(\Carbon\Carbon::parse($deadlineStr));

        $data = $request->validated();

        // Generate slug if not provided and ensure uniqueness
        $slug = $data['slug'] ?? \Illuminate\Support\Str::slug($data['title']);
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
            'adviser_name' => $data['adviser_name'],
            'adviser_id' => null, // Explicitly null as we are moving away from adviser roles
            'status' => 'pending',
            'program' => $data['program'] ?? 'CSIT',
            'specialization' => $request->other_category ?? null,
            'custom_category' => $request->other_category ?? null,
            'authors_list' => implode(', ', array_map('trim', $data['authors'])),
        ]);

        // Sync multiple categories
        $categoryIds = $request->categories ?? [];
        $project->categories()->sync($categoryIds);

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

            // Check for duplicate content across ACTIVE projects
            $duplicateFile = \App\Models\ProjectFile::where('file_hash', $fileHash)
                ->where('type', 'manuscript')
                ->whereHas('project', function ($query) {
                    // This automatically filters out soft-deleted projects
                    $query->whereNull('deleted_at');
                })
                ->with('project')
                ->first();

            if ($duplicateFile) {
                // Delete the just-uploaded file since it's a duplicate
                Storage::disk('public')->delete($path);

                // CRITICAL FIX: The project was already created in database, we MUST delete it now!
                // Since this happened inside the same request, we can safely force delete it.
                $project->authors()->detach();
                $project->forceDelete();

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

            if (!$scanResult['ok']) {
                $isSystemError = (isset($scanResult['error_type']) && $scanResult['error_type'] === 'system');

                // 1. Log the failure
                \App\Models\ActivityLog::create([
                    'user_id' => $request->user()->id,
                    'action' => $isSystemError ? 'scanner_system_error' : 'security_threat_blocked',
                    'target_type' => 'project',
                    'target_id' => $project->id,
                    'ip' => $request->ip(),
                    'meta' => [
                        'notes' => $scanResult['notes'],
                        'filename' => $file->getClientOriginalName()
                    ],
                ]);

                // 2. Create an automatic High-Priority Security Ticket for Admin
                if (!$isSystemError) {
                    \App\Models\SupportTicket::create([
                        'user_id' => $request->user()->id,
                        'email' => $request->user()->email,
                        'category' => 'security',
                        'subject' => '⚠️ SECURITY ALERT: Malicious Upload Blocked',
                        'message' => "AUTOMATED SYSTEM ALERT:\n\n" .
                            "Student: " . $request->user()->name . " (ID: " . $request->user()->id . ")\n" .
                            "Action: Attempted to upload a file with a security threat.\n" .
                            "File: " . $file->getClientOriginalName() . "\n" .
                            "Threat Detected: " . $scanResult['notes'] . "\n\n" .
                            "The submission has been blocked and the temporary file has been shredded.",
                        'status' => 'resolved',
                    ]);
                }

                // Cleanup stored file and DB records to block submission
                Storage::disk('public')->delete($path);
                \App\Models\ProjectFile::where('project_id', $project->id)->delete();
                $project->authors()->detach();
                $project->forceDelete();

                $errorMsg = "⚠️ Security Threat Detected in Manuscript!\n\n";
                $errorMsg .= "The file \"{$file->getClientOriginalName()}\" contains a restricted signature: " . str_replace('FOUND', '', $scanResult['notes']) . "\n\n";
                $errorMsg .= "Your submission has been blocked for security reasons. Please ensure your files are clean and try again.";

                if ($isSystemError) {
                    $errorMsg = "⚠️ Scanner Service Error!\n\n";
                    $errorMsg .= "The system encountered an error while scanning the manuscript. Please contact the technical administrator.";
                }

                $payload = ['message' => 'File scan failed', 'notes' => $scanResult['notes'], 'error' => $errorMsg];

                if ($request->expectsJson() || $request->wantsJson()) {
                    return response()->json($payload, 422);
                }

                return redirect()->back()->withInput()
                    ->withErrors(['manuscript' => $errorMsg]);
            }

            // Run PDF validation heuristics (page count, keywords)
            $validator = app(\App\Services\PDFValidator::class);
            $validation = $validator->validate($fullPath);

            // Determine specific validation message based on what failed
            $validatorMessage = 'Initial criteria met';
            if (!$validation['valid']) {
                if ($validation['keywords_missing']) {
                    $validatorMessage = 'Missing required elements';
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

            // 4. Run PDF validation heuristics (LOG ONLY, NO BLOCKING per user request)
            $validator = app(\App\Services\PDFValidator::class);
            $validation = $validator->validate($fullPath);

            $combinedNotes = [
                '[OK] Security Scan: Clean (No threats detected)',
            ];
            $combinedNotes = array_merge($combinedNotes, $validation['notes']);

            $project->update([
                'manuscript_validated' => $validation['valid'],
                'manuscript_validation_notes' => implode("\n", $combinedNotes),
            ]);

            \App\Models\ActivityLog::create([
                'user_id' => $request->user()->id,
                'action' => 'manuscript_verified',
                'target_type' => 'project',
                'target_id' => $project->id,
                'ip' => $request->ip(),
                'meta' => ['valid' => $validation['valid'], 'notes' => $combinedNotes],
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
                    $project->forceDelete();
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
                    $project->forceDelete();
                    return redirect()->back()->withInput()
                        ->withErrors(['attachments' => "OS Firewall Interception: The host operating system quarantined and locked \"{$attach->getClientOriginalName()}\" during transfer. Threat blocked."]);
                }
                $fullAttPath = Storage::disk('public')->path($attPath);

                // Security scan each attachment
                $attachScanResult = $scanner->scan($fullAttPath);

                if (!$attachScanResult['ok']) {
                    $isSystemError = (isset($attachScanResult['error_type']) && $attachScanResult['error_type'] === 'system');

                    // 1. Log the failure
                    ActivityLog::create([
                        'user_id' => $request->user()->id,
                        'action' => $isSystemError ? 'attachment_scanner_error' : 'attachment_threat_blocked',
                        'target_type' => 'project',
                        'target_id' => $project->id,
                        'ip' => $request->ip(),
                        'meta' => [
                            'filename' => $attach->getClientOriginalName(),
                            'notes' => $attachScanResult['notes'],
                        ],
                    ]);

                    // 2. Create an automatic High-Priority Security Ticket for Admin
                    if (!$isSystemError) {
                        \App\Models\SupportTicket::create([
                            'user_id' => $request->user()->id,
                            'email' => $request->user()->email,
                            'category' => 'security',
                            'subject' => '⚠️ SECURITY ALERT: Malicious Attachment Blocked',
                            'message' => "AUTOMATED SYSTEM ALERT:\n\n" .
                                "Student: " . $request->user()->name . " (ID: " . $request->user()->id . ")\n" .
                                "Action: Attempted to upload an attachment with a security threat.\n" .
                                "File: " . $attach->getClientOriginalName() . "\n" .
                                "Threat Detected: " . $attachScanResult['notes'] . "\n\n" .
                                "The entire submission has been blocked and the temporary files have been shredded.",
                            'status' => 'resolved',
                        ]);
                    }

                    // Remove the flagged file
                    Storage::disk('public')->delete($attPath);

                    // Clean up entire submission
                    foreach ($project->files as $existingFile) {
                        Storage::disk('public')->delete($existingFile->path);
                    }
                    $project->files()->delete();
                    $project->authors()->detach();
                    $project->forceDelete();

                    $errorMsg = "⚠️ Security Threat Detected in Attachment!\n\n";
                    if ($isSystemError) {
                        $errorMsg = "⚠️ Scanner Service Error!\n\n";
                        $errorMsg .= "The security scanner encountered a system error while processing: {$attach->getClientOriginalName()}\n\n";
                        $errorMsg .= "Please contact the technical administrator for assistance.";
                    } else {
                        $errorMsg .= "File: \"{$attach->getClientOriginalName()}\"\n";
                        $errorMsg .= "Threat Signature: " . str_replace('FOUND', '', $attachScanResult['notes']) . "\n\n";
                        $errorMsg .= "CRITICAL: The entire submission has been blocked for your safety. To proceed, please remove the flagged file and re-upload only clean files.";
                    }

                    if ($request->expectsJson() || $request->wantsJson()) {
                        return response()->json(['message' => 'Attachment scan failed', 'error' => $errorMsg], 422);
                    }

                    return redirect()->back()->withInput()
                        ->withErrors(['attachments' => $errorMsg]);
                }

                if ($attPath) {
                    try {
                        $fullAttPath = Storage::disk('public')->path($attPath);
                        $checksum = @hash_file('sha256', $fullAttPath) ?: null;
                        $fileHash = $checksum;

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
                    } catch (\Exception $e) {
                        \Log::error("Attachment processing failed for project {$project->id}: " . $e->getMessage());
                    }
                }
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

        // Send email notification to Admin
        try {
            $admin = \App\Models\User::where('role', 'admin')->first();
            if ($admin) {
                \Illuminate\Support\Facades\Mail::to($admin)->queue(new \App\Mail\NewSubmission($project));
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send new submission email to admin: ' . $e->getMessage());
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Project submitted and pending administrator review', 'project_id' => $project->id], 201);
        }

        return redirect()->route('student.home')->with('success', 'Project submitted successfully and is pending administrator review.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $project = Project::withTrashed()->with(['authors', 'adviser', 'files', 'verification'])->findOrFail($id);

        // Access rules: published projects are public; unpublished require authorization
        if ($project->status !== 'published') {
            $user = auth()->user();
            if (!$user || (!$user->isAdmin() && !$project->authors->contains($user))) {
                abort(403);
            }
        }

        // Log the view if user is logged in
        if (auth()->check()) {
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'project_viewed',
                'target_type' => 'project',
                'target_id' => $project->id,
                'ip' => request()->ip(),
                'meta' => [
                    'title' => $project->title,
                    'status' => $project->status
                ]
            ]);
        }

        // If request expects JSON, return JSON
        if (request()->wantsJson()) {
            return response()->json($project);
        }

        return view('projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified resource (student editing pending/returned).
     */
    public function edit(string $id)
    {
        $project = Project::with(['authors', 'adviser', 'files'])->findOrFail($id);
        $user = auth()->user();

        // Only authors can edit
        if (!$project->authors->contains($user)) {
            abort(403, 'You are not authorized to edit this project.');
        }

        // Only pending or returned projects can be edited
        if (!in_array($project->status, ['pending', 'returned'])) {
            return redirect()->route('projects.show', $project)
                ->with('error', 'This project can no longer be edited because it has been ' . $project->status . '.');
        }

        $categories = \App\Models\Category::all();
        $programs = \App\Models\Program::all();

        return view('projects.edit', compact('project', 'categories', 'programs'));
    }

    /**
     * Update the specified resource in storage (student updating pending/returned).
     */
    public function update(Request $request, string $id)
    {
        $project = Project::with(['authors', 'adviser'])->findOrFail($id);
        $user = $request->user();

        // Only authors can update
        if (!$project->authors->contains($user)) {
            abort(403, 'You are not authorized to update this project.');
        }

        // Only pending or returned projects can be updated
        if (!in_array($project->status, ['pending', 'returned'])) {
            return redirect()->route('projects.show', $project)
                ->with('error', 'This project can no longer be edited.');
        }

        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('projects')->ignore($project->id)->where(function ($query) {
                    return $query->where('status', '!=', 'returned');
                })
            ],
            'abstract' => 'required|string',
            'year' => 'required|integer',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'other_category' => ['nullable', 'string', 'max:50'],
            'program' => [
                'required',
                'string',
                auth()->user()->isAdmin()
                ? Rule::in(\App\Models\Program::pluck('abbreviation')->toArray())
                : Rule::in([auth()->user()->program])
            ],
            'adviser_name' => 'required|string|max:255',
            'keywords' => 'nullable|string',
            'authors' => 'required|array|min:1',
            'authors.*' => 'required|string|max:255',
            'manuscript' => 'nullable|file|mimes:pdf|max:51200', // Optional on edit
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

        $wasReturned = $project->status === 'returned';

        $project->update([
            'title' => $validated['title'],
            'abstract' => $validated['abstract'],
            'year' => $validated['year'],
            'program' => $validated['program'],
            'adviser_name' => $validated['adviser_name'],
            'keywords' => $keywords,
            'custom_category' => $validated['other_category'] ?? null,
            'authors_list' => implode(', ', array_map('trim', $validated['authors'])),
            'status' => 'pending', // Reset to pending for re-review
            'rejection_reason' => null, // Clear previous feedback
        ]);

        // Handle Manuscript Replacement
        if ($request->hasFile('manuscript')) {
            $file = $request->file('manuscript');

            // 1. Delete old manuscript
            $oldManuscripts = $project->files()->where('type', 'manuscript')->get();
            foreach ($oldManuscripts as $old) {
                Storage::disk('public')->delete($old->path);
                $old->delete();
            }

            // 2. Store new file
            $year = $project->year ?: 'unknown';
            $dir = "projects/{$year}/{$project->slug}";
            $filename = 'manuscript.pdf';
            $path = $file->storeAs($dir, $filename, 'public');

            $fullPath = Storage::disk('public')->path($path);
            $fileHash = hash_file('sha256', $fullPath);

            // 1.5 CHECK FOR DUPLICATE CONTENT
            $duplicateFile = \App\Models\ProjectFile::where('file_hash', $fileHash)
                ->where('type', 'manuscript')
                ->where('project_id', '!=', $project->id) // Don't match against itself if they re-upload the same file
                ->whereHas('project', function ($query) {
                    $query->whereNull('deleted_at');
                })
                ->with('project')
                ->first();

            if ($duplicateFile) {
                Storage::disk('public')->delete($path);

                $duplicateProject = $duplicateFile->project;
                $errorMessage = "⚠️ Duplicate Content Detected!\n\n";
                $errorMessage .= "This new PDF is identical to a manuscript already submitted in:\n";
                $errorMessage .= "• Project: \"{$duplicateProject->title}\"\n";
                $errorMessage .= "• Submitted by: " . $duplicateProject->authors->pluck('name')->join(', ') . "\n";
                $errorMessage .= "Please ensure you are submitting original work.";

                return redirect()->back()->withInput()->withErrors(['manuscript' => $errorMessage]);
            }

            $projectFile = \App\Models\ProjectFile::create([
                'project_id' => $project->id,
                'type' => 'manuscript',
                'filename' => $filename,
                'path' => $path,
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'file_hash' => $fileHash,
                'uploaded_by' => $user->id,
                'is_primary' => true,
            ]);

            // 3. Re-run scans and validation
            $scanner = app(\App\Services\FileScanner::class);
            $scanResult = $scanner->scan($fullPath);

            // STRICT SECURITY: Block if threat is detected
            if (!$scanResult['ok']) {
                $isSystemError = (isset($scanResult['error_type']) && $scanResult['error_type'] === 'system');

                // 1. Log the failure
                \App\Models\ActivityLog::create([
                    'user_id' => $user->id,
                    'action' => $isSystemError ? 'update_scanner_system_error' : 'update_security_threat_blocked',
                    'target_type' => 'project',
                    'target_id' => $project->id,
                    'ip' => $request->ip(),
                    'meta' => [
                        'notes' => $scanResult['notes'],
                        'filename' => $file->getClientOriginalName()
                    ],
                ]);

                // 2. Security Ticket for Admin
                if (!$isSystemError) {
                    \App\Models\SupportTicket::create([
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'category' => 'security',
                        'subject' => '⚠️ SECURITY ALERT: Malicious Update Blocked',
                        'message' => "AUTOMATED SYSTEM ALERT:\n\n" .
                            "Student: " . $user->name . " (ID: " . $user->id . ")\n" .
                            "Action: Attempted to UPDATE a project with a malicious file.\n" .
                            "File: " . $file->getClientOriginalName() . "\n" .
                            "Threat Detected: " . $scanResult['notes'] . "\n\n" .
                            "The update was blocked and the malicious file was shredded immediately.",
                        'status' => 'resolved',
                    ]);
                }

                // Cleanup ONLY the new bad file, keep the project record (returned state)
                Storage::disk('public')->delete($path);
                $projectFile->delete(); // Delete the file record we just created

                $errorMsg = "⚠️ Security Threat Detected!\n\n";
                $errorMsg .= "The file \"{$file->getClientOriginalName()}\" contains a restricted signature: " . str_replace('FOUND', '', $scanResult['notes']) . "\n\n";
                $errorMsg .= "Your update has been blocked for security reasons.";

                if ($request->expectsJson() || $request->wantsJson()) {
                    return response()->json(['message' => 'Security scan failed', 'errors' => ['manuscript' => [$errorMsg]]], 422);
                }

                return redirect()->back()->withInput()->withErrors(['manuscript' => $errorMsg]);
            }

            // 4. Run PDF validation heuristics (LOG ONLY, NO BLOCKING per user request)
            $validator = app(\App\Services\PDFValidator::class);
            $validation = $validator->validate($fullPath);

            $combinedNotes = [
                '[OK] Security Scan: Clean (No threats detected)',
            ];
            $combinedNotes = array_merge($combinedNotes, $validation['notes']);

            $project->update([
                'manuscript_validated' => $validation['valid'],
                'manuscript_validation_notes' => implode("\n", $combinedNotes),
            ]);

            // Log the resubmission validation
            \App\Models\ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'update_manuscript_verified',
                'target_type' => 'project',
                'target_id' => $project->id,
                'ip' => $request->ip(),
                'meta' => ['valid' => $validation['valid'], 'notes' => $combinedNotes],
            ]);
        }

        // Sync categories
        $categoryIds = $validated['categories'] ?? [];
        if ($request->filled('other_category')) {
            $newCat = \App\Models\Category::firstOrCreate([
                'name' => ucwords(strtolower(trim($request->other_category)))
            ]);
            if (!in_array($newCat->id, $categoryIds)) {
                $categoryIds[] = $newCat->id;
            }
        }
        $project->categories()->sync($categoryIds);

        // Log the edit
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => $wasReturned ? 'project_resubmitted' : 'project_edited',
            'target_type' => 'project',
            'target_id' => $project->id,
            'ip' => $request->ip(),
            'meta' => [
                'title' => $project->title,
                'was_returned' => $wasReturned,
            ],
        ]);

        // Handle Additional Attachments
        if ($request->hasFile('attachments')) {
            $scanner = app(\App\Services\FileScanner::class);
            $year = $project->year ?: 'unknown';
            $dir = "projects/{$year}/{$project->slug}/attachments";

            foreach ($request->file('attachments') as $file) {
                // 1. Temporary store for scanning
                $tempPath = $file->store('temp', 'public');
                $tempFullPath = Storage::disk('public')->path($tempPath);

                // 2. Scan
                $scanResult = $scanner->scan($tempFullPath);
                if (!$scanResult['ok']) {
                    Storage::disk('public')->delete($tempPath);
                    $errorMsg = "⚠️ Security Threat Detected in Attachment: \"{$file->getClientOriginalName()}\"!\n\n";
                    $errorMsg .= "The file contains a restricted signature. Update blocked.";
                    return redirect()->back()->withInput()->withErrors(['attachments' => $errorMsg]);
                }

                // 3. Move to permanent location
                $filename = $file->getClientOriginalName();
                // Ensure unique filename if needed
                if (Storage::disk('public')->exists("{$dir}/{$filename}")) {
                    $filename = time() . '_' . $filename;
                }
                $path = $file->storeAs($dir, $filename, 'public');
                $fullPath = Storage::disk('public')->path($path);
                $fileHash = hash_file('sha256', $fullPath);

                // 4. Create record
                \App\Models\ProjectFile::create([
                    'project_id' => $project->id,
                    'type' => 'attachment',
                    'filename' => $file->getClientOriginalName(),
                    'path' => $path,
                    'mime_type' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                    'file_hash' => $fileHash,
                    'uploaded_by' => $user->id,
                    'is_primary' => false,
                ]);

                // Cleanup temp
                Storage::disk('public')->delete($tempPath);
            }
        }

        // Notify admin about the resubmission
        if ($wasReturned) {
            try {
                $admin = \App\Models\User::where('role', 'admin')->first();
                if ($admin) {
                    \Illuminate\Support\Facades\Mail::to($admin)->queue(new \App\Mail\NewSubmission($project));
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send resubmission email: ' . $e->getMessage());
            }
        }

        $message = $wasReturned
            ? 'Project updated and resubmitted for administrator review.'
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
        if (!$project->authors->contains($user)) {
            abort(403, 'Unauthorized action.');
        }

        // Can only cancel if pending or returned (not yet approved/archived)
        if (!in_array($project->status, ['pending', 'returned'])) {
            return redirect()->back()->with('error', 'Cannot cancel submission. Project is already ' . $project->status . '.');
        }

        // Delete files
        foreach ($project->files as $file) {
            Storage::disk('public')->delete($file->path);
        }
        $project->files()->delete();

        // Detach authors
        $project->authors()->detach();

        // Delete project (Hard Delete for cancelled drafts)
        $project->forceDelete();

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
            $recentProject->forceDelete();

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

    /**
     * Log and download a project file.
     */
    public function downloadFile(ProjectFile $file)
    {
        $fullPath = Storage::disk('public')->path($file->path);
        abort_unless(file_exists($fullPath), 404);

        $project = $file->project;
        $user = auth()->user();

        // 1. Attachment Protection: Only owners and admins can download attachments
        if ($file->type === 'attachment') {
            $isOwner = $user && $project && $project->authors->contains($user);
            $isPrivileged = $user && $user->isAdmin();
            abort_unless($isOwner || $isPrivileged, 403, 'Attachments are restricted to faculty and administrators.');
        }

        // 2. Manuscript Visibility: ONLY logged-in users can download manuscripts
        $isManuscript = strtolower($file->type) === 'manuscript' || strtolower($file->filename) === 'manuscript.pdf';
        if ($isManuscript) {
            abort_unless(auth()->check(), 403, 'Downloading the full manuscript is restricted to registered members. Please sign in to download.');
        }

        if ($project->status !== 'published') {
            $isOwner = $user && $project->authors->contains($user);
            $isPrivileged = $user && $user->isAdmin();
            abort_unless($isOwner || $isPrivileged, 403, 'This project has not been published yet.');
        }

        // Log download
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'file_downloaded',
            'target_type' => 'project_file',
            'target_id' => $file->id,
            'ip' => request()->ip(),
            'meta' => [
                'filename' => $file->filename,
                'type' => $file->type,
                'project_id' => $file->project_id,
                'guest' => !auth()->check()
            ]
        ]);

        // Generate a clean filename from project title for manuscripts
        $downloadName = $file->filename;
        $isManuscript = strtolower($file->type) === 'manuscript' || strtolower($file->filename) === 'manuscript.pdf';

        if ($isManuscript && $project) {
            $safeTitle = Str::slug($project->title);
            if (empty($safeTitle)) {
                $safeTitle = 'project-' . $project->id;
            }
            $downloadName = $safeTitle . '.pdf';
        }

        return response()->download($fullPath, $downloadName);
    }

    /**
     * Log and stream a project file.
     */
    public function viewFile(ProjectFile $file)
    {
        $fullPath = Storage::disk('public')->path($file->path);
        abort_unless(file_exists($fullPath), 404);

        $project = $file->project;
        $user = auth()->user();

        // 1. Attachment Protection
        if ($file->type === 'attachment') {
            $ext = strtolower(pathinfo($file->filename, PATHINFO_EXTENSION));
            $isSafeMedia = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'mov', 'webm', 'avi']);

            $isOwner = $user && $project && ($project->authors->contains($user) || $project->adviser_id == $user->id);
            $isPrivileged = $user && $user->isAdmin();

            // Allow if owner/admin OR if logged in and it's a safe media file
            $canView = $isOwner || $isPrivileged || (auth()->check() && $isSafeMedia);

            abort_unless($canView, 403, 'This attachment is restricted to project authors and administrators.');
        }

        // 2. Manuscript Visibility: Guests can only view published manuscripts
        $isManuscript = strtolower($file->type) === 'manuscript' || strtolower($file->filename) === 'manuscript.pdf';
        $isGuest = !auth()->check();

        if ($project->status !== 'published') {
            $isOwner = $user && $project->authors->contains($user);
            $isPrivileged = $user && $user->isAdmin();
            abort_unless($isOwner || $isPrivileged, 403, 'This project has not been published yet.');
        }

        // Log view
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'file_viewed',
            'target_type' => 'project_file',
            'target_id' => $file->id,
            'ip' => request()->ip(),
            'meta' => [
                'filename' => $file->filename,
                'type' => $file->type,
                'project_id' => $file->project_id,
                'guest' => $isGuest
            ]
        ]);

        // 3. Guest Partial Preview (5 Pages) for Manuscripts
        if ($isManuscript && $isGuest) {
            return $this->streamPartialPdf($fullPath, 5);
        }

        // Force PDF to be inline for better browser/mobile support (Streaming Option)
        if (strtolower(pathinfo($fullPath, PATHINFO_EXTENSION)) === 'pdf') {
            $downloadName = $file->filename;
            $isManuscript = strtolower($file->type) === 'manuscript' || strtolower($file->filename) === 'manuscript.pdf';

            if ($isManuscript && $project) {
                $downloadName = Str::slug($project->title) . '.pdf';
            }

            return response()->stream(function () use ($fullPath) {
                readfile($fullPath);
            }, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $downloadName . '"',
                'Content-Length' => filesize($fullPath),
                'X-Content-Type-Options' => 'nosniff',
                'Cache-Control' => 'private, max-age=0, must-revalidate',
            ]);
        }

        return response()->file($fullPath);
    }

    /**
     * Streams only the first X pages of a PDF for guest previews.
     * Requires: composer require setasign/fpdf setasign/fpdi
     */
    protected function streamPartialPdf($path, $maxPages = 5)
    {
        if (!class_exists(\setasign\Fpdi\Fpdi::class)) {
            // Fallback: If library is not installed, we still serve the file but 
            // you should run: composer require setasign/fpdf setasign/fpdi
            return response()->stream(function () use ($path) {
                readfile($path);
            }, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="preview_unrestricted.pdf"',
            ]);
        }

        try {
            $pdf = new \setasign\Fpdi\Fpdi();
            $pageCount = $pdf->setSourceFile($path);
            $pagesToRender = min($pageCount, $maxPages);

            for ($n = 1; $n <= $pagesToRender; $n++) {
                $tplIdx = $pdf->importPage($n);
                $specs = $pdf->getTemplateSize($tplIdx);
                $pdf->AddPage($specs['orientation'], [$specs['width'], $specs['height']]);
                $pdf->useTemplate($tplIdx);
            }

            return response($pdf->Output('S'), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="manuscript_preview.pdf"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
            ]);
        } catch (\Exception $e) {
            // If splitting fails, fallback to full stream to avoid breaking the page
            return response()->stream(function () use ($path) {
                readfile($path);
            }, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="preview_fallback.pdf"',
            ]);
        }
    }
}

