<x-mail::message>
# Project Returned for Revisions

Your project **"{{ $project->title }}"** has been returned by your adviser, **{{ $project->adviser->name }}**.

@if($project->rejection_reason)
## Adviser's Feedback

> {{ $project->rejection_reason }}
@endif

**Action Required:**
1.  Go to your Student Dashboard.
2.  Locate the returned project.
3.  Click **"Edit"** to update your submission.
4.  Make the necessary corrections and resubmit.

<x-mail::button :url="route('student.home')">
Go to Student Dashboard
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
