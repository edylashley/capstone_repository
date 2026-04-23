<x-mail::message>
<div style="text-align: center; border-bottom: 1px solid #f1f5f9; padding-bottom: 20px; margin-bottom: 20px;">
    <h1 style="color: #4f46e5; font-size: 24px; font-weight: 900; margin: 0;">Ticket Resolved</h1>
</div>

<p style="color: #475569; font-size: 16px; line-height: 1.6; margin-bottom: 24px; text-align: center;">
    Your support request regarding <strong>"{{ $ticket->subject }}"</strong> has been reviewed and resolved.
</p>

@if($ticket->admin_reply)
<div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; margin-bottom: 24px;">
    <div style="color: #6366f1; font-size: 12px; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px;">
        Official Administrator Response
    </div>
    <div style="color: #1e293b; font-size: 15px; line-height: 1.6; font-style: italic;">
        "{{ $ticket->admin_reply }}"
    </div>
</div>
@endif

<p style="color: #64748b; font-size: 14px; line-height: 1.6; text-align: center;">
    If you have further questions, please visit our support portal.
</p>

<div style="text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #f1f5f9; color: #94a3b8; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px;">
    Sent via {{ config('app.name') }}
</div>
</x-mail::message>
