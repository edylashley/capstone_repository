<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\RegistrationVerificationCode;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

use App\Models\ActivityLog;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     * Step 1: Validate data, generate a code, send it via email, and show the verification form.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): View|RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'student_id' => ['required', 'string', 'regex:/^[0-9]{9}$/', 'unique:'.User::class],
            'program' => ['required', 'string', 'in:BSInT,Com-Sci'],
            'password' => ['required', 'confirmed', Rules\Password::min(8)],
        ]);

        // Generate a 6-digit verification code
        $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store registration data and code in session
        $request->session()->put('registration_data', [
            'name' => $request->name,
            'email' => $request->email,
            'student_id' => $request->student_id,
            'program' => $request->program,
            'password' => $request->password,
        ]);
        $request->session()->put('registration_code', $verificationCode);
        $request->session()->put('registration_code_expires_at', now()->addMinutes(2));

        // Send the verification email
        Mail::to($request->email)->queue(
            new RegistrationVerificationCode($verificationCode, $request->name)
        );

        return view('auth.verify-registration-code', [
            'email' => $request->email,
            'expiresAt' => now()->addMinutes(2)->timestamp,
        ]);
    }

    /**
     * Verify the registration code and create the user account.
     * Step 2: User enters the 6-digit code to complete registration.
     */
    public function verifyCode(Request $request): View|RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $registrationData = $request->session()->get('registration_data');
        $storedCode = $request->session()->get('registration_code');
        $expiresAt = $request->session()->get('registration_code_expires_at');

        // Session data missing — user likely navigated here directly
        if (!$registrationData || !$storedCode || !$expiresAt) {
            return redirect()->route('register')
                ->withErrors(['code' => 'Your registration session has expired. Please start over.']);
        }

        // Check if code has expired
        if (now()->greaterThan($expiresAt)) {
            // Clean up session
            $request->session()->forget(['registration_data', 'registration_code', 'registration_code_expires_at']);

            return redirect()->route('register')
                ->withErrors(['code' => 'Your verification code has expired. Please register again.']);
        }

        // Check if the code matches
        if ($request->code !== $storedCode) {
            $expiresAt = $request->session()->get('registration_code_expires_at');
            return view('auth.verify-registration-code', [
                'email' => $registrationData['email'],
                'expiresAt' => $expiresAt ? $expiresAt->timestamp : now()->timestamp,
            ])->withErrors(['code' => 'The verification code you entered is incorrect. Please try again.']);
        }

        // Code is valid — create the user
        $user = User::create([
            'name' => $registrationData['name'],
            'email' => $registrationData['email'],
            'student_id' => $registrationData['student_id'],
            'program' => $registrationData['program'],
            'password' => Hash::make($registrationData['password']),
            'role' => User::ROLE_STUDENT,
            'is_active' => false, // Require admin approval
        ]);

        // Log the registration event
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'account_request',
            'target_type' => 'user',
            'target_id' => $user->id,
            'ip' => $request->ip(),
            'meta' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role
            ]
        ]);

        event(new Registered($user));

        // Clean up session
        $request->session()->forget(['registration_data', 'registration_code', 'registration_code_expires_at']);

        // Note: We DO NOT log in the user here because they are not active yet.
        
        return redirect()->route('login')->with('success', 'Registration successful! Your email has been verified and your account is now pending administrative review. You will be able to log in once your CSIT student status is verified.');
    }

    /**
     * Resend the verification code.
     */
    public function resendCode(Request $request): View|RedirectResponse
    {
        $registrationData = $request->session()->get('registration_data');

        if (!$registrationData) {
            return redirect()->route('register')
                ->withErrors(['code' => 'Your registration session has expired. Please start over.']);
        }

        // Generate a new 6-digit code
        $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Update session with new code and expiry
        $request->session()->put('registration_code', $verificationCode);
        $request->session()->put('registration_code_expires_at', now()->addMinutes(10));

        // Send the new verification email
        Mail::to($registrationData['email'])->queue(
            new RegistrationVerificationCode($verificationCode, $registrationData['name'])
        );

        return view('auth.verify-registration-code', [
            'email' => $registrationData['email'],
            'resent' => true,
            'expiresAt' => now()->addMinutes(2)->timestamp,
        ]);
    }
}
