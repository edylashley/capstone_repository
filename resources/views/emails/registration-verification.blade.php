<x-mail::message>
# Verify Your Email Address

Hello **{{ $userName }}**,

We received a request to register an account with the email address **{{ $email }}**. Use the verification code below to complete your registration:

<x-mail::panel>
<div style="text-align: center;">
<div style="text-transform: uppercase; font-size: 11px; font-weight: 800; color: #64748b; letter-spacing: 3px; margin-bottom: 10px;">
Your Verification Code
</div>
<div style="font-size: 42px; font-weight: 900; letter-spacing: 10px; color: #4f46e5; font-family: monospace;">
{{ $code }}
</div>
<div style="font-size: 11px; color: #94a3b8; font-weight: 600; margin-top: 15px; text-transform: uppercase;">
This code expires in 10 minutes
</div>
</div>
</x-mail::panel>

<div style="background-color: #fffbeb; border: 1px solid #fef3c7; border-radius: 8px; padding: 15px; margin-top: 20px;">
<strong style="color: #92400e; font-size: 14px;">⚠️ Important Security Note</strong><br>
<span style="color: #b45309; font-size: 13px;">If you did not request this registration, please ignore this email.</span>
</div>

Enter this code on the registration page to verify your email and continue setting up your account.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>