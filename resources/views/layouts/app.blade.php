<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Theme Initialization -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    <link rel="icon" type="image/jpg" href="{{ asset('images/system-logo.jpg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Sidebar offset styles -->
    <style>
        @media (min-width: 1024px) {
            .main-content {
                margin-left: 280px;
                width: calc(100% - 280px);
                transition: all 0.3s ease-in-out;
            }

            .sidebar-collapsed-offset {
                margin-left: 0 !important;
                width: 100% !important;
            }
        }

        /* Custom Premium Scrollbar - Light Mode (Default) */
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #f3f4f6; /* gray-100 */
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1; /* slate-300 */
            border-radius: 20px;
            border: 3px solid #f3f4f6;
            transition: all 0.3s ease;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #6366f1; /* indigo-500 */
        }

        /* Custom Premium Scrollbar - Dark Mode */
        .dark ::-webkit-scrollbar-track {
            background: #020617; /* slate-950 */
        }

        .dark ::-webkit-scrollbar-thumb {
            background: #475569; /* slate-600 */
            border: 3px solid #020617;
        }

        .dark ::-webkit-scrollbar-thumb:hover {
            background: #6366f1; /* indigo-500 */
        }

        /* Firefox */
        * {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f3f4f6;
        }

        .dark * {
            scrollbar-color: #475569 #020617;
        }

        /* Performance Optimization: Pause heavy animations when modal is open */
        .overflow-hidden .animate-cyber-grid,
        .overflow-hidden .animate-float {
            animation-play-state: paused !important;
        }
    </style>
    @stack('styles')
</head>

<body
    class="font-sans antialiased bg-gray-50 text-gray-900 dark:bg-slate-950 dark:text-slate-200 transition-colors duration-300"
    :class="{ 'overflow-hidden': sidebarOpen || (typeof supportOpen !== 'undefined' && supportOpen) }" x-data="{ 
        sidebarOpen: false, 
        sidebarCollapsed: false, 
        supportOpen: false,
        theme: localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'),
        toggleTheme() {
            this.theme = this.theme === 'dark' ? 'light' : 'dark';
            localStorage.setItem('theme', this.theme);
            if (this.theme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
    }">
    <div class="min-h-screen flex flex-col lg:flex-row">
        @auth
            @if(!request()->is('/'))
                @include('layouts.sidebar')

                <!-- Simple Sidebar Arrow Toggle -->
                <button @click="sidebarCollapsed = false" x-show="sidebarCollapsed" x-cloak
                    class="fixed top-1/2 left-0 -translate-y-1/2 z-[50] hidden lg:flex w-8 h-12 items-center justify-center rounded-r-xl bg-indigo-600/20 hover:bg-indigo-600 text-white transition-all group border border-l-0 border-indigo-500/30"
                    title="Show Sidebar">
                    <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            @endif
        @endauth

        <div class="flex-1 flex flex-col min-h-screen min-w-0 transition-all duration-300 ease-in-out {{ auth()->check() && !request()->is('/') ? 'main-content' : '' }}"
            :class="{ 'sidebar-collapsed-offset': sidebarCollapsed }">
            <!-- Mobile Header (Visible only on small screens) -->
            @auth
                @if(!request()->is('/'))
                    <div
                        class="lg:hidden bg-white dark:bg-slate-950 text-gray-900 dark:text-white p-4 flex items-center justify-between shadow-sm dark:shadow-md border-b border-gray-200 dark:border-white/5 transition-colors duration-300">
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('images/system-logo.jpg') }}"
                                class="w-12 h-12 object-contain rounded-full shadow-md dark:shadow-lg border border-gray-200 dark:border-white/20"
                                alt="NORSU Capstone Repository">
                            <span class="text-sm font-black uppercase tracking-wider">Capstone Repository</span>
                        </div>
                            <button @click="sidebarOpen = true"
                                class="p-2 rounded-lg text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-white/10 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                    </div>
                @endif
            @endauth

            <!-- Universal Theme Toggle -->
            <div class="fixed mt-2 right-3 z-[100] flex items-center">
                <button @click="toggleTheme()" title="Toggle Light/Dark Mode"
                    class="p-2.5 rounded-xl transition-all duration-500 shadow-xl border focus:outline-none focus:ring-2 group overflow-hidden relative"
                    :class="theme === 'dark' ? 'bg-white/90 backdrop-blur-md border-amber-200 text-amber-600 shadow-amber-500/10 focus:ring-amber-500/50' : 'bg-slate-900/80 backdrop-blur-md border-indigo-500/30 text-indigo-400 shadow-indigo-500/20 focus:ring-indigo-500/50'">

                    <!-- Subtle Hover Glow -->
                    <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"
                        :class="theme === 'dark' ? 'bg-gradient-to-tr from-amber-500/20 to-yellow-500/20' : 'bg-gradient-to-tr from-indigo-500/20 to-purple-500/20'">
                    </div>

                    <template x-if="theme === 'dark'">
                        <svg class="w-5 h-5 relative z-10 transform group-hover:rotate-90 transition-transform duration-700"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M12 3v1m0 16v1m9-9h1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M16.071 19.071l-.707-.707M7.929 4.929l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z">
                            </path>
                        </svg>
                    </template>
                    <template x-if="theme === 'light'">
                        <svg class="w-5 h-5 relative z-10 transform group-hover:-rotate-12 transition-transform duration-700"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
                            </path>
                        </svg>
                    </template>
                </button>
            </div>

            <!-- Page Heading -->
            @if(isset($header) || View::hasSection('header'))
                <header
                    class="bg-white dark:bg-slate-900 shadow-sm dark:shadow-2xl border-b border-gray-200 dark:border-white/5 relative z-30 transition-colors duration-300">
                    <div
                        class="py-4 md:py-6 px-4 md:px-8 text-gray-900 dark:text-white flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4 flex-1">
                            <!-- Toggle Button (Visible only when sidebar is collapsed on desktop) -->
                                <button @click="sidebarCollapsed = false" x-show="sidebarCollapsed" x-cloak
                                    class="hidden lg:block p-2 rounded-lg bg-gray-100 dark:bg-slate-800 hover:bg-gray-200 dark:hover:bg-slate-700 transition-colors focus:outline-none text-gray-900 dark:text-white"
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
                class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl w-full max-w-sm p-8 flex flex-col items-center text-center border border-gray-200 dark:border-white/10 transition-colors duration-300">
                {{-- Animated Check Circle --}}
                <div
                    class="w-20 h-20 rounded-full bg-emerald-50 dark:bg-emerald-900/40 flex items-center justify-center mb-5 border border-emerald-500/30">
                    <svg class="w-10 h-10 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>

                {{-- Title --}}
                <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2">Success!</h3>

                {{-- Message --}}
                <p class="text-sm text-gray-900 dark:text-slate-400 leading-relaxed mb-6">
                    {{ session('success') ?? session('status') }}
                </p>

                {{-- OK Button --}}
                <button @click="open = false"
                    class="w-full px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-lg shadow-emerald-900/20 hover:shadow-emerald-900/40 transition-all transform hover:-translate-y-0.5 uppercase text-sm tracking-wider">
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
        <div x-data="{ selectedCategory: '' }" class="relative z-[9990]">
            {{-- Floating Action Button --}}
            <button @click="supportOpen = true"
                class="fixed bottom-6 right-6 w-14 h-14 bg-indigo-600 hover:bg-indigo-500 text-white rounded-full shadow-[0_0_20px_rgba(79,70,229,0.4)] flex items-center justify-center transition-all duration-300 transform hover:scale-110 focus:outline-none focus:ring-4 focus:ring-indigo-500/50 group"
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
                x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-950/80 z-[9991]"
                @click="supportOpen = false"></div>

            {{-- Support Modal Content --}}
            <div x-show="supportOpen" x-cloak x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
                class="fixed inset-0 z-[9992] flex items-center justify-center p-4 sm:p-6 pointer-events-none">
                <div
                    class="bg-white dark:bg-slate-900 rounded-[2.5rem] shadow-2xl w-full max-w-lg overflow-hidden pointer-events-auto flex flex-col max-h-full border border-gray-200 dark:border-white/10 transition-colors duration-300">
                    {{-- Header --}}
                    <div
                        class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-slate-800 dark:to-slate-900 p-8 text-gray-900 dark:text-white flex justify-between items-center gap-4 shrink-0 border-b border-gray-200 dark:border-white/5 transition-colors">
                        <div class="flex items-center gap-4 pr-2">
                            <div
                                class="w-12 h-12 bg-blue-50 dark:bg-indigo-500/20 rounded-2xl flex items-center justify-center border border-blue-200 dark:border-indigo-500/30">
                                <svg class="w-7 h-7 text-blue-600 dark:text-indigo-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-black text-xl tracking-tight">System Support</h3>
                                <p
                                    class="text-gray-900 dark:text-slate-400 text-[10px] font-black uppercase tracking-widest mt-0.5">
                                    Verified
                                    Communication Channel</p>
                            </div>
                        </div>
                        <button @click="supportOpen = false"
                            class="text-gray-400 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white transition-colors bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 rounded-xl p-2.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    {{-- Form Body & Replies --}}
                    <div class="p-8 overflow-y-auto no-scrollbar">
                        @auth
                            @php
                                $latestReply = \App\Models\SupportTicket::where('user_id', auth()->id())
                                    ->where('status', 'resolved')
                                    ->whereNotNull('admin_reply')
                                    ->orderBy('updated_at', 'desc')
                                    ->first();
                            @endphp
                            @if($latestReply)
                                <div
                                    class="mb-8 bg-emerald-50 dark:bg-emerald-500/10 rounded-[1.5rem] border border-emerald-200 dark:border-emerald-500/20 overflow-hidden relative shadow-sm transition-colors">
                                    <div class="absolute top-0 right-0 pt-3 pr-4">
                                        @if($latestReply->expires_at)
                                            <span class="text-[9px] font-black text-emerald-500/40 uppercase tracking-[0.2em]">
                                                Expires {{ $latestReply->expires_at->diffForHumans() }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="p-6">
                                        <div class="flex items-center gap-3 mb-3">
                                            <div
                                                class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center border border-emerald-300 dark:border-emerald-500/30">
                                                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                            <h4
                                                class="font-black text-xs text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">
                                                Admin
                                                Resolution</h4>
                                        </div>
                                        <div
                                            class="bg-white dark:bg-slate-950/50 p-4 rounded-xl border border-emerald-100 dark:border-white/5 shadow-inner">
                                            <p class="text-sm text-gray-700 dark:text-slate-200 leading-relaxed font-medium italic">
                                                "{{ $latestReply->admin_reply }}"
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endauth

                        <form action="{{ route('support.store') }}" method="POST" id="support-form" class="space-y-6">
                            @csrf
                            <div>
                                <label
                                    class="block text-[10px] font-black text-gray-900 dark:text-slate-400 uppercase tracking-[0.2em] mb-2.5 ml-1">Issue
                                    Category</label>
                                <select name="category" x-model="selectedCategory" required
                                    class="w-full bg-gray-50 dark:bg-slate-950 border-gray-200 dark:border-white/10 rounded-2xl text-gray-900 dark:text-white focus:border-blue-500 focus:ring-blue-500 text-sm py-3.5 px-4 font-bold transition-colors">
                                    <option value="" disabled selected>Select category...</option>
                                    <option value="bug">System Bug / Error</option>
                                    <option value="correction">Request Record Correction</option>
                                    <option value="account">Account / Login Issue</option>
                                    <option value="general">General Question</option>
                                    <option value="others">Others (Please specify)</option>
                                </select>
                            </div>

                            <div x-show="selectedCategory === 'others'" x-cloak x-transition class="mt-4">
                                <label
                                    class="block text-[10px] font-black text-gray-900 dark:text-slate-400 uppercase tracking-[0.2em] mb-2.5 ml-1">Specify
                                    Category</label>
                                <input type="text" name="custom_category" :required="selectedCategory === 'others'"
                                    placeholder="e.g. Feedback, UI Improvement"
                                    class="w-full bg-gray-50 dark:bg-slate-950 border-gray-200 dark:border-white/10 rounded-2xl text-gray-900 dark:text-white focus:border-blue-500 focus:ring-blue-500 text-sm py-3.5 px-4 font-bold placeholder:text-gray-400 dark:placeholder:text-slate-600 transition-colors">
                            </div>

                            <div>
                                <label
                                    class="block text-[10px] font-black text-gray-900 dark:text-slate-400 uppercase tracking-[0.2em] mb-2.5 ml-1">Issue
                                    Details</label>
                                <textarea name="message" rows="4" required
                                    placeholder="Provide detailed context for administrative review..."
                                    class="w-full bg-gray-50 dark:bg-slate-950 border-gray-200 dark:border-white/10 rounded-2xl text-gray-900 dark:text-white focus:border-blue-500 focus:ring-blue-500 text-sm py-3.5 px-4 font-bold resize-none placeholder:text-gray-400 dark:placeholder:text-slate-600 leading-relaxed transition-colors"></textarea>
                            </div>



                            @if(!auth()->check())
                                <div>
                                    <label
                                        class="block text-[10px] font-black text-gray-900 dark:text-slate-400 uppercase tracking-[0.2em] mb-2.5 ml-1">Contact
                                        Email</label>
                                    <input type="email" name="email" required placeholder="Your email address"
                                        class="w-full bg-gray-50 dark:bg-slate-950 border-gray-200 dark:border-white/10 rounded-2xl text-gray-900 dark:text-white focus:border-blue-500 focus:ring-blue-500 text-sm py-3.5 px-4 font-bold shadow-inner placeholder:text-gray-400 dark:placeholder:text-slate-600 transition-colors">
                                </div>
                            @endif

                            <div class="pt-4">
                                <button type="submit"
                                    class="w-full flex justify-center py-4 px-6 rounded-2xl text-[11px] font-black uppercase tracking-[0.2em] text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all shadow-xl shadow-blue-500/30 transform hover:-translate-y-1 active:scale-[0.98]">
                                    Submit Ticket
                                </button>
                                <p
                                    class="text-[9px] text-center text-gray-900 dark:text-slate-500 mt-5 font-black uppercase tracking-widest opacity-60">
                                    We'll get back to you as soon as possible
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
        <a href="{{ route('admin.support.index') }}" x-data="{ 
                                                    count: @js(\App\Models\SupportTicket::getNotificationCount()),
                                                    async updateCount() {
                                                        try {
                                                            const response = await fetch('{{ route('admin.support.count') }}');
                                                            const data = await response.json();
                                                            this.count = data.count;
                                                        } catch (e) { }
                                                    }
                                                }" x-init="setInterval(() => updateCount(), 30000)"
            class="fixed bottom-6 right-6 w-14 h-14 bg-rose-600 hover:bg-rose-500 text-white rounded-full shadow-[0_0_20px_rgba(225,29,72,0.4)] flex items-center justify-center transition-all duration-300 transform hover:scale-110 focus:outline-none focus:ring-4 focus:ring-rose-500/50 group z-[9990]"
            :title="'Support Tickets' + (count > 0 ? ' — ' + count + ' pending' : '')">
            <svg class="w-6 h-6 transition-transform group-hover:scale-110" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                </path>
            </svg>
            <template x-if="count > 0">
                <span x-text="count"
                    class="absolute -top-1.5 -right-1.5 w-6 h-6 bg-amber-400 text-slate-900 text-[10px] font-black rounded-full flex items-center justify-center shadow-[0_0_15px_rgba(251,191,36,0.5)] animate-pulse ring-2 ring-slate-900">
                </span>
            </template>
        </a>
    @endif
</body>

</html>