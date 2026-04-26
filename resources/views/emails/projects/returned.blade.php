<x-mail::message>
# Project Revision Required

Your submission **"{{ $project->title }}"** has been reviewed by the administrative team. Some modifications are required before it can be officially published.

@if($project->rejection_reason)
<x-mail::panel>
<div style="color: #38bdf8; font-weight: 800; text-transform: uppercase; font-size: 11px; letter-spacing: 1px; margin-bottom: 5px;">Administrator Feedback</div>
<div style="font-style: italic; color: #f1f5f9;">"{{ $project->rejection_reason }}"</div>
</x-mail::panel>
@endif

### Required Actions:
1. Log in to your **Student Dashboard**.
2. Locate this project in the **"Returned"** section.
3. Click **"Edit"** to address the feedback.
4. Resubmit for final administrative review.

<x-mail::button :url="route('student.home')">
Open Student Dashboard
</x-mail::button>

If you have questions, please reach out to the department office.

Regards,<br>
**{{ config('app.name') }} Administration**
</x-mail::message>