<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">
                {{ __('User Profile') }}: {{ $user->name }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.users.edit', $user) }}" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition ease-in-out duration-150">
                    Edit User
                </a>
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-gray-300 dark:hover:bg-gray-600 transition-all shadow-sm hover:shadow-md border border-gray-300 dark:border-gray-600">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($user->trashed())
                <div class="mb-6 bg-rose-500/10 border border-rose-500/30 rounded-3xl p-6 flex items-center justify-between shadow-2xl shadow-rose-500/10 animate-pulse">
                    <div class="flex items-center gap-4">
                        <span class="text-3xl">👤</span>
                        <div>
                            <h3 class="text-rose-500 font-black uppercase tracking-widest text-sm">Archived Account</h3>
                            <p class="text-rose-400/70 text-xs font-bold">This user account is currently in the Central Archive. They cannot log in until restored.</p>
                        </div>
                    </div>
                    <form action="{{ route('admin.archive.restore', ['type' => 'user', 'id' => $user->id]) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-6 py-2.5 bg-rose-500 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:bg-rose-600 transition-all shadow-lg shadow-rose-500/40">
                            Restore Account
                        </button>
                    </form>
                </div>
            @endif
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- User Basic Info Card -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-white/5 overflow-hidden shadow-sm sm:rounded-lg p-6 transition-colors">
                        <div class="text-center mb-6">
                            <div class="flex justify-center mb-4">
                                <!-- Ultra-Strict Circle Fix -->
                                <div class="bg-indigo-600 rounded-full border-4 border-white/20 shadow-xl flex items-center justify-center text-white flex-shrink-0" 
                                     style="width: 96px; height: 96px; min-width: 96px; min-height: 96px; overflow: hidden;">
                                    <span class="text-4xl font-black uppercase" style="line-height: 1;">
                                        {{ substr($user->name, 0, 1) }}
                                    </span>
                                </div>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h3>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">{{ $user->email }}</p>
                        </div>

                        <div class="space-y-4 pt-6 border-t border-gray-100 dark:border-slate-700 transition-colors">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Account Role</label>
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $user->role === 'admin' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </div>

                            @if($user->isStudent())
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Student ID</label>
                                <p class="text-gray-900 dark:text-white font-mono font-bold">{{ $user->student_id ?? 'Not Provided' }}</p>
                            </div>
                            @endif

                            @if($user->program)
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Degree Program</label>
                                <p class="text-gray-900 dark:text-white font-bold text-sm">{{ $user->program }}</p>
                            </div>
                            @endif

                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Account Status</label>
                                @if($user->is_active)
                                    <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-xs font-bold bg-green-100 text-green-700 uppercase tracking-wider">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-xs font-bold bg-amber-100 text-amber-700 uppercase tracking-wider border border-amber-200">
                                        Pending Review
                                    </span>
                                @endif
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Registered Since</label>
                                <p class="text-gray-900 dark:text-white text-sm ">{{ $user->created_at->format('M d, Y') }}</p>
                            </div>

                            @if($user->last_activity_at)
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Last Seen</label>
                                <p class="text-gray-900 dark:text-white text-sm">{{ $user->last_activity_at->diffForHumans() }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Recent Activity Card -->
                    <div class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-white/5 overflow-hidden shadow-sm sm:rounded-lg mt-6 p-6 transition-colors">
                        <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4 border-b border-gray-200 dark:border-slate-700 pb-2 transition-colors">Recent Activity Log</h4>
                        <div class="space-y-4">
                            @forelse($activities as $log)
                                @php
                                    $colorClass = match(true) {
                                        str_contains($log->action, 'login') || str_contains($log->action, 'validated') || str_contains($log->action, 'approved') => 'emerald',
                                        str_contains($log->action, 'logout') || str_contains($log->action, 'delete') => 'red',
                                        str_contains($log->action, 'failed') || str_contains($log->action, 'returned') || str_contains($log->action, 'rejected') || str_contains($log->action, 'blocked') => 'amber',
                                        str_contains($log->action, 'upload') || str_contains($log->action, 'edit') || str_contains($log->action, 'create') => 'indigo',
                                        default => 'gray'
                                    };

                                    $dotColors = match($colorClass) {
                                        'emerald' => 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]',
                                        'red' => 'bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.5)]',
                                        'amber' => 'bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.5)]',
                                        'indigo' => 'bg-indigo-500 shadow-[0_0_8px_rgba(99,102,241,0.5)]',
                                        default => 'bg-gray-500 shadow-[0_0_8px_rgba(107,114,128,0.5)]'
                                    };

                                    $textColors = match($colorClass) {
                                        'emerald' => 'text-emerald-400',
                                        'red' => 'text-red-400',
                                        'amber' => 'text-amber-400',
                                        'indigo' => 'text-indigo-400',
                                        default => 'text-gray-400'
                                    };
                                @endphp
                                <div class="flex gap-3 items-start">
                                    <div class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0 {{ $dotColors }}"></div>
                                    <div>
                                        <p class="text-xs text-gray-600 dark:text-gray-300 leading-snug">
                                            <span class="{{ $textColors }} font-bold uppercase tracking-tighter">{{ str_replace('_', ' ', $log->action) }}</span>
                                        </p>
                                        <span class="text-[9px] text-gray-500 uppercase font-black tracking-widest">{{ $log->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-xs text-gray-500 italic">No recent activity detected.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Projects / Submissions Card -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-white/5 overflow-hidden shadow-sm sm:rounded-lg transition-colors">
                        <div class="p-6 md:p-8">
                            <div class="flex items-center justify-between mb-8 border-b border-gray-100 dark:border-slate-800 pb-4">
                                <h4 class="text-[11px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-[0.3em]">
                                    Project Records
                                </h4>
                                <span class="bg-gray-100 dark:bg-slate-800 px-3 py-1 rounded-full text-[10px] font-black text-gray-500 dark:text-slate-400">
                                    {{ $user->authoredProjects->count() }} Total
                                </span>
                            </div>

                            <div class="space-y-4">
                                @forelse($user->authoredProjects as $project)
                                    @php
                                        $isTrashed = $project->trashed();
                                        $statusColors = match($project->status) {
                                            'published' => 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20',
                                            'pending'   => 'bg-amber-500/10 text-amber-500 border-amber-500/20',
                                            'returned'  => 'bg-rose-500/10 text-rose-500 border-rose-500/20',
                                            'archived'  => 'bg-gray-500/10 text-gray-500 border-gray-500/20',
                                            default     => 'bg-blue-500/10 text-blue-500 border-blue-500/20'
                                        };
                                        $statusLabel = match($project->status) {
                                            'published' => 'Official Published Record',
                                            'pending'   => 'Archive Processing',
                                            'returned'  => 'Returned for Revision',
                                            'archived'  => 'Archived Repository',
                                            default     => ucfirst($project->status)
                                        };
                                    @endphp
                                    <div class="group relative bg-gray-50 dark:bg-slate-800/40 border border-gray-200 dark:border-white/5 rounded-[2rem] p-6 hover:bg-white dark:hover:bg-slate-800 transition-all duration-300">
                                        <div class="flex flex-col md:flex-row gap-6 items-start">
                                            {{-- Year/Icon --}}
                                            <div class="flex flex-col items-center justify-center bg-white dark:bg-slate-900 rounded-2xl w-20 h-20 flex-shrink-0 border border-gray-200 dark:border-white/5 shadow-sm">
                                                <span class="text-2xl">📄</span>
                                                <span class="text-[9px] font-black text-gray-400 mt-1 uppercase">{{ $project->year }}</span>
                                            </div>

                                            {{-- Info --}}
                                            <div class="flex-1 min-w-0">
                                                <div class="flex flex-wrap items-center gap-2 mb-2">
                                                    <span class="px-2 py-0.5 {{ $statusColors }} rounded text-[9px] font-black uppercase tracking-widest border">
                                                        {{ $statusLabel }}
                                                    </span>
                                                    @if($isTrashed)
                                                        <span class="px-2 py-0.5 bg-rose-600 text-white rounded text-[9px] font-black uppercase tracking-widest shadow-sm">
                                                            Deleted
                                                        </span>
                                                    @endif
                                                    <span class="text-[10px] font-bold text-gray-400 dark:text-slate-500 uppercase tracking-tight ml-1">
                                                        {{ $project->program }}
                                                    </span>
                                                </div>

                                                <h5 class="text-lg font-black text-gray-900 dark:text-white leading-tight mb-4 group-hover:text-blue-600 dark:group-hover:text-indigo-400 transition-colors truncate">
                                                    {{ $project->title ?: 'Untitled Project' }}
                                                </h5>

                                                <div class="flex flex-wrap gap-x-6 gap-y-2">
                                                    <div class="flex items-center gap-2 text-xs">
                                                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Adviser:</span>
                                                        <span class="font-bold text-gray-600 dark:text-slate-400">{{ $project->adviser->name ?? $project->adviser_name ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="flex items-center gap-2 text-xs">
                                                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Category:</span>
                                                        <span class="font-bold text-gray-600 dark:text-slate-400">
                                                            {{ $project->categories->isNotEmpty() ? $project->categories->first()->name : 'Uncategorized' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Actions --}}
                                            <div class="flex md:flex-col gap-2">
                                                <a href="{{ route('projects.show', $project) }}" class="p-2 rounded-xl bg-white dark:bg-slate-900 border border-gray-200 dark:border-white/10 text-gray-400 hover:text-blue-500 transition-colors" title="View">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                </a>
                                                @if(auth()->user()->isAdmin())
                                                <a href="{{ route('admin.projects.edit', $project) }}" class="p-2 rounded-xl bg-white dark:bg-slate-900 border border-gray-200 dark:border-white/10 text-gray-400 hover:text-emerald-500 transition-colors" title="Edit">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-16 bg-gray-50 dark:bg-slate-800/20 rounded-[2rem] border border-dashed border-gray-200 dark:border-slate-800 transition-colors">
                                        <div class="w-16 h-16 bg-white dark:bg-slate-900 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl shadow-sm">📁</div>
                                        <p class="text-gray-500 dark:text-slate-400 font-black text-xs uppercase tracking-widest">No projects found</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
