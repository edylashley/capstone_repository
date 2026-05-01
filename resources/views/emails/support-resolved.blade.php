<x-mail::message>
    # Support Request Resolved!

    Your support request regarding **"{{ $ticket->subject }}"** has been marked as resolved by the administrative team.

    @if($ticket->admin_reply)
        <x-mail::panel>
            <div
                style="color: #38bdf8; font-weight: 800; text-transform: uppercase; font-size: 11px; letter-spacing: 1px; margin-bottom: 5px;">
                Administrator Resolution Notes</div>
            <div style="color: #f1f5f9;">{{ $ticket->admin_reply }}</div>
        </x-mail::panel>
    @endif

    If you believe this ticket was closed in error or have further questions, please submit a new request through the
    portal.

    Regards,<br>
    **{{ config('app.name') }} Team**
</x-mail::message>