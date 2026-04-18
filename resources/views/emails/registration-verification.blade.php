<x-mail::message>
    # Verify Your Email Address

    Hello **{{ $userName }}**,

    Thank you for registering with **{{ config('app.name') }}**. To complete your registration, please enter the
    following verification code:

    <x-mail::panel>
        <div
            style="text-align: center; font-size: 32px; font-weight: bold; letter-spacing: 8px; color: #4F46E5; font-family: monospace;">
            {{ $code }}
        </div>
    </x-mail::panel>

    This code will expire in **2 minutes**. If you did not request this registration, you can safely ignore this email.

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>
</tr>
<tr>
    <td align="center" style="color: #94a3b8; font-size: 13px; padding-top: 25px; line-height: 1.5;">
        Enter this code on the registration page to verify your email and continue setting up your account.
    </td>
</tr>
<tr>
    <td align="center"
        style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #f1f5f9; color: #cbd5e1; font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px;">
        Sent by {{ config('app.name') }}
    </td>
</tr>
</table>
</x-mail::message>