<x-mail::message>
<div style="text-align: center; border-top: 1px solid #e5e7eb; margin-top: 10px; padding-top: 25px;">
    <h1 style="color: #059669; font-size: 24px; font-weight: 900; margin-bottom: 10px;">Project Approved!</h1>
</div>

<div style="text-align: center; color: #4b5563; font-size: 15px; line-height: 1.6; margin-bottom: 30px;">
    Congratulations! Your project **"{{ $project->title }}"** has been approved by the **Administrator**.
</div>

<div style="background-color: #ecfdf5; border: 1px solid #d1fae5; border-radius: 12px; padding: 25px; margin-bottom: 30px; text-align: left;">
    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
        <span style="font-size: 20px;">🎉</span>
        <strong style="color: #065f46; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;">Status Update</strong>
    </div>
    <p style="color: #047857; font-size: 14px; line-height: 1.6; margin: 0;">
        Your work has successfully passed the verification stage. It is now in the **"Approved"** status and is currently awaiting final administrative review before official publication in the archive.
    </p>
</div>

<x-mail::button :url="route('student.home')" color="success">
View Project Status
</x-mail::button>

<div style="text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #f1f5f9; color: #cbd5e1; font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px;">
    Sent by {{ config('app.name') }}
</div>
</x-mail::message>