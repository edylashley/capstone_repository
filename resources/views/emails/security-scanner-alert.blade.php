<x-mail::message>
# ⚠️ Security Scanner Alert

The NORSU Capstone Repository Security Engine has detected an issue that requires your immediate attention.

**Alert Type:** {{ $details['type'] === 'threat' ? 'MALICIOUS THREAT BLOCKED' : 'SYSTEM SCANNER FAILURE' }}
**Status:** {{ $details['status'] }}

<x-mail::panel>
**Details:**
{{ $details['message'] }}
</x-mail::panel>

**Event Details:**
*   **User:** {{ $details['user_name'] }} ({{ $details['user_role'] }})
*   **Action:** {{ $details['action'] ?? 'File Upload Attempt' }}
*   **Timestamp:** {{ now()->format('M d, Y h:i A') }}
*   **IP Address:** {{ request()->ip() }}

@if($details['type'] === 'threat')
The system has automatically shredded the malicious file and blocked the submission. A high-priority security ticket has also been created.
@else
**ACTION REQUIRED:** The security scanner service is currently non-functional. Students are unable to submit projects until the scanner path or service is fixed. Please check the server configuration immediately.
@endif

<x-mail::button :url="config('app.url') . '/admin/dashboard'">
Go to Admin Dashboard
</x-mail::button>

Thanks,<br>
{{ config('app.name') }} Automated Guard
</x-mail::message>
