<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-white leading-tight">Admin: Project Oversight</h2>
                <p class="text-xs text-gray-500 uppercase tracking-widest font-black mt-1">Manage project submissions and approvals</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-black uppercase text-gray-400">Database Status:</span>
                <span class="px-2 py-1 bg-green-100 text-green-700 text-[10px] font-black rounded-full uppercase tracking-tighter">Sync Active</span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Top Controls -->
            <div class="flex flex-wrap items-center gap-4">
                <!-- Filter Bar -->
                <div class="dark:bg-gray-800 bg-white p-4 rounded-2xl shadow-sm border border-gray-100 w-auto">
                    <form method="GET" class="flex flex-wrap items-center gap-4 m-0">
                        <div class="flex items-center gap-2">
                            <label class="text-xs font-white uppercase text-gray-400">Status:</label>
                            <select name="status" onchange="this.form.submit()" class="text-sm rounded-lg border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 py-1.5 pl-3 pr-10 border-0 shadow-sm">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending Adviser</option>
                                <option value="approved" {{ request('status')=='approved'?'selected':'' }}>Awaiting Publication</option>
                                <option value="published" {{ request('status')=='published'?'selected':'' }}>Published Records</option>
                            </select>
                        </div>
                        <div class="flex items-center gap-2 border-l-0 sm:border-l border-gray-200 pl-0 sm:pl-4">
                            <label class="text-xs font-white uppercase text-gray-400">Program:</label>
                            <select name="program" onchange="this.form.submit()" class="text-sm rounded-lg border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 py-1.5 pl-3 pr-10 border-0 shadow-sm">
                                <option value="">All Programs</option>
                                <option value="BSInT" {{ request('program')=='BSInT'?'selected':'' }}>BSIT</option>
                                <option value="Com-Sci" {{ request('program')=='Com-Sci'?'selected':'' }}>BSCS</option>
                            </select>
                        </div>
                    </form>
                </div>
                <!-- Bulk Actions -->
                <div class="dark:bg-gray-800 bg-white p-4 rounded-2xl shadow-sm border border-gray-100 w-auto">
                    <form id="bulkActionForm" method="POST" action="{{ route('admin.projects.bulk') }}" class="flex items-center m-0 gap-2" onsubmit="return confirm('Are you sure you want to perform this bulk action?');">
                        @csrf
                        <label class="text-xs font-white uppercase text-gray-400 whitespace-nowrap">Bulk Action:</label>
                        <select name="action" id="bulkActionSelect" required class="text-sm rounded-lg border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 py-1.5 pl-3 pr-8" onchange="document.getElementById('bulkAdviserSelect').classList.toggle('hidden', this.value !== 'reassign'); document.getElementById('bulkAdviserSelect').disabled = this.value !== 'reassign';">
                            <option value="">Select Action...</option>
                            <option value="publish">Publish Selected</option>
                            <option value="reassign">Reassign Adviser</option>
                            <option value="delete">Delete Selected</option>
                        </select>
                        <select name="adviser_id" id="bulkAdviserSelect" class="text-sm rounded-lg border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 py-1.5 pl-3 pr-8 hidden" disabled required>
                            <option value="">Select Adviser...</option>
                            @foreach($advisers as $adviser)
                                <option value="{{ $adviser->id }}">{{ $adviser->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-4 py-1.5 bg-indigo-600 text-white text-[10px] font-black uppercase rounded shadow hover:bg-indigo-700 transition">Apply</button>
                    </form>
                </div>
                <!-- Direct Entry Dropdown -->
                <div class="relative ml-auto" x-data="{ open: false }">
                    <button @click="open = !open" @click.away="open = false" type="button" class="inline-flex items-center justify-center gap-2 px-6 py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-[10px] uppercase tracking-widest rounded-2xl shadow-lg hover:shadow-indigo-500/30 transition transform hover:-translate-y-0.5 flex-shrink-0 whitespace-nowrap">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                        Direct Entry
                        <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="origin-top-right absolute right-0 mt-2 w-48 rounded-2xl shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 z-50 overflow-hidden text-[10px] font-black uppercase tracking-widest"
                         style="display: none;">
                        <a href="{{ route('admin.projects.create') }}" class="block px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-gray-700 hover:text-indigo-600 transition">
                            Single Entry
                        </a>
                        <a href="{{ route('admin.projects.bulk-create') }}" class="block px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-gray-700 hover:text-indigo-600 transition">
                            Bulk Entry (Multi)
                        </a>
                    </div>
                </div>
            </div>

            @if(session('error'))
                <div class="p-4 rounded-xl bg-red-50 text-red-700 border border-red-200 text-sm font-bold animate-pulse">
                    {{ session('error') }}
                </div>
            @endif

            <div class="dark:bg-gray-800 bg-white overflow-hidden shadow-xl sm:rounded-3xl border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="dark:bg-gray-800 bg-gray-50/50 border-b border-gray-100">
                                <th class="px-6 py-4 w-12"><input type="checkbox" onchange="document.querySelectorAll('.project-checkbox').forEach(cb => cb.checked = this.checked)" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"></th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Project Workspace</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Institutional Metadata</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">Project Status</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Administrative Actions</th>
                            </tr>
                        </thead>
                        <tbody class="dark:bg-gray-800 divide-y divide-gray-100">
                            @foreach($projects as $project)
                            <tr class="hover:bg-black transition group">
                                <td class="px-6 py-4">
                                    <input type="checkbox" name="project_ids[]" value="{{ $project->id }}" form="bulkActionForm" class="project-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="max-w-md">
                                        <a href="{{ route('projects.show', $project->id) }}" class="block font-bold text-white group-hover:text-indigo-400 hover:underline transition truncate" title="View Project Details">
                                            {{ $project->title }}
                                        </a>
                                        <div class="text-[10px] text-gray-400 uppercase font-bold mt-1 tracking-tighter">
                                            Authors: {{ $project->authors_list ?: $project->authors->pluck('name')->take(2)->join(', ') }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-gray-400">{{ $project->adviser->name ?? $project->adviser_name ?? 'None' }}</span>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Class of {{ $project->year }}</span>
                                            <span class="text-[9px] px-1.5 py-0.5 bg-gray-700 text-gray-300 rounded font-black uppercase">{{ $project->program }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($project->status === 'published')
                                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-[10px] font-black uppercase tracking-widest ring-1 ring-green-200">Published</span>
                                    @elseif($project->status === 'approved' || $project->status === 'verified')
                                        <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-[10px] font-black uppercase tracking-widest ring-1 ring-amber-200">Awaiting Admin</span>
                                    @else
                                        <span class="px-3 py-1 bg-gray-100 text-gray-500 rounded-full text-[10px] font-black uppercase tracking-widest ring-1 ring-gray-200">Awaiting Faculty</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        @if(in_array($project->status, ['approved', 'verified']))
                                            <form method="POST" action="{{ route('admin.projects.publish', $project->id) }}">
                                                @csrf
                                                <button class="px-4 py-1.5 bg-indigo-600 text-white text-[10px] font-black uppercase rounded shadow-lg hover:shadow-indigo-200 hover:bg-indigo-700 transition transform hover:-translate-y-0.5">
                                                    Publish Now
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('admin.projects.edit', $project->id) }}" class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Edit Metadata">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                        <form method="POST" action="{{ route('admin.projects.destroy', $project->id) }}" onsubmit="return confirm('⚠️ WARNING: This will permanently delete this project and all associated files.\n\nProject: {{ $project->title }}\n\nThis action cannot be undone. Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete Project">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($projects->hasPages())
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                        {{ $projects->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>