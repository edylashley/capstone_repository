<x-mail::message>
# Project Officially Published!

Great News! Your project **"{{ $project->title }}"** has been officially published to the **{{ config('app.name') }}**.

<x-mail::panel>
Your work is now publicly accessible and permanently indexed in the institutional archives. Congratulations on this significant academic milestone!
</x-mail::panel>

<x-mail::button :url="route('projects.show', $project->id)">
View Published Record
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>