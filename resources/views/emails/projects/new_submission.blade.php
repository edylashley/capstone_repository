<x-mail::message>
# New Manuscript Submission

Head's up! A new manuscript has been submitted for your review.

**Project Title:** {{ $project->title }}  
**Submitted By:** {{ $project->authors->pluck('name')->join(', ') }}  
**Cohort Year:** {{ $project->year }}

Please review the manuscript and provide your validation.

<x-mail::button :url="route('faculty.review')">
Go to Review Dashboard
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
