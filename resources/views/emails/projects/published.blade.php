<x-mail::message>
# Project Published!

Great News! The project **"{{ $project->title }}"** has been officially published to the Repository.

It is now publicly accessible and indexed in the institutional archives.

<x-mail::button :url="route('projects.show', $project->id)">
View Published Record
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
