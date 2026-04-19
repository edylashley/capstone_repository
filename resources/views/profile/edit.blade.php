<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Profile Settings') }}
            </h2>
            <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-800 dark:bg-gray-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-gray-700 dark:hover:bg-gray-600 transition-all shadow-sm hover:shadow-md border border-gray-700 dark:border-gray-600">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Go Back
            </a>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Profile Information Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="text-lg font-black text-slate-800">Profile Information</h3>
                    <p class="text-xs text-slate-500 mt-1">Update your account's profile information and email address.</p>
                </div>
                <div class="p-6 max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- Update Password Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="text-lg font-black text-slate-800">Update Password</h3>
                    <p class="text-xs text-slate-500 mt-1">Ensure your account is using a long, random password to stay secure.</p>
                </div>
                <div class="p-6 max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
