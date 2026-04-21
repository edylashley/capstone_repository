<!-- Logo -->
<div class="flex flex-col items-center py-4 px-2 border-b border-slate-700/50">
    <button @click="sidebarCollapsed = true"
        class="relative w-32 h-32 flex items-center justify-center overflow-hidden rounded-full shadow-[0_0_50px_-10px_rgba(99,102,241,0.6)] bg-slate-950/80 backdrop-blur-md transition duration-300 border-4 border-indigo-500/30 hover:scale-105 hover:shadow-indigo-500/80 focus:outline-none group">
        <img src="{{ asset('images/system-logo.jpg') }}" alt="NORSU Capstone Repository"
            class="w-full h-full object-cover transform scale-120 transition-transform duration-300 group-hover:scale-135" />
    </button>
    <span class="text-sm font-black text-center leading-tight mt-4 uppercase tracking-wider text-white">
        {{ \App\Models\Setting::get('repository_name', 'CSIT Capstone Repository') }}
    </span>
</div>

<!-- Navigation -->
<nav class="flex-1 flex flex-col gap-1 py-4 px-3 overflow-y-auto custom-scrollbar no-scrollbar">
    <!-- Public Access -->
    <a href="{{ route('projects.index') }}"
        class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all duration-200 {{ request()->routeIs('projects.index') ? 'bg-slate-800 shadow-lg text-white' : 'text-slate-400' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
        <span class="font-bold tracking-tight">Browse Library</span>
    </a>

    <!-- Student Specific -->
    @if(auth()->user()->isStudent())
        <div class="mt-4 mb-1 px-4 text-[10px] uppercase font-black tracking-[0.2em] text-slate-500">Registry</div>
        <a href="{{ route('student.home') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all duration-200 {{ request()->routeIs('student.home') ? 'bg-slate-800 shadow-lg text-white' : 'text-slate-400' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                </path>
            </svg>
            <span class="font-bold tracking-tight">My Dashboard</span>
        </a>

        @php
            $deadlineStr = \App\Models\Setting::get('submission_deadline');
            $isPastDeadline = $deadlineStr && \Carbon\Carbon::now()->greaterThan(\Carbon\Carbon::parse($deadlineStr));
            $hasSubmitted = auth()->check() && auth()->user()->isStudent() && auth()->user()->authoredProjects()->exists();
        @endphp
        @if(\App\Models\Setting::get('submissions_open', '1') == '1' && !$isPastDeadline)
            <a href="{{ route('projects.create') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all duration-200 {{ request()->routeIs('projects.create') ? 'bg-slate-800 shadow-lg text-white' : 'text-slate-400' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                </svg>
                <span class="font-bold tracking-tight">Submit Project</span>
            </a>
        @endif
    @endif

    @if(auth()->user()->isAdmin())
        <div class="mt-4 mb-1 px-4 text-[10px] uppercase font-black tracking-[0.2em] text-slate-500">Oversight</div>
        <a href="{{ route('admin.dashboard') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-slate-800 shadow-lg text-white' : 'text-slate-400' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                </path>
            </svg>
            <span class="font-bold tracking-tight">Admin Dashboard</span>
        </a>
    @endif


    <!-- Admin Specific -->
    @if(auth()->user()->isAdmin())
        <div class="mt-4 mb-1 px-4 text-[10px] uppercase font-black tracking-[0.2em] text-slate-500">Management</div>
        <a href="{{ route('admin.users.index') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all duration-200 {{ request()->routeIs('admin.users.*') ? 'bg-slate-800 shadow-lg text-white' : 'text-slate-400' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                </path>
            </svg>
            <span class="font-bold tracking-tight">User Accounts</span>
        </a>
        <a href="{{ route('admin.projects.index') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all duration-200 {{ request()->routeIs('admin.projects.*') ? 'bg-slate-800 shadow-md text-white' : 'text-slate-400' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9l-2-2H5a2 2 0 01-2 2v8a2 2 0 012 2z">
                </path>
            </svg>
            <span class="font-bold tracking-tight">Projects</span>
        </a>

        <a href="{{ route('admin.categories.index') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all duration-200 {{ request()->routeIs('admin.categories.*') ? 'bg-slate-800 shadow-md text-white' : 'text-slate-400' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                </path>
            </svg>
            <span class="font-bold tracking-tight">Specializations</span>
        </a>
        <a href="{{ route('admin.programs.index') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all duration-200 {{ request()->routeIs('admin.programs.*') ? 'bg-slate-800 shadow-md text-white' : 'text-slate-400' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            <span class="font-bold tracking-tight">Programs</span>
        </a>
        <a href="{{ route('admin.settings.index') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all duration-200 {{ request()->routeIs('admin.settings.*') ? 'bg-slate-800 shadow-md text-white' : 'text-slate-400' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                </path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                </path>
            </svg>
            <span class="font-bold tracking-tight">System Preferences</span>
        </a>
        <a href="{{ route('admin.security-demo.index') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all duration-200 {{ request()->routeIs('admin.security-demo.*') ? 'bg-slate-800 shadow-md text-white' : 'text-slate-400' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                </path>
            </svg>
            <span class="font-bold tracking-tight">Security Scan Demo</span>
        </a>
        @php
            $openTicketCount = \App\Models\SupportTicket::where('status', 'pending')->count();
        @endphp
        <a href="{{ route('admin.support.index') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all duration-200 {{ request()->routeIs('admin.support.*') ? 'bg-slate-800 shadow-md text-white' : 'text-slate-400' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z">
                </path>
            </svg>
            <span class="font-bold tracking-tight">Support Tickets</span>
            @if($openTicketCount > 0)
                <span
                    class="ml-auto bg-red-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full min-w-[20px] text-center shadow-sm animate-pulse">{{ $openTicketCount }}</span>
            @endif
        </a>
        <a href="{{ route('admin.logs') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all duration-200 {{ request()->routeIs('admin.logs') ? 'bg-slate-800 shadow-md text-white' : 'text-slate-400' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                </path>
            </svg>
            <span class="font-bold tracking-tight">System Logs</span>
        </a>
    @endif

    <div class="mt-4 mb-1 px-4 text-[10px] uppercase font-black tracking-[0.2em] text-slate-500">Account</div>
    <a href="{{ route('profile.edit') }}"
        class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all duration-200 {{ request()->routeIs('profile.edit') ? 'bg-slate-800 shadow-md text-white' : 'text-slate-400' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
        </svg>
        <span class="font-bold tracking-tight">Profile Settings</span>
    </a>
    {{-- My Tickets removed from sidebar; accessible via the ? support button --}}
</nav>

<!-- User Info & Logout -->
<div class="mt-auto px-4 py-4 border-t border-indigo-400/20 flex flex-col justify-center">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3 overflow-hidden">
            <div
                class="w-10 h-10 rounded-full bg-slate-700 border border-white/10 flex items-center justify-center text-white font-bold text-sm flex-shrink-0 shadow-inner">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
            <div class="flex flex-col overflow-hidden">
                <span class="font-bold text-white text-sm truncate leading-tight">{{ auth()->user()->name }}</span>
                <span class="text-[9px] uppercase font-black tracking-widest text-slate-400 mt-0.5">
                    {{ auth()->user()->role }} {{ auth()->user()->program ? '• ' . auth()->user()->program : '' }}
                </span>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="flex-shrink-0">
            @csrf
            <button type="submit" title="Logout"
                class="p-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white transition-all duration-300 shadow-sm border border-white/5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                    </path>
                </svg>
            </button>
        </form>
    </div>
</div>