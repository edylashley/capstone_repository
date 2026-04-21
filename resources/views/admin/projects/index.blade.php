<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Integrated Header --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-2">
                <div>
                    <h2 class="font-black text-4xl text-white uppercase tracking-tighter leading-none">Capstone Projects
                    </h2>
                    <p class="text-[10px] text-indigo-400 uppercase tracking-[0.4em] font-black mt-3 opacity-80">
                        Research Repository & Manuscript Oversight</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.dashboard') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-slate-900/50 text-slate-400 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-slate-800 hover:text-white transition-all shadow-inner border border-white/5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Dashboard
                    </a>
                </div>
            </div>

            <!-- Top Controls -->
            <div class="flex flex-wrap items-center gap-4">
                <!-- Filter Bar -->
                <div class="bg-slate-900 p-4 rounded-2xl shadow-sm border border-white/5 w-full md:w-auto">
                    <form method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 m-0">
                        <div class="flex items-center justify-between sm:justify-start gap-2">
                            <label class="text-xs font-white uppercase text-slate-400">Status:</label>
                            <select name="status" onchange="this.form.submit()"
                                class="text-sm rounded-lg border-white/10 bg-slate-800 text-white focus:ring-indigo-500 focus:border-indigo-500 py-1.5 pl-3 pr-10 border-0 shadow-sm w-full sm:w-auto text-center sm:text-left">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending
                                    Review
                                </option>
                                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>
                                    Published
                                    Records</option>
                                <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived
                                </option>
                            </select>
                        </div>
                        <div
                            class="flex items-center justify-between sm:justify-start gap-2 border-l-0 sm:border-l border-white/10 pl-0 sm:pl-4">
                            <label class="text-xs font-white uppercase text-slate-400">Program:</label>
                            <select name="program" onchange="this.form.submit()"
                                class="text-sm rounded-lg border-white/10 bg-slate-800 text-white focus:ring-indigo-500 focus:border-indigo-500 py-1.5 pl-3 pr-10 border-0 shadow-sm w-full sm:w-auto text-center sm:text-left">
                                <option value="">All Programs</option>
                                @foreach($programs as $prog)
                                    <option value="{{ $prog->abbreviation }}" {{ request('program') == $prog->abbreviation ? 'selected' : '' }}>
                                        {{ $prog->abbreviation }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <!-- Bulk Actions -->
                <div class="bg-slate-900 p-4 rounded-2xl shadow-sm border border-white/5 w-full md:w-auto">
                    <form id="bulkActionForm" method="POST" action="{{ route('admin.projects.bulk') }}"
                        class="flex flex-col sm:flex-row items-stretch sm:items-center m-0 gap-2"
                        onsubmit="return confirm('Are you sure you want to perform this bulk action?');">
                        @csrf
                        <label class="text-xs font-white uppercase text-slate-400 whitespace-nowrap">Bulk
                            Action:</label>
                        <select name="action" id="bulkActionSelect" required
                            class="text-sm rounded-lg border-white/10 bg-slate-800 text-white focus:ring-indigo-500 focus:border-indigo-500 py-1.5 pl-3 pr-8 w-full sm:w-auto text-center sm:text-left">
                            <option value="">Select Action...</option>
                            <option value="pending">Move to Pending</option>
                            <option value="publish">Publish All</option>
                            <option value="archive">Archive All</option>
                            <option value="delete">Delete All</option>
                        </select>
                        <button type="submit"
                            class="px-4 py-2 sm:py-1.5 bg-indigo-600 text-white text-[10px] font-black uppercase rounded shadow hover:bg-indigo-700 transition w-full sm:w-auto">Apply</button>
                    </form>
                </div>
                <!-- Direct Entry Dropdown -->
                <div class="relative w-full md:w-auto md:ml-auto" x-data="{ open: false }">
                    <button @click="open = !open" @click.away="open = false" type="button"
                        class="inline-flex items-center justify-center gap-2 px-6 py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-[10px] uppercase tracking-widest rounded-2xl shadow-lg hover:shadow-indigo-500/30 transition transform hover:-translate-y-0.5 w-full md:w-auto whitespace-nowrap">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2">
                            </path>
                        </svg>
                        Direct Entry
                        <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="origin-top-right absolute right-0 mt-2 w-48 rounded-2xl shadow-lg bg-slate-900 ring-1 ring-white/10 z-50 overflow-hidden text-[10px] font-black uppercase tracking-widest"
                        style="display: none;">
                        <a href="{{ route('admin.projects.create') }}"
                            class="block px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-gray-700 hover:text-indigo-600 transition">
                            Single Entry
                        </a>
                        <a href="{{ route('admin.projects.bulk-create') }}"
                            class="block px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-gray-700 hover:text-indigo-600 transition">
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

            <div class="bg-slate-900 overflow-hidden shadow-xl sm:rounded-3xl border border-white/5">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-800/50 border-b border-white/5">
                                <th class="px-6 py-4 w-12"><input type="checkbox"
                                        onchange="document.querySelectorAll('.project-checkbox').forEach(cb => cb.checked = this.checked)"
                                        class="rounded-md border-white/10 bg-slate-800 text-indigo-500 focus:ring-indigo-500 focus:ring-offset-slate-900 shadow-sm">
                                </th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500">
                                    Project Workspace</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500">
                                    Institutional Metadata</th>
                                <th
                                    class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500 text-center">
                                    Project Status</th>
                                <th
                                    class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500 text-right">
                                    Administrative Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-slate-900 divide-y divide-white/5">
                            @foreach($projects as $project)
                                <tr class="hover:bg-indigo-500/[0.02] transition-all duration-300 group">
                                    <td class="px-6 py-4">
                                        <input type="checkbox" name="project_ids[]" value="{{ $project->id }}"
                                            form="bulkActionForm"
                                            class="project-checkbox rounded-md border-white/10 bg-slate-800 text-indigo-500 focus:ring-indigo-500 focus:ring-offset-slate-900 shadow-sm">
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="max-w-md">
                                            <a href="{{ route('projects.show', $project->id) }}"
                                                class="block font-bold text-white group-hover:text-indigo-400 hover:underline transition truncate"
                                                title="View Project Details">
                                                {{ $project->title }}
                                            </a>
                                            <div
                                                class="text-[10px] text-gray-400 uppercase font-bold mt-1 tracking-tighter">
                                                Authors:
                                                {{ $project->authors_list ?: $project->authors->pluck('name')->take(2)->join(', ') }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span
                                                class="text-sm font-bold text-gray-400">{{ $project->adviser->name ?? $project->adviser_name ?? 'None' }}</span>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span
                                                    class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Class
                                                    of {{ $project->year }}</span>
                                                <span
                                                    class="text-[9px] px-2 py-0.5 {{ $project->program === 'BSInT' ? 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20' : 'bg-slate-700 text-slate-300 border-white/10' }} rounded-lg font-black uppercase tracking-tighter border shadow-sm transition-all duration-300">{{ $project->program }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($project->status === 'published')
                                            <span
                                                class="px-3 py-1 bg-emerald-500/10 text-emerald-400 rounded-full text-[10px] font-black uppercase tracking-widest border border-emerald-500/20 shadow-[0_0_15px_-5px_rgba(16,185,129,0.3)]">Published</span>
                                        @elseif($project->status === 'archived')
                                            <span
                                                class="px-3 py-1 bg-slate-800 text-slate-400 rounded-full text-[10px] font-black uppercase tracking-widest border border-white/5">Archived</span>
                                        @else
                                            <span
                                                class="px-3 py-1 bg-amber-500/10 text-amber-500 rounded-full text-[10px] font-black uppercase tracking-widest border border-amber-500/30 shadow-[0_0_15px_-5px_rgba(245,158,11,0.3)]">Review
                                                Pending</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            @if(in_array($project->status, ['pending', 'approved', 'verified']))
                                                <div class="flex flex-col gap-1.5 min-w-[80px]">
                                                    <form method="POST"
                                                        action="{{ route('admin.projects.approve', $project->id) }}"
                                                        onsubmit="return confirm('Confirm this project for publication?');"
                                                        class="w-full">
                                                        @csrf
                                                        <button type="submit"
                                                            class="w-full px-3 py-1.5 bg-emerald-500 hover:bg-emerald-400 text-slate-950 text-[10px] font-black uppercase rounded shadow-[0_4px_15px_-5px_rgba(16,185,129,0.5)] transition transform hover:-translate-y-0.5 active:scale-95"
                                                            title="Approve & Publish Project">
                                                            Confirm
                                                        </button>
                                                    </form>
                                                    <button type="button"
                                                        onclick="const reason = prompt('Please enter the reason for returning this project:'); if(reason) { const f = document.getElementById('reject-form-{{ $project->id }}'); f.querySelector('input[name=rejection_reason]').value = reason; f.submit(); }"
                                                        class="w-full px-3 py-1.5 bg-rose-500 hover:bg-rose-400 text-white text-[10px] font-black uppercase rounded shadow-[0_4px_15px_-5px_rgba(244,63,94,0.5)] transition transform hover:-translate-y-0.5 active:scale-95"
                                                        title="Return for Revision">
                                                        Return
                                                    </button>
                                                    <form id="reject-form-{{ $project->id }}" method="POST"
                                                        action="{{ route('admin.projects.reject', $project->id) }}"
                                                        class="hidden">
                                                        @csrf
                                                        <input type="hidden" name="rejection_reason" value="">
                                                    </form>
                                                </div>
                                            @endif

                                            <div class="flex flex-col gap-1.5">
                                                <a href="{{ route('admin.projects.edit', $project->id) }}"
                                                    class="p-2 bg-slate-800 text-slate-400 hover:text-white hover:bg-indigo-600 rounded-lg border border-white/5 transition-all duration-300 shadow-sm hover:shadow-indigo-500/20"
                                                    title="Edit Metadata">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                        </path>
                                                    </svg>
                                                </a>
                                                <form method="POST"
                                                    action="{{ route('admin.projects.destroy', $project->id) }}"
                                                    onsubmit="return confirm('⚠️ WARNING: This will permanently delete this project and all associated files.\n\nProject: {{ $project->title }}\n\nThis action cannot be undone. Are you sure?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="p-2 bg-slate-800 text-slate-400 hover:text-white hover:bg-rose-600 rounded-lg border border-white/5 transition-all duration-300 shadow-sm hover:shadow-rose-500/20"
                                                        title="Delete Project">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>

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