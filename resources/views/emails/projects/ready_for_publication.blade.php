<x-mail::message>
# Project Ready for Publication

**Adviser Verification Complete**

The project **"{{ $project->title }}"** has been approved by Adviser **{{ $project->adviser->name }}**.

It is now ready for your final verification and publication to the repository.

**Project Details:**
*   **Title:** {{ $project->title }}
*   **Adviser:** {{ $project->adviser->name }}
*   **Year:** {{ $project->year }}

<x-mail::button :url="route('admin.projects.index')">
Go to Admin Dashboard
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
