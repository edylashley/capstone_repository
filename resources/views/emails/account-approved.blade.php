<x-mail::message>
    # Welcome to the Archive, <span style="color: #4f46e5 !important;">{{ $user->name }}</span>!

    We are pleased to inform you that your student account has been **officially approved** by the administrative team.

    <x-mail::panel>
        You now have full access to the **{{ config('app.name') }}**. You can start submitting your capstone
        manuscripts, tracking your publication status, and exploring the institutional archives.
    </x-mail::panel>

    ### What can you do now?
    * **Submit Manuscripts:** Upload your final project for review.
    * **Track Status:** Monitor the validation and publication process.
    * **Explore:** Browse through years of institutional research.

    <x-mail::button :url="route('login')">
        Go to Login Page
    </x-mail::button>

    We look forward to seeing your contributions to the library!

    Best regards,<br>
    **{{ config('app.name') }} Administration**
</x-mail::message>