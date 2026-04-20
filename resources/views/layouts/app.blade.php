<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    <link rel="icon" type="image/jpg" href="{{ asset('images/system-logo.jpg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Sidebar offset styles -->
        /* Sidebar offsets no longer needed with flex-flow */
    @stack('styles')
</head>

<body class="font-sans antialiased bg-slate-100 text-slate-900 overflow-x-hidden"
    x-data="{ sidebarOpen: false, sidebarCollapsed: false }">
    <div class="min-h-screen flex flex-col lg:flex-row overflow-x-hidden">
        @auth
            @if(!request()->is('/'))
                @include('layouts.sidebar')
            @endif
        @endauth

        <div class="flex-1 flex flex-col min-h-screen min-w-0 transition-all duration-300 ease-in-out">
            <!-- Mobile Header (Visible only on small screens) -->
            @auth
                @if(!request()->is('/'))
                    <div class="lg:hidden bg-indigo-700 text-white p-4 flex items-center justify-between shadow-md">
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('images/system-logo.jpg') }}" class="w-12 h-12 object-contain rounded-full shadow-lg border-2 border-white/20"
                                alt="NORSU Capstone Repository">
                            <span class="text-sm font-black uppercase tracking-wider">Capstone Repository</span>
                        </div>
                        <button @click="sidebarOpen = true" class="p-2 rounded-lg hover:bg-indigo-800 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                    </div>
                @endif
            @endauth

            <!-- Page Heading -->
            @if(isset($header) || View::hasSection('header'))
                <header class="bg-gray-800 shadow-sm border-b border-gray-700">
                    <div class="py-4 md:py-6 px-4 md:px-8 text-white flex items-center gap-4">
                        <!-- Toggle Button (Visible only when sidebar is collapsed on desktop) -->
                        <button @click="sidebarCollapsed = false" x-show="sidebarCollapsed" x-cloak
                            class="hidden lg:block p-2 rounded-lg bg-gray-700 hover:bg-gray-600 transition-colors focus:outline-none"
                            title="Show Sidebar">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        <div class="flex-1">
                            @if(isset($header))
                                {{ $header }}
                            @else
                                @yield('header')
                            @endif
                        </div>
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="flex-1">
                @if(isset($slot))
                    {{ $slot }}
                @else
                    @yield('content')
                @endif
            </main>

            @include('layouts.footer')
        </div>
    </div>
    {{-- ── Global Success Modal ──────────────────────────────────────── --}}
    @if(session('success') || session('status'))
        <div x-data="{ open: true }" x-show="open" x-cloak
            class="fixed inset-0 z-[9999] flex items-center justify-center px-4" style="display: none;">
            {{-- Backdrop --}}
            <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="open = false"></div>

            {{-- Modal Card --}}
            <div x-show="open" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-90 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-90 translate-y-4"
                class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 flex flex-col items-center text-center">
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
                <button @click="open = false"
                    class="w-full px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl shadow-lg shadow-green-200 hover:shadow-green-300 transition-all transform hover:-translate-y-0.5 uppercase text-sm tracking-wider">
                    OK
                </button>
            </div>
        </div>
    @endif

    {{-- x-cloak helper (hides Alpine elements until init) --}}
    <style>
        [x-cloak] {
            display: none !important
        }
    </style>

    {{-- Global loading spinner for submit buttons --}}
    @include('components.loading-spinner')

    {{-- ── Global Support / Report Issue FAB & Modal ─────────────────── --}}
    @if(!auth()->check() || !auth()->user()->isAdmin())
        <div x-data="{ supportOpen: false }" class="relative z-[9990]">
            {{-- Floating Action Button --}}
            <button @click="supportOpen = true"
                class="fixed bottom-6 right-6 w-14 h-14 bg-indigo-600 hover:bg-indigo-500 text-white rounded-full shadow-2xl flex items-center justify-center transition-all duration-300 transform hover:scale-110 focus:outline-none focus:ring-4 focus:ring-indigo-300 group"
                title="Help & Support">
                <svg class="w-6 h-6 transition-transform group-hover:rotate-12" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                    </path>
                </svg>
            </button>

            {{-- Support Modal Backdrop --}}
            <div x-show="supportOpen" x-cloak x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-[9991]"
                @click="supportOpen = false"></div>

            {{-- Support Modal Content --}}
            <div x-show="supportOpen" x-cloak x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
                class="fixed inset-0 z-[9992] flex items-center justify-center p-4 sm:p-6 pointer-events-none">
                <div
                    class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden pointer-events-auto flex flex-col max-h-full">
                    {{-- Header --}}
                    <div
                        class="bg-gradient-to-r from-indigo-600 to-indigo-800 p-6 text-white flex justify-between items-center gap-4 shrink-0">
                        <div class="flex items-center gap-3 pr-2">
                            <div
                                class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-black text-lg tracking-tight">System Support</h3>
                                <p class="text-indigo-200 text-xs font-semibold uppercase tracking-wider">Report an Issue or
                                    Contact Admin</p>
                            </div>
                        </div>
                        <button @click="supportOpen = false"
                            class="text-white/60 hover:text-white transition-colors bg-white/10 hover:bg-white/20 rounded-lg p-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    {{-- Form Body & Replies --}}
                    <div class="p-6 overflow-y-auto custom-scrollbar">
                        @auth
                            @php
                                $latestReply = \App\Models\SupportTicket::where('user_id', auth()->id())
                                    ->where('status', 'resolved')
                                    ->whereNotNull('admin_reply')
                                    ->orderBy('updated_at', 'desc')
                                    ->first();
                            @endphp
                            @if($latestReply)
                                <div class="mb-6 bg-green-50 rounded-xl border border-green-200 overflow-hidden relative">
                                    <div class="absolute top-0 right-0 pt-2 pr-3">
                                        @if($latestReply->expires_at)
                                            <span class="text-[9px] font-bold text-green-600/60 uppercase tracking-widest">
                                                Auto-deletes {{ $latestReply->expires_at->diffForHumans() }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="p-4 border-b border-green-200/50">
                                        <div class="flex items-center gap-2 mb-1">
                                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <h4 class="font-black text-sm text-green-800">Feedback from Admin</h4>
                                        </div>
                                        <p class="text-xs text-green-600 font-semibold mb-2">Regarding: {{ $latestReply->subject }}
                                        </p>
                                        <div class="bg-white/60 p-3 rounded-lg">
                                            <p class="text-sm text-gray-800 leading-relaxed whitespace-pre-wrap">
                                                {{ $latestReply->admin_reply }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endauth

                        <form action="{{ route('support.store') }}" method="POST" id="support-form" class="space-y-5">
                            @csrf
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Issue Category <span
                                        class="text-red-500">*</span></label>
                                <select name="category" required
                                    class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm">
                                    <option value="" disabled selected>Select the type of issue...</option>
                                    <option value="bug">System Bug / Error</option>
                                    <option value="correction">Request Record Correction</option>
                                    <option value="account">Account / Login Issue</option>
                                    <option value="general">General Question</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Subject <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="subject" required placeholder="Brief title of your issue"
                                    class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Details <span
                                        class="text-red-500">*</span></label>
                                <textarea name="message" rows="4" required
                                    placeholder="Please provide specific details so the admin can assist you efficiently..."
                                    class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm resize-none"></textarea>
                            </div>

                            @if(!auth()->check())
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Your Email <span
                                            class="text-red-500">*</span></label>
                                    <input type="email" name="email" required placeholder="We need this to reply to you"
                                        class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm">
                                </div>
                            @endif

                            <div class="pt-2">
                                <button type="submit"
                                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                    Submit Ticket
                                </button>
                                <p
                                    class="text-[10px] text-center text-gray-400 mt-3 font-semibold uppercase tracking-wider">
                                    Admins typically respond within 24-48 hours.
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ── Admin: Floating Support Notification Icon ──────────────────── --}}
    @if(auth()->check() && auth()->user()->isAdmin())
        @php
            $adminOpenTickets = \App\Models\SupportTicket::where('status', 'pending')->count();
        @endphp
        <a href="{{ route('admin.support.index') }}"
            class="fixed bottom-6 right-6 w-14 h-14 bg-indigo-600 hover:bg-indigo-500 text-white rounded-full shadow-2xl flex items-center justify-center transition-all duration-300 transform hover:scale-110 focus:outline-none focus:ring-4 focus:ring-indigo-300 group z-[9990]"
            title="Support Tickets{{ $adminOpenTickets > 0 ? ' — ' . $adminOpenTickets . ' pending' : '' }}">
            <svg class="w-6 h-6 transition-transform group-hover:scale-110" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                </path>
            </svg>
            @if($adminOpenTickets > 0)
                <span
                    class="absolute -top-1 -right-1 w-6 h-6 bg-red-500 text-white text-[10px] font-black rounded-full flex items-center justify-center shadow-lg animate-pulse ring-2 ring-white">{{ $adminOpenTickets }}</span>
            @endif
        </a>
    @endif
</body>

</html>