<x-mail::message>
# Verify Your Email Address

Hello **{{ $userName }}**,

Thank you for joining the **{{ config('app.name') }}**. To complete your registration, please enter the following verification code:

<x-mail::panel>
<div style="text-align: center; padding: 20px 0;">
<div style="text-transform: uppercase; font-size: 11px; font-weight: 800; color: #38bdf8; letter-spacing: 4px; margin-bottom: 12px;">
Security Verification Code
</div>
<div style="font-size: 48px; font-weight: 900; letter-spacing: 12px; color: #f8fafc; font-family: 'Courier New', Courier, monospace; text-shadow: 0 0 20px rgba(56, 189, 248, 0.3);">
{{ $code }}
</div>
<div style="font-size: 11px; color: #64748b; font-weight: 700; margin-top: 15px; text-transform: uppercase; letter-spacing: 1px;">
Expires in 10 minutes
</div>
</div>
</x-mail::panel>

If you did not request this registration, please ignore this email.

Regards,<br>
**{{ config('app.name') }} Team**
</x-mail::message>