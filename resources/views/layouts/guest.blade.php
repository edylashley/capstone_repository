<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'NORSU Portal') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <link rel="icon" type="image/jpg" href="{{ asset('images/system-logo.jpg') }}">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex flex-col items-center justify-center py-6 px-6 bg-slate-950">
            <!-- Decorative background elements -->
            <div class="fixed inset-0 overflow-hidden pointer-events-none">
                <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-600 rounded-full opacity-5 blur-3xl -translate-y-1/2 translate-x-1/2"></div>
                <div class="absolute bottom-0 left-0 w-96 h-96 bg-purple-600 rounded-full opacity-5 blur-3xl translate-y-1/2 -translate-x-1/2"></div>
            </div>
        
            <div class="relative w-full sm:max-w-md bg-slate-900 shadow-2xl rounded-2xl overflow-hidden border border-slate-800 shadow-indigo-500/10 backdrop-blur-xl">
                <!-- Header with gradient -->
                <div class="bg-gradient-to-br from-indigo-600 via-slate-800 to-slate-900 p-4 text-center">
                    <div class="flex flex-col items-center">
                        <div class="w-32 h-32 flex items-center justify-center overflow-hidden mb-4 rounded-full shadow-lg bg-white border-4 border-white/20">
                            <img src="{{ asset('images/system-logo.jpg') }}" alt="Portal Logo" class="w-full h-full object-cover transform scale-100 hover:scale-125 transition-transform duration-500" />
                        </div>
                        <h3 class="text-2xl font-bold text-white uppercase tracking-tight">
                            @if(request()->routeIs('login'))
                                @if(request()->cookie('returning_user'))
                                    Welcome Back
                                @else
                                    Welcome to the Portal
                                @endif
                            @elseif(request()->routeIs('register'))
                                Create Account
                            @elseif(request()->is('register/verify-code') || request()->is('register/resend-code'))
                                Email Verification
                            @else
                                Portal Access
                            @endif
                        </h3>
                        <p class="text-blue-100 text-sm mt-2">
                            @if(request()->routeIs('login'))
                                @if(request()->cookie('returning_user'))
                                    Sign in to your account
                                @else
                                    Access your institutional repository
                                @endif
                            @elseif(request()->routeIs('register'))
                                Join the NORSU CSIT community
                            @elseif(request()->is('register/verify-code') || request()->is('register/resend-code'))
                                Confirm your email address
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Form content -->
                <div class="p-8 md:p-10">
                    {{ $slot }}
                </div>
            </div>

            <div class="mt-6 text-slate-500 text-sm">
                &copy; {{ date('Y') }} NORSU {{ \App\Models\Setting::get('repository_name', 'CSIT Capstone Repository') }}
            </div>
        </div>
        {{-- ── Global Success Modal ──────────────────────────────────────── --}}
        @if(session('success') || session('status'))
        <div
            x-data="{ open: true }"
            x-show="open"
            x-cloak
            class="fixed inset-0 z-[9999] flex items-center justify-center px-4"
            style="display: none;"
        >
            {{-- Backdrop --}}
            <div
                x-show="open"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm"
                @click="open = false"
            ></div>

            {{-- Modal Card --}}
            <div
                x-show="open"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-90 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-90 translate-y-4"
                class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 flex flex-col items-center text-center"
            >
                {{-- Animated Check Circle --}}
                <div class="w-20 h-20 rounded-full bg-green-100 flex items-center justify-center mb-5">
                    <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>

                {{-- Title --}}
                <h3 class="text-xl font-black text-gray-800 mb-2">Success!</h3>

                {{-- Message --}}
                <p class="text-sm text-gray-600 leading-relaxed mb-6">
                    {{ session('success') ?? session('status') }}
                </p>

                {{-- OK Button --}}
                <button
                    @click="open = false"
                    class="w-full px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl shadow-lg shadow-green-200 hover:shadow-green-300 transition-all transform hover:-translate-y-0.5 uppercase text-sm tracking-wider"
                >
                    OK
                </button>
            </div>
        </div>
        @endif

        {{-- x-cloak helper --}}
        <style>[x-cloak]{display:none!important}</style>

        {{-- Global loading spinner for submit buttons --}}
        @include('components.loading-spinner')

        {{-- Set returning user cookie --}}
        <script>
            if (!document.cookie.split(';').some((item) => item.trim().startsWith('returning_user='))) {
                document.cookie = "returning_user=true; max-age=31536000; path=/";
            }
        </script>
    </body>
</html>