<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2
                class="font-black text-3xl text-gray-900 dark:text-white leading-tight uppercase tracking-widest flex items-center gap-3">
                <span class="text-blue-500 dark:text-indigo-500"></span> Recently Deleted
            </h2>
            <div class="px-3 py-1 bg-rose-500/10 border border-rose-500/20 rounded-full">
                <span class="text-[10px] font-black text-rose-500 uppercase tracking-widest">Security Safety Net
                    Active</span>
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
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Unified Control Bar --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                {{-- Tab Navigation --}}
                <div
                    class="flex items-center gap-2 bg-gray-100 dark:bg-slate-900 p-1.5 rounded-2xl border border-gray-200 dark:border-white/5 w-fit transition-colors">
                    <button @click="tab = 'projects'; window.history.replaceState(null, null, '?tab=projects')"
                        :class="tab === 'projects' ? 'bg-blue-600 dark:bg-indigo-600 text-white shadow-md dark:shadow-lg' : 'text-gray-500 dark:text-slate-500 hover:text-gray-900 dark:hover:text-slate-300'"
                        class="px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all duration-300 flex items-center gap-2">
                        📄 Projects
                        <span
                            class="bg-black/20 px-2 py-0.5 rounded-md text-[10px]">{{ $trashedProjects->count() }}</span>
                    </button>
                    <button @click="tab = 'users'; window.history.replaceState(null, null, '?tab=users')"
                        :class="tab === 'users' ? 'bg-blue-600 dark:bg-indigo-600 text-white shadow-md dark:shadow-lg' : 'text-gray-500 dark:text-slate-500 hover:text-gray-900 dark:hover:text-slate-300'"
                        class="px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all duration-300 flex items-center gap-2">
                        👥 Users
                        <span class="bg-black/20 px-2 py-0.5 rounded-md text-[10px]">{{ $trashedUsers->count() }}</span>
                    </button>
                </div>

                {{-- Bulk/Selected Actions --}}
                <div class="flex items-center gap-3">
                    {{-- Restore Selected/All --}}
                    <form action="{{ route('admin.archive.bulk') }}" method="POST"
                        x-show="(tab === 'projects' && {{ $trashedProjects->count() }} > 0) || (tab === 'users' && {{ $trashedUsers->count() }} > 0)">
                        @csrf
                        <input type="hidden" name="type" :value="tab === 'projects' ? 'project' : 'user'">
                        <input type="hidden" name="action" value="restore">
                        <input type="hidden" name="tab" :value="tab">

                        {{-- Determine if ALL or SELECTED --}}
                        <input type="hidden" name="all"
                            :value="(tab === 'projects' ? selectedProjects.length : selectedUsers.length) === 0 ? '1' : '0'">
                        <template x-for="id in (tab === 'projects' ? selectedProjects : selectedUsers)" :key="id">
                            <input type="hidden" name="ids[]" :value="id">
                        </template>

                        <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600/10 text-emerald-500 border border-emerald-500/20 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-600 hover:text-white transition-all shadow-lg">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                </path>
                            </svg>
                            <span
                                x-text="(tab === 'projects' ? selectedProjects.length : selectedUsers.length) === 0 ? 'Restore All' : 'Restore Selected'"></span>
                        </button>
                    </form>

                    {{-- Delete Selected/All --}}
                    <form action="{{ route('admin.archive.bulk') }}" method="POST"
                        x-show="(tab === 'projects' && {{ $trashedProjects->count() }} > 0) || (tab === 'users' && {{ $trashedUsers->count() }} > 0)"
                        onsubmit="return confirm('WARNING: This will PERMANENTLY delete ' + ((tab === 'projects' ? selectedProjects.length : selectedUsers.length) === 0 ? 'ALL' : 'SELECTED') + ' items. This cannot be undone. Proceed?')">
                        @csrf
                        <input type="hidden" name="type" :value="tab === 'projects' ? 'project' : 'user'">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="tab" :value="tab">

                        <input type="hidden" name="all"
                            :value="(tab === 'projects' ? selectedProjects.length : selectedUsers.length) === 0 ? '1' : '0'">
                        <template x-for="id in (tab === 'projects' ? selectedProjects : selectedUsers)" :key="id">
                            <input type="hidden" name="ids[]" :value="id">
                        </template>

                        <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-rose-600/10 text-rose-500 border border-rose-500/20 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-rose-600 hover:text-white transition-all shadow-lg">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                </path>
                            </svg>
                            <span
                                x-text="(tab === 'projects' ? selectedProjects.length : selectedUsers.length) === 0 ? 'Empty Trash' : 'Purge Selected'"></span>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Projects Tab --}}
            <div x-show="tab === 'projects'" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                <div
                    class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-white/5 rounded-3xl overflow-hidden shadow-sm dark:shadow-2xl transition-colors">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="bg-gray-50 dark:bg-slate-800/50 border-b border-gray-200 dark:border-white/5 transition-colors">
                                <th class="p-4 w-10">
                                    <input type="checkbox" @click="toggleAllProjects()"
                                        :checked="selectedProjects.length === {{ $trashedProjects->count() }} && {{ $trashedProjects->count() }} > 0"
                                        class="rounded border-gray-400 dark:border-slate-600 bg-white dark:bg-slate-800 text-blue-600 dark:text-indigo-600 focus:ring-blue-500 dark:focus:ring-indigo-500">
                                </th>
                                <th class="p-4 text-[10px] font-black uppercase tracking-widest text-slate-500">Project Details</th>
                                <th class="p-4 text-[10px] font-black uppercase tracking-widest text-slate-500 text-center w-32">Deleted On</th>
                                <th class="p-4 text-[10px] font-black uppercase tracking-widest text-slate-500 text-right w-24">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                            @forelse($trashedProjects as $project)
                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors group"
                                    :class="selectedProjects.includes('{{ $project->id }}') ? 'bg-blue-500/5 dark:bg-indigo-500/5' : ''">
                                    <td class="p-4">
                                        <input type="checkbox" value="{{ $project->id }}" x-model="selectedProjects"
                                            class="rounded border-gray-400 dark:border-slate-600 bg-white dark:bg-slate-800 text-blue-600 dark:text-indigo-600 focus:ring-blue-500 dark:focus:ring-indigo-500">
                                    </td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-4">
                                            <div>
                                                <a href="{{ route('projects.show', $project->id) }}"
                                                    class="font-black text-sm text-gray-900 dark:text-white leading-tight mb-1 hover:text-blue-600 dark:hover:text-indigo-400 transition-colors">
                                                    {{ $project->title }}</a>
                                                <p
                                                    class="text-[10px] text-gray-500 dark:text-slate-500 font-bold uppercase tracking-widest">
                                                    {{ $project->program }} • Adviser:
                                                    {{ $project->adviser_name ?? $project->adviser?->name ?? 'N/A' }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 text-center">
                                        <p class="text-xs font-bold text-gray-600 dark:text-slate-400">
                                            {{ $project->deleted_at->format('M d, Y') }}</p>
                                        <p
                                            class="text-[10px] text-gray-400 dark:text-slate-600 uppercase font-black tracking-tighter">
                                            {{ $project->deleted_at->diffForHumans() }}</p>
                                    </td>
                                    <td class="p-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <form
                                                action="{{ route('admin.archive.restore', ['type' => 'project', 'id' => $project->id, 'tab' => 'projects']) }}"
                                                method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="p-2 text-emerald-500 hover:bg-emerald-500/10 rounded-lg transition-all"
                                                    title="Restore">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </form>
                                            <form
                                                action="{{ route('admin.archive.force-delete', ['type' => 'project', 'id' => $project->id, 'tab' => 'projects']) }}"
                                                method="POST" class="inline"
                                                onsubmit="return confirm('WARNING: This will permanently delete this project. Proceed?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="p-2 text-rose-500 hover:bg-rose-500/10 rounded-lg transition-all"
                                                    title="Delete Permanently">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
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
                                                <div
                                                    class="absolute inset-0 bg-blue-500/10 dark:bg-indigo-500/10 blur-3xl rounded-full">
                                                </div>
                                                <svg class="w-20 h-20 text-gray-300 dark:text-slate-700 relative z-10 opacity-40 dark:opacity-40 transition-colors"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                                    </path>
                                                </svg>
                                            </div>
                                            <h3
                                                class="text-gray-500 dark:text-slate-400 font-black text-sm uppercase tracking-[0.3em] mb-2 transition-colors">
                                                No Archived Projects</h3>
                                            <p
                                                class="text-gray-400 dark:text-slate-600 text-[10px] font-bold uppercase tracking-widest transition-colors">
                                                Your project archive is currently empty.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Users Tab --}}
            <div x-show="tab === 'users'" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                <div
                    class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-white/5 rounded-3xl overflow-hidden shadow-sm dark:shadow-2xl transition-colors">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="bg-gray-50 dark:bg-slate-800/50 border-b border-gray-200 dark:border-white/5 transition-colors">
                                <th class="p-5 w-10">
                                    <input type="checkbox" @click="toggleAllUsers()"
                                        :checked="selectedUsers.length === {{ $trashedUsers->count() }} && {{ $trashedUsers->count() }} > 0"
                                        class="rounded border-gray-400 dark:border-slate-600 bg-white dark:bg-slate-800 text-blue-600 dark:text-indigo-600 focus:ring-blue-500 dark:focus:ring-indigo-500">
                                </th>
                                <th class="p-4 text-[10px] font-black uppercase tracking-widest text-slate-500">Account Details</th>
                                <th class="p-4 text-[10px] font-black uppercase tracking-widest text-slate-500 text-center w-32">Deleted On</th>
                                <th class="p-4 text-[10px] font-black uppercase tracking-widest text-slate-500 text-right w-24">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                            @forelse($trashedUsers as $user)
                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors group"
                                    :class="selectedUsers.includes('{{ $user->id }}') ? 'bg-blue-500/5 dark:bg-indigo-500/5' : ''">
                                    <td class="p-4">
                                        <input type="checkbox" value="{{ $user->id }}" x-model="selectedUsers"
                                            class="rounded border-gray-400 dark:border-slate-600 bg-white dark:bg-slate-950 text-blue-600 dark:text-indigo-600 focus:ring-blue-500 dark:focus:ring-indigo-500">
                                    </td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-4">
                                            <div>
                                                <a href="{{ route('admin.users.show', $user->id) }}"
                                                    class="font-black text-sm text-gray-900 dark:text-white leading-tight mb-1 hover:text-blue-600 dark:hover:text-indigo-400 transition-colors">
                                                    {{ $user->name }}</a>
                                                <p
                                                    class="text-[10px] text-gray-500 dark:text-slate-500 font-bold uppercase tracking-widest">
                                                    {{ $user->email }} • <span
                                                        class="text-blue-500 dark:text-indigo-400">{{ strtoupper($user->role) }}</span>
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 text-center">
                                        <p class="text-xs font-bold text-gray-600 dark:text-slate-400">
                                            {{ $user->deleted_at->format('M d, Y') }}</p>
                                        <p
                                            class="text-[10px] text-gray-400 dark:text-slate-600 uppercase font-black tracking-tighter">
                                            {{ $user->deleted_at->diffForHumans() }}</p>
                                    </td>
                                    <td class="p-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <form
                                                action="{{ route('admin.archive.restore', ['type' => 'user', 'id' => $user->id, 'tab' => 'users']) }}"
                                                method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="p-2 text-emerald-500 hover:bg-emerald-500/10 rounded-lg transition-all"
                                                    title="Restore">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </form>
                                            <form
                                                action="{{ route('admin.archive.force-delete', ['type' => 'user', 'id' => $user->id, 'tab' => 'users']) }}"
                                                method="POST" class="inline"
                                                onsubmit="return confirm('WARNING: This will permanently delete this user account. Proceed?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="p-2 text-rose-500 hover:bg-rose-500/10 rounded-lg transition-all"
                                                    title="Delete Permanently">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
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
                                                <div
                                                    class="absolute inset-0 bg-blue-500/10 dark:bg-indigo-500/10 blur-3xl rounded-full">
                                                </div>
                                                <svg class="w-20 h-20 text-gray-300 dark:text-slate-700 relative z-10 opacity-40 dark:opacity-40 transition-colors"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                                    </path>
                                                </svg>
                                            </div>
                                            <h3
                                                class="text-gray-500 dark:text-slate-400 font-black text-sm uppercase tracking-[0.3em] mb-2 transition-colors">
                                                No Archived Users</h3>
                                            <p
                                                class="text-gray-400 dark:text-slate-600 text-[10px] font-bold uppercase tracking-widest transition-colors">
                                                There are no deleted user accounts to display.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>