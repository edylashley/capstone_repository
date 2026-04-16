<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('User Profile') }}: {{ $user->name }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.users.edit', $user) }}" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition ease-in-out duration-150">
                    Edit User
                </a>
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition ease-in-out duration-150">
                    Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- User Basic Info Card -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
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

                        <div class="space-y-4 pt-6 border-t border-gray-100 dark:border-gray-700">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Account Role</label>
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $user->role === 'admin' ? 'bg-red-100 text-red-800' : 
                                      ($user->role === 'adviser' ? 'bg-purple-100 text-purple-800' : 
                                      'bg-blue-100 text-blue-800') }}">
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
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mt-6 p-6">
                        <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4 border-b border-gray-700 pb-2">Recent Activity Log</h4>
                        <div class="space-y-4">
                            @forelse($activities as $log)
                                <div class="flex gap-3 items-start">
                                    <div class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0 bg-indigo-500 shadow-[0_0_8px_rgba(99,102,241,0.5)]"></div>
                                    <div>
                                        <p class="text-xs text-gray-300 leading-snug">
                                            <span class="text-indigo-400 font-bold uppercase tracking-tighter">{{ str_replace('_', ' ', $log->action) }}</span>
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
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            @if($user->isAdviser())
                                <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6 border-b border-gray-700 pb-2">Advised Projects</h4>
                                @php $projects = $user->advisedProjects; @endphp
                            @else
                                <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6 border-b border-gray-700 pb-2">Project Submissions</h4>
                                @php $projects = $user->authoredProjects; @endphp
                            @endif

                            <div class="space-y-6">
                                @forelse($projects as $project)
                                    <div class="relative group bg-gray-900/40 rounded-2xl border border-gray-700/50 p-5 hover:border-indigo-500/50 transition-all duration-300">
                                        <div class="flex justify-between items-start gap-4">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 mb-2">
                                                    @if($project->status === 'published')
                                                        <span class="bg-emerald-500/10 text-emerald-500 text-[10px] font-black px-2 py-0.5 rounded uppercase tracking-widest border border-emerald-500/20">Official Record</span>
                                                    @elseif($project->status === 'approved')
                                                        <span class="bg-indigo-500/10 text-indigo-500 text-[10px] font-black px-2 py-0.5 rounded uppercase tracking-widest border border-indigo-500/20">Approved</span>
                                                    @else
                                                        <span class="bg-yellow-500/10 text-yellow-500 text-[10px] font-black px-2 py-0.5 rounded uppercase tracking-widest border border-yellow-500/20">{{ strtoupper($project->status) }}</span>
                                                    @endif
                                                    <span class="text-[10px] text-gray-500 font-bold tracking-widest">{{ $project->year }}</span>
                                                </div>
                                                <h5 class="text-lg font-bold text-white group-hover:text-indigo-400 transition-colors leading-tight mb-2">
                                                    {{ $project->title }}
                                                </h5>
                                                <p class="text-sm text-gray-400 line-clamp-2 italic mb-4">
                                                    {{ Str::limit($project->abstract, 180) }}
                                                </p>
                                                
                                                <div class="flex flex-wrap gap-4 pt-4 border-t border-gray-700/50">
                                                    <div>
                                                        <span class="text-[9px] uppercase font-black text-gray-500 tracking-widest block mb-1">Adviser</span>
                                                        <span class="text-xs text-gray-300 font-bold tracking-tight">{{ $project->adviser->name ?? $project->adviser_name ?? 'N/A' }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="text-[9px] uppercase font-black text-gray-500 tracking-widest block mb-1">Category</span>
                                                        <span class="text-xs text-gray-300 font-bold tracking-tight">{{ $project->specialization ?? 'Uncategorized' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex flex-col gap-2">
                                                <a href="{{ route('projects.show', $project) }}" class="flex items-center justify-center p-2 rounded-xl bg-gray-800 border border-gray-700 text-gray-400 hover:text-white hover:bg-indigo-600 hover:border-indigo-500 transition-all shadow-sm" title="View Full Project">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                </a>
                                                 @if(auth()->user()->isAdmin())
                                                <a href="{{ route('admin.projects.edit', $project) }}" class="flex items-center justify-center p-2 rounded-xl bg-gray-800 border border-gray-700 text-gray-400 hover:text-white hover:bg-emerald-600 hover:border-emerald-500 transition-all shadow-sm" title="Manage Project Metadata">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-10 bg-gray-900/20 rounded-2xl border border-dashed border-gray-700">
                                        <p class="text-gray-500 italic text-sm">No project records found for this user.</p>
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
