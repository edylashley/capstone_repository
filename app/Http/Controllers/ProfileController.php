<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Log profile update
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'profile_updated',
            'target_type' => 'user',
            'target_id' => $user->id,
            'ip' => $request->ip(),
            'meta' => [
                'name' => $user->name,
                'email' => $user->email
            ]
        ]);

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        $userId = $user->id;
        $userName = $user->name;

        Auth::logout();

        $user->delete();

        // Log self-deletion
        ActivityLog::create([
            'user_id' => $userId, // Use ID even though account is deleted
            'action' => 'account_self_deleted',
            'target_type' => 'user',
            'target_id' => $userId,
            'ip' => $request->ip(),
            'meta' => [
                'name' => $userName
            ]
        ]);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
