<x-mail::message>
<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" align="center">
    <tr>
        <td align="center" style="border-top: 1px solid #e5e7eb; margin-top: 10px; padding-top: 25px;">
            <h1 style="color: #111827; font-size: 24px; font-weight: 900; margin-bottom: 10px;">Verify Your Email Address</h1>
        </td>
    </tr>
    <tr>
        <td align="center" style="color: #4b5563; font-size: 15px; line-height: 1.6; padding-bottom: 30px;">
            Hello **{{ $userName }}**, <br>
            We received a request to register an account with the email address **{{ $email }}**. Use the verification code below to complete your registration:
        </td>
    </tr>
    <tr>
        <td align="center">
            <div style="background-color: #f8fafc; border: 2px dashed #e2e8f0; border-radius: 16px; padding: 40px 20px; text-align: center; margin-bottom: 35px; width: 80%;">
                <div style="text-transform: uppercase; font-size: 11px; font-weight: 800; color: #64748b; letter-spacing: 3px; margin-bottom: 15px;">
                    Your Verification Code
                </div>
                <div style="font-size: 48px; font-weight: 900; letter-spacing: 15px; color: #4f46e5; font-family: 'Courier New', Courier, monospace; margin-left: 15px;">
                    {{ $code }}
                </div>
                <div style="font-size: 12px; color: #94a3b8; font-weight: 600; margin-top: 20px; text-transform: uppercase; letter-spacing: 1px;">
                    This code expires in 10 minutes
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <td align="left">
            <div style="background-color: #fffbeb; border: 1px solid #fef3c7; border-radius: 12px; padding: 20px; margin-bottom: 35px;">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td style="vertical-align: top; width: 30px; font-size: 20px;">⚠️</td>
                        <td style="vertical-align: top; padding-left: 10px;">
                            <strong style="color: #92400e; font-size: 14px; display: block; margin-bottom: 2px;">Important Security Note</strong> 
                            <span style="color: #b45309; font-size: 13px; line-height: 1.5;">If you did not request this registration, please ignore this email. Someone may have entered your email address by mistake.</span>
                        </td>
                    </tr>
                </table>
            </div>
        </td>
    </tr>
    <tr>
        <td align="center" style="color: #94a3b8; font-size: 13px; padding-top: 25px; line-height: 1.5;">
            Enter this code on the registration page to verify your email and continue setting up your account.
        </td>
    </tr>
    <tr>
        <td align="center" style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #f1f5f9; color: #cbd5e1; font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px;">
            Sent by {{ config('app.name') }}
        </td>
    </tr>
</table>
</x-mail::message>