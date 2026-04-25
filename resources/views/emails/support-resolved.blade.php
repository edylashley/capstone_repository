<x-mail::message>
# Ticket Resolved

Your support request regarding **"{{ $ticket->subject }}"** has been reviewed and resolved.

@if($ticket->admin_reply)
<x-mail::panel>
**Official Administrator Response:**
*{{ $ticket->admin_reply }}*
</x-mail::panel>
@endif

If you have further questions, please visit our support portal.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
