<x-mail::message>
# Project Ready for Publication

**Administrator Verification Complete**

The project **"{{ $project->title }}"** (Adviser: {{ $project->adviser_name }}) has been verified.

It is now ready for your final verification and publication to the repository.

**Project Details:**
*   **Title:** {{ $project->title }}
*   **Adviser:** {{ $project->adviser_name }}
*   **Year:** {{ $project->year }}

<x-mail::button :url="route('admin.projects.index')">
Go to Admin Dashboard
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
