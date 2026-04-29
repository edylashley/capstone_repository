<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use App\Mail\AccountApproved;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('program')) {
            $query->where('program', $request->program);
        }

        $users = $query->orderBy('is_active', 'asc') // Show Inactive/Pending first
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
        ]);
    }

    public function show($id): View
    {
        $user = User::withTrashed()->findOrFail($id);
        
        $user->load(['authoredProjects' => function($q) {
            $q->withTrashed()->orderBy('year', 'desc');
        }]);

        // Get recent activity for this user
        $activities = ActivityLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.users.show', [
            'user' => $user,
            'activities' => $activities,
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', Rule::in(['student', 'admin'])],
            'student_id' => ['nullable', 'string', 'regex:/^[0-9]{9}$/', 'unique:users'],
            'program' => ['nullable', 'string', Rule::in(\App\Models\Program::pluck('abbreviation')->toArray())],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => $validated['role'],
            'student_id' => $validated['student_id'] ?? null,
            'program' => $validated['program'] ?? null,
            'is_active' => true,
        ]);

        // Log the creation
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'account_created',
            'target_type' => 'user',
            'target_id' => $user->id,
            'ip' => $request->ip(),
            'meta' => [
                'name' => $user->name,
                'role' => $user->role,
                'email' => $user->email
            ]
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'student_id' => ['nullable', 'string', 'regex:/^[0-9]{9}$/', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', Rule::in(['student', 'admin'])],
            'program' => ['nullable', 'string', Rule::in(\App\Models\Program::pluck('abbreviation')->toArray())],
            'is_active' => ['boolean'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->student_id = $validated['student_id'];
        $user->program = $validated['program'];
        $user->role = $validated['role'];
        $user->is_active = $request->has('is_active');

        if (! empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->save();
        
        // Log the update
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'user_updated',
            'target_type' => 'user',
            'target_id' => $user->id,
            'ip' => $request->ip(),
            'meta' => [
                'name' => $user->name,
                'role' => $user->role,
                'status' => $user->is_active ? 'active' : 'inactive'
            ]
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function approve(User $user): RedirectResponse
    {
        $user->is_active = true;
        $user->save();

        // Send approval notification email
        Mail::to($user->email)->queue(new AccountApproved($user));

        // Log the approval
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'account_approved',
            'target_type' => 'user',
            'target_id' => $user->id,
            'ip' => request()->ip(),
            'meta' => [
                'name' => $user->name,
                'student_id' => $user->student_id,
                'role' => $user->role
            ]
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User ' . ($user->student_id ?? $user->name) . ' has been approved and activated.');
    }

    public function deny(User $user): RedirectResponse
    {
        $name = $user->name;
        $userId = $user->id;
        
        // Move to Trash (Soft Delete)
        $user->delete();

        // Log the rejection
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'account_denied',
            'target_type' => 'user',
            'target_id' => $user->id,
            'ip' => request()->ip(),
            'meta' => [
                'name' => $name,
                'email' => $email,
                'rejected_name' => $name
            ],
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "Account for '$name' has been denied and moved to Trash.");
    }

    public function destroy(User $user): RedirectResponse
    {
        // Prevent self-deletion
        if (auth()->id() === $user->id) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot delete your own administrator account.');
        }

        $name = $user->name;
        $userId = $user->id;

        if (request('force_delete') === 'true') {
            $user->forceDelete();
            $actionMsg = "User account for '$name' has been permanently deleted.";
        } else {
            $user->delete();
            $actionMsg = "User account for '$name' has been moved to Trash.";
        }

        // Log the deletion
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'account_deleted',
            'target_type' => 'user',
            'target_id' => $userId,
            'ip' => request()->ip(),
            'meta' => [
                'deleted_name' => $name
            ]
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', $actionMsg);
    }
}
