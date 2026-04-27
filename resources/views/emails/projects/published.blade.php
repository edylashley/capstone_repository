<x-mail::message>
<x-slot:subcopy>
Midnight
</x-slot:subcopy>

# Project Officially Published!

Congratulations! Your project **"{{ $project->title }}"** has been officially published to the **{{ config('app.name') }}**.

<x-mail::panel>
Your work is now publicly accessible and has been added to the research library. This is a significant academic milestone.
</x-mail::panel>

<x-mail::button :url="route('projects.show', $project->id)">
View Published Record
</x-mail::button>

We are proud to host your research in our library.

Best regards,<br>
**{{ config('app.name') }}**
</x-mail::message>