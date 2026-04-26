<x-mail::message>
# New Manuscript Submission

A new capstone manuscript has been submitted and is awaiting administrative review.

<x-mail::panel>
<div style="color: #38bdf8; font-weight: 800; text-transform: uppercase; font-size: 11px; letter-spacing: 1px; margin-bottom: 8px;">Project Details</div>
**Title:** {{ $project->title }}<br>
**Authors:** {{ $project->authors->pluck('name')->join(', ') }}<br>
**Cohort Year:** {{ $project->year }}
</x-mail::panel>

Please review the submission in the administrative dashboard to determine its publication status.

<x-mail::button :url="route('admin.projects.index')">
Review Submission
</x-mail::button>

Regards,<br>
**{{ config('app.name') }} System**
</x-mail::message>