<x-mail::message>
<div style="text-align: center; border-top: 1px solid #e5e7eb; margin-top: 10px; padding-top: 25px;">
    <h1 style="color: #4f46e5; font-size: 24px; font-weight: 900; margin-bottom: 10px;">Project Officially Published!</h1>
</div>

<div style="text-align: center; color: #4b5563; font-size: 15px; line-height: 1.6; margin-bottom: 30px;">
    Great News! Your project **"{{ $project->title }}"** has been officially published to the **{{ config('app.name') }}**.
</div>

<div style="background-color: #eef2ff; border: 1px solid #e0e7ff; border-radius: 12px; padding: 25px; margin-bottom: 30px; text-align: center;">
    <div style="font-size: 30px; margin-bottom: 10px;">📜</div>
    <p style="color: #4338ca; font-size: 14px; line-height: 1.6; margin: 0; font-weight: 600;">
        Your work is now publicly accessible and permanently indexed in the institutional archives. Congratulations on this significant academic milestone!
    </p>
</div>

<x-mail::button :url="route('projects.show', $project->id)" color="primary">
View Published Record
</x-mail::button>

<div style="text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #f1f5f9; color: #cbd5e1; font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px;">
    Sent by {{ config('app.name') }}
</div>
</x-mail::message>