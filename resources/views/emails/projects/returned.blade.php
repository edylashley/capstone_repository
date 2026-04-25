<x-mail::message>
# Project Returned for Revisions

Your project **"{{ $project->title }}"** has been reviewed and requires modifications before it can be published.

@if($project->rejection_reason)
<x-mail::panel>
**Administrator Feedback:**
*{{ $project->rejection_reason }}*
</x-mail::panel>
@endif

### Next Steps:
1. Go to your **Student Dashboard**.
2. Locate the returned project in your list.
3. Click **"Edit"** to update your submission.
4. Make the necessary corrections and resubmit for review.

<x-mail::button :url="route('student.home')">
Go to Student Dashboard
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>