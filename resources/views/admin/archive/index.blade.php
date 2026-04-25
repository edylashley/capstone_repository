<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-xl text-white leading-tight uppercase tracking-widest flex items-center gap-3">
                <span class="text-indigo-500">🛡️</span> Central Archive Center
            </h2>
            <div class="px-3 py-1 bg-rose-500/10 border border-rose-500/20 rounded-full">
                <span class="text-[10px] font-black text-rose-500 uppercase tracking-widest">Security Safety Net Active</span>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ 
        tab: '{{ request('tab', 'projects') }}',
        selectedProjects: [],
        selectedUsers: [],
        
        toggleAllProjects() {
            if (this.selectedProjects.length === {{ $trashedProjects->count() }}) {
                this.selectedProjects = [];
            } else {
                this.selectedProjects = [
                    @foreach($trashedProjects as $p) '{{ $p->id }}', @endforeach
                ];
            }
        },
        
        toggleAllUsers() {
            if (this.selectedUsers.length === {{ $trashedUsers->count() }}) {
                this.selectedUsers = [];
            } else {
                this.selectedUsers = [
                    @foreach($trashedUsers as $u) '{{ $u->id }}', @endforeach
                ];
            }
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Tab Navigation --}}
            <div class="flex items-center gap-2 mb-8 bg-slate-900 p-1.5 rounded-2xl border border-white/5 w-fit">
                <button @click="tab = 'projects'; window.history.replaceState(null, null, '?tab=projects')" 
                        :class="tab === 'projects' ? 'bg-indigo-600 text-white shadow-lg' : 'text-slate-500 hover:text-slate-300'"
                        class="px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all duration-300 flex items-center gap-2">
                    📄 Projects
                    <span class="bg-black/20 px-2 py-0.5 rounded-md text-[10px]">{{ $trashedProjects->count() }}</span>
                </button>
                <button @click="tab = 'users'; window.history.replaceState(null, null, '?tab=users')" 
                        :class="tab === 'users' ? 'bg-indigo-600 text-white shadow-lg' : 'text-slate-500 hover:text-slate-300'"
                        class="px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all duration-300 flex items-center gap-2">
                    👥 Users
                    <span class="bg-black/20 px-2 py-0.5 rounded-md text-[10px]">{{ $trashedUsers->count() }}</span>
                </button>
            </div>

            {{-- Projects Tab --}}
            <div x-show="tab === 'projects'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="bg-slate-900 border border-white/5 rounded-3xl overflow-hidden shadow-2xl">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-950 border-b border-white/5">
                                <th class="p-5 w-10">
                                    <input type="checkbox" @click="toggleAllProjects()" :checked="selectedProjects.length === {{ $trashedProjects->count() }} && {{ $trashedProjects->count() }} > 0" class="rounded border-white/10 bg-slate-800 text-indigo-600 focus:ring-indigo-500">
                                </th>
                                <th class="p-5 text-[10px] font-black uppercase tracking-widest text-slate-500">Project Information</th>
                                <th class="p-5 text-[10px] font-black uppercase tracking-widest text-slate-500">Deleted On</th>
                                <th class="p-5 text-[10px] font-black uppercase tracking-widest text-slate-500 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($trashedProjects as $project)
                                <tr class="hover:bg-white/[0.02] transition-colors group" :class="selectedProjects.includes('{{ $project->id }}') ? 'bg-indigo-500/5' : ''">
                                    <td class="p-5">
                                        <input type="checkbox" value="{{ $project->id }}" x-model="selectedProjects" class="rounded border-white/10 bg-slate-800 text-indigo-600 focus:ring-indigo-500">
                                    </td>
                                    <td class="p-5">
                                        <a href="{{ route('projects.show', $project) }}" class="flex items-center gap-4 group/item">
                                            <div class="w-10 h-10 rounded-xl bg-slate-950 border border-white/5 flex items-center justify-center text-lg shadow-inner group-hover/item:border-indigo-500/50 transition-all">📄</div>
                                            <div>
                                                <p class="font-black text-sm text-white leading-tight mb-1 group-hover/item:text-indigo-400 transition-colors">{{ $project->title }}</p>
                                                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">
                                                    {{ $project->program }} • Adviser: {{ $project->adviser_name ?? $project->adviser?->name ?? 'N/A' }}
                                                </p>
                                            </div>
                                        </a>
                                    </td>
                                    <td class="p-5">
                                        <p class="text-xs font-bold text-slate-400">{{ $project->deleted_at->format('M d, Y') }}</p>
                                        <p class="text-[10px] text-slate-600 uppercase font-black tracking-tighter">{{ $project->deleted_at->diffForHumans() }}</p>
                                    </td>
                                    <td class="p-5">
                                        <div class="flex items-center justify-end gap-2">
                                            <form action="{{ route('admin.archive.restore', ['type' => 'project', 'id' => $project->id, 'tab' => 'projects']) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="px-4 py-2 bg-emerald-600/10 hover:bg-emerald-600 text-emerald-500 hover:text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all border border-emerald-500/20">
                                                    Restore
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.archive.force-delete', ['type' => 'project', 'id' => $project->id, 'tab' => 'projects']) }}" method="POST" onsubmit="return confirm('WARNING: This will permanently delete this project and all its records from the system. This cannot be undone. Proceed?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-4 py-2 bg-rose-600/10 hover:bg-rose-600 text-rose-500 hover:text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all border border-rose-500/20">
                                                    Delete Permanently
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                             @empty
                                <tr>
                                    <td colspan="4" class="p-24 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="relative mb-6">
                                                <div class="absolute inset-0 bg-indigo-500/10 blur-3xl rounded-full"></div>
                                                <svg class="w-20 h-20 text-slate-700 relative z-10 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                                </svg>
                                            </div>
                                            <h3 class="text-slate-400 font-black text-sm uppercase tracking-[0.3em] mb-2">No Archived Projects</h3>
                                            <p class="text-slate-600 text-[10px] font-bold uppercase tracking-widest">Your project archive is currently empty.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Users Tab --}}
            <div x-show="tab === 'users'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="bg-slate-900 border border-white/5 rounded-3xl overflow-hidden shadow-2xl">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-950 border-b border-white/5">
                                <th class="p-5 w-10">
                                    <input type="checkbox" @click="toggleAllUsers()" :checked="selectedUsers.length === {{ $trashedUsers->count() }} && {{ $trashedUsers->count() }} > 0" class="rounded border-white/10 bg-slate-800 text-indigo-600 focus:ring-indigo-500">
                                </th>
                                <th class="p-5 text-[10px] font-black uppercase tracking-widest text-slate-500">Account Details</th>
                                <th class="p-5 text-[10px] font-black uppercase tracking-widest text-slate-500">Deleted On</th>
                                <th class="p-5 text-[10px] font-black uppercase tracking-widest text-slate-500 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($trashedUsers as $user)
                                <tr class="hover:bg-white/[0.02] transition-colors group" :class="selectedUsers.includes('{{ $user->id }}') ? 'bg-indigo-500/5' : ''">
                                    <td class="p-5">
                                        <input type="checkbox" value="{{ $user->id }}" x-model="selectedUsers" class="rounded border-white/10 bg-slate-800 text-indigo-600 focus:ring-indigo-500">
                                    </td>
                                    <td class="p-5">
                                        <a href="{{ route('admin.users.show', $user) }}" class="flex items-center gap-4 group/item">
                                            <div class="w-10 h-10 rounded-xl bg-slate-950 border border-white/5 flex items-center justify-center text-lg shadow-inner group-hover/item:border-indigo-500/50 transition-all">👤</div>
                                            <div>
                                                <p class="font-black text-sm text-white leading-tight mb-1 group-hover/item:text-indigo-400 transition-colors">{{ $user->name }}</p>
                                                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">
                                                    {{ $user->email }} • <span class="text-indigo-400">{{ strtoupper($user->role) }}</span>
                                                </p>
                                            </div>
                                        </a>
                                    </td>
                                    <td class="p-5">
                                        <p class="text-xs font-bold text-slate-400">{{ $user->deleted_at->format('M d, Y') }}</p>
                                        <p class="text-[10px] text-slate-600 uppercase font-black tracking-tighter">{{ $user->deleted_at->diffForHumans() }}</p>
                                    </td>
                                    <td class="p-5">
                                        <div class="flex items-center justify-end gap-2">
                                            <form action="{{ route('admin.archive.restore', ['type' => 'user', 'id' => $user->id, 'tab' => 'users']) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="px-4 py-2 bg-emerald-600/10 hover:bg-emerald-600 text-emerald-500 hover:text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all border border-emerald-500/20">
                                                    Restore
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.archive.force-delete', ['type' => 'user', 'id' => $user->id, 'tab' => 'users']) }}" method="POST" onsubmit="return confirm('WARNING: This will permanently delete this user account and all their login history. This cannot be undone. Proceed?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-4 py-2 bg-rose-600/10 hover:bg-rose-600 text-rose-500 hover:text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all border border-rose-500/20">
                                                    Delete Permanently
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                             @empty
                                <tr>
                                    <td colspan="4" class="p-24 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="relative mb-6">
                                                <div class="absolute inset-0 bg-indigo-500/10 blur-3xl rounded-full"></div>
                                                <svg class="w-20 h-20 text-slate-700 relative z-10 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                                </svg>
                                            </div>
                                            <h3 class="text-slate-400 font-black text-sm uppercase tracking-[0.3em] mb-2">No Archived Users</h3>
                                            <p class="text-slate-600 text-[10px] font-bold uppercase tracking-widest">There are no deleted user accounts to display.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Floating Bulk Actions Bar --}}
            <div x-show="(tab === 'projects' && selectedProjects.length > 0) || (tab === 'users' && selectedUsers.length > 0)"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-10"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-10"
                 class="fixed bottom-8 left-1/2 transform -translate-x-1/2 z-50">
                
                <div class="bg-indigo-600 text-white px-8 py-4 rounded-3xl shadow-2xl flex items-center gap-8 border border-white/20 backdrop-blur-md">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-xs font-black" x-text="tab === 'projects' ? selectedProjects.length : selectedUsers.length"></span>
                        <span class="text-xs font-black uppercase tracking-widest">Items Selected</span>
                    </div>
                    
                    <div class="h-8 w-px bg-white/20"></div>
                    
                    <div class="flex items-center gap-3">
                        {{-- Bulk Restore Form --}}
                        <form action="{{ route('admin.archive.bulk') }}" method="POST">
                            @csrf
                            <input type="hidden" name="type" :value="tab === 'projects' ? 'project' : 'user'">
                            <input type="hidden" name="action" value="restore">
                            <input type="hidden" name="tab" :value="tab">
                            <template x-for="id in (tab === 'projects' ? selectedProjects : selectedUsers)" :key="id">
                                <input type="hidden" name="ids[]" :value="id">
                            </template>
                            <button type="submit" class="px-5 py-2 bg-white text-indigo-600 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-50 transition-all flex items-center gap-2">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                Bulk Restore
                            </button>
                        </form>

                        {{-- Bulk Delete Form --}}
                        <form action="{{ route('admin.archive.bulk') }}" method="POST" onsubmit="return confirm('WARNING: You are about to PERMANENTLY DELETE multiple items. This cannot be undone. Proceed?')">
                            @csrf
                            <input type="hidden" name="type" :value="tab === 'projects' ? 'project' : 'user'">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="tab" :value="tab">
                            <template x-for="id in (tab === 'projects' ? selectedProjects : selectedUsers)" :key="id">
                                <input type="hidden" name="ids[]" :value="id">
                            </template>
                            <button type="submit" class="px-5 py-2 bg-rose-500 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-rose-600 transition-all flex items-center gap-2">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                Bulk Purge
                            </button>
                        </form>
                        
                        <button @click="selectedProjects = []; selectedUsers = []" class="text-white/60 hover:text-white text-[10px] font-black uppercase tracking-widest transition-all">Cancel</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
