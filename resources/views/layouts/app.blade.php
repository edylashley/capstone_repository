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

        /* Custom Premium Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #020617;
            /* Slate 950 */
        }

        ::-webkit-scrollbar-thumb {
            background: #475569;
            /* Slate 600 - Brighter for better visibility */
            border-radius: 20px;
            border: 3px solid #020617;
            transition: all 0.3s ease;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #6366f1;
            /* Indigo 500 */
        }

        /* Firefox */
        * {
            scrollbar-width: thin;
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

<body class="font-sans antialiased bg-slate-950 text-slate-200 transition-all duration-500"
    :class="{ 'overflow-hidden': sidebarOpen || (typeof supportOpen !== 'undefined' && supportOpen) }"
    x-data="{ sidebarOpen: false, sidebarCollapsed: false, supportOpen: false }">
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
                        class="lg:hidden bg-slate-950 text-white p-4 flex items-center justify-between shadow-md border-b border-white/5">
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('images/system-logo.jpg') }}"
                                class="w-12 h-12 object-contain rounded-full shadow-lg border-2 border-white/20"
                                alt="NORSU Capstone Repository">
                            <span class="text-sm font-black uppercase tracking-wider">Capstone Repository</span>
                        </div>
                        <button @click="sidebarOpen = true" class="p-2 rounded-lg hover:bg-white/10 transition">
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
                <header class="bg-slate-900 shadow-2xl border-b border-white/5 relative z-30">
                    <div class="py-4 md:py-6 px-4 md:px-8 text-white flex items-center gap-4">
                        <!-- Toggle Button (Visible only when sidebar is collapsed on desktop) -->
                        <button @click="sidebarCollapsed = false" x-show="sidebarCollapsed" x-cloak
                            class="hidden lg:block p-2 rounded-lg bg-slate-800 hover:bg-slate-700 transition-colors focus:outline-none"
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
                class="relative bg-slate-900 rounded-2xl shadow-2xl w-full max-w-sm p-8 flex flex-col items-center text-center border border-white/10">
                {{-- Animated Check Circle --}}
                <div
                    class="w-20 h-20 rounded-full bg-green-900/40 flex items-center justify-center mb-5 border border-green-500/30">
                    <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>

                {{-- Title --}}
                <h3 class="text-xl font-black text-white mb-2">Success!</h3>

                {{-- Message --}}
                <p class="text-sm text-slate-400 leading-relaxed mb-6">
                    {{ session('success') ?? session('status') }}
                </p>

                {{-- OK Button --}}
                <button @click="open = false"
                    class="w-full px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl shadow-lg shadow-green-900/20 hover:shadow-green-900/40 transition-all transform hover:-translate-y-0.5 uppercase text-sm tracking-wider">
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
                    class="bg-slate-900 rounded-[2.5rem] shadow-2xl w-full max-w-lg overflow-hidden pointer-events-auto flex flex-col max-h-full border border-white/10">
                    {{-- Header --}}
                    <div
                        class="bg-gradient-to-br from-slate-800 to-slate-900 p-8 text-white flex justify-between items-center gap-4 shrink-0 border-b border-white/5">
                        <div class="flex items-center gap-4 pr-2">
                            <div
                                class="w-12 h-12 bg-indigo-500/20 rounded-2xl flex items-center justify-center border border-indigo-500/30">
                                <svg class="w-7 h-7 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-black text-xl tracking-tight">System Support</h3>
                                <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest mt-0.5">Verified
                                    Communication Channel</p>
                            </div>
                        </div>
                        <button @click="supportOpen = false"
                            class="text-slate-400 hover:text-white transition-colors bg-white/5 hover:bg-white/10 rounded-xl p-2.5">
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
                                    class="mb-8 bg-emerald-500/10 rounded-[1.5rem] border border-emerald-500/20 overflow-hidden relative shadow-lg">
                                    <div class="absolute top-0 right-0 pt-3 pr-4">
                                        @if($latestReply->expires_at)
                                            <span class="text-[9px] font-black text-emerald-500/40 uppercase tracking-[0.2em]">
                                                Terminating {{ $latestReply->expires_at->diffForHumans() }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="p-6">
                                        <div class="flex items-center gap-3 mb-3">
                                            <div
                                                class="w-8 h-8 rounded-full bg-emerald-500/20 flex items-center justify-center border border-emerald-500/30">
                                                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                            <h4 class="font-black text-xs text-emerald-400 uppercase tracking-widest">Admin
                                                Resolution</h4>
                                        </div>
                                        <div class="bg-slate-950/50 p-4 rounded-xl border border-white/5 shadow-inner">
                                            <p class="text-sm text-slate-200 leading-relaxed font-medium italic">
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
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2.5 ml-1">Issue
                                    Category</label>
                                <select name="category" x-model="selectedCategory" required
                                    class="w-full bg-slate-950 border-white/10 rounded-2xl text-white focus:border-indigo-500 focus:ring-indigo-500 text-sm py-3.5 px-4 font-bold">
                                    <option value="" disabled selected class="bg-slate-900">Select category...</option>
                                    <option value="bug" class="bg-slate-900">System Bug / Error</option>
                                    <option value="correction" class="bg-slate-900">Request Record Correction</option>
                                    <option value="account" class="bg-slate-900">Account / Login Issue</option>
                                    <option value="general" class="bg-slate-900">General Question</option>
                                    <option value="others" class="bg-slate-900">Others (Please specify)</option>
                                </select>
                            </div>

                            <div x-show="selectedCategory === 'others'" x-cloak x-transition class="mt-4">
                                <label
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2.5 ml-1">Specify
                                    Category</label>
                                <input type="text" name="custom_category" :required="selectedCategory === 'others'"
                                    placeholder="e.g. Feedback, UI Improvement"
                                    class="w-full bg-slate-950 border-white/10 rounded-2xl text-white focus:border-indigo-500 focus:ring-indigo-500 text-sm py-3.5 px-4 font-bold placeholder:text-slate-600">
                            </div>



                            <div>
                                <label
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2.5 ml-1">Issue
                                    Details</label>
                                <textarea name="message" rows="4" required
                                    placeholder="Provide detailed context for administrative review..."
                                    class="w-full bg-slate-950 border-white/10 rounded-2xl text-white focus:border-indigo-500 focus:ring-indigo-500 text-sm py-3.5 px-4 font-bold resize-none placeholder:text-slate-600 leading-relaxed"></textarea>
                            </div>

                            @if(!auth()->check())
                                <div>
                                    <label
                                        class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2.5 ml-1">Contact
                                        Email</label>
                                    <input type="email" name="email" required placeholder="Institutional email"
                                        class="w-full bg-slate-950 border-white/10 rounded-2xl text-white focus:border-indigo-500 focus:ring-indigo-500 text-sm py-3.5 px-4 font-bold shadow-inner placeholder:text-slate-600">
                                </div>
                            @endif

                            <div class="pt-4">
                                <button type="submit"
                                    class="w-full flex justify-center py-4 px-6 rounded-2xl text-[11px] font-black uppercase tracking-[0.2em] text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all shadow-xl shadow-indigo-900/40 transform hover:-translate-y-1 active:scale-[0.98]">
                                    Transmit Ticket
                                </button>
                                <p
                                    class="text-[9px] text-center text-slate-500 mt-5 font-black uppercase tracking-widest opacity-60">
                                    Queue Priority: Standard Institutional Response
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
                    count: {{ \App\Models\SupportTicket::where(function ($q) {
            $q->where('status', 'pending')->orWhere(function ($q2) {
                $q2->where('category', 'security')->where('created_at', '>=', now()->subDay()); }); })->count() }},
                    async updateCount() {
                        try {
                            const response = await fetch('{{ route('admin.support.count') }}');
                            const data = await response.json();
                            this.count = data.count;
                        } catch (e) {}
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