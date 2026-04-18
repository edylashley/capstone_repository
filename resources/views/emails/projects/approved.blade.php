<x-mail::message>
    # Project Approved!

    Congratulations! Your project **"{{ $project->title }}"** has been approved by your adviser,
    **{{ $project->adviser->name }}**.

    Your work has passed the verification stage and is now in the **"Approved"** status. It is currently awaiting final
    administrative review for publication.

    <x-mail::button :url="route('projects.index')">
        View Project Status
    </x-mail::button>

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>