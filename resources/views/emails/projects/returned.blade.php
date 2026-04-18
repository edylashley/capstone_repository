<x-mail::message>
<div style="text-align: center; border-top: 1px solid #e5e7eb; margin-top: 10px; padding-top: 25px;">
    <h1 style="color: #111827; font-size: 24px; font-weight: 900; margin-bottom: 10px;">Project Returned for Revisions</h1>
</div>

<div style="text-align: center; color: #4b5563; font-size: 15px; line-height: 1.6; margin-bottom: 30px;">
    Your project **"{{ $project->title }}"** has been reviewed by your adviser, **{{ $project->adviser->name }}**, and requires some modifications before it can be published.
</div>

@if($project->rejection_reason)
<div style="background-color: #fef2f2; border: 1px solid #fee2e2; border-radius: 12px; padding: 25px; margin-bottom: 30px; text-align: left;">
    <strong style="color: #991b1b; font-size: 14px; display: block; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 1px;">Adviser's Feedback:</strong>
    <div style="color: #b91c1c; font-size: 14px; line-height: 1.6; font-style: italic;">
        "{{ $project->rejection_reason }}"
    </div>
</div>
@endif

<div style="text-align: left; background-color: #f8fafc; border-radius: 12px; padding: 25px; margin-bottom: 30px;">
    <strong style="color: #475569; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 15px;">Next Steps:</strong>
    <ul style="color: #64748b; font-size: 14px; line-height: 1.8; margin: 0; padding-left: 20px;">
        <li>Go to your **Student Dashboard**.</li>
        <li>Locate the returned project in your list.</li>
        <li>Click **"Edit"** to update your submission.</li>
        <li>Make the necessary corrections and resubmit for review.</li>
    </ul>
</div>

<x-mail::button :url="route('student.home')" color="primary">
Go to Student Dashboard
</x-mail::button>

<div style="text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #f1f5f9; color: #cbd5e1; font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px;">
    Sent by {{ config('app.name') }}
</div>
</x-mail::message>