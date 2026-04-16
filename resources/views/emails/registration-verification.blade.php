<x-mail::message>
# Verify Your Email Address

Hello **{{ $userName }}**,

Thank you for registering with **{{ config('app.name') }}**. To complete your registration, please enter the following verification code:

<x-mail::panel>
<div style="text-align: center; font-size: 32px; font-weight: bold; letter-spacing: 8px; color: #4F46E5; font-family: monospace;">
{{ $code }}
</div>
</x-mail::panel>

This code will expire in **2 minutes**. If you did not request this registration, you can safely ignore this email.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
