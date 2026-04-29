<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Integrated Header --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-2">
                <div>
                    <h2
                        class="font-black text-4xl text-gray-900 dark:text-white uppercase tracking-tighter leading-none">
                        Capstone Projects
                    </h2>
                    <p
                        class="text-[10px] text-blue-600 dark:text-indigo-400 uppercase tracking-[0.4em] font-black mt-3 opacity-80">
                        Review & Manage All Projects</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.dashboard') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-gray-100 dark:bg-slate-900/50 text-gray-500 dark:text-slate-400 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-gray-900 hover:text-white transition-all shadow-sm dark:shadow-inner border border-gray-200 dark:border-white/5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Dashboard
                    </a>
                </div>
            </div>

            <!-- Top Controls -->
            <div class="flex flex-wrap items-center justify-center gap-4">
                <!-- Filter Bar -->
                <div
                    class="bg-white dark:bg-slate-900 p-4 rounded-2xl shadow-sm border border-gray-200 dark:border-white/5 w-full md:w-auto transition-colors">
                    <form method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 m-0">
                        <div class="flex items-center justify-between sm:justify-start gap-2">
                            <label class="text-xs font-bold uppercase text-gray-600 dark:text-slate-400">Status:</label>
                            <select name="status" onchange="this.form.submit()"
                                class="text-sm rounded-lg border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-blue-500 dark:focus:ring-indigo-500 focus:border-blue-500 dark:focus:border-indigo-500 py-1.5 pl-3 pr-10 shadow-sm w-full sm:w-auto text-center sm:text-left transition-colors">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending
                                    Review
                                </option>
                                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>
                                    Published
                                    Records</option>
                                <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived
                                </option>
                                <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned
                                </option>
                            </select>
                        </div>
                        <div
                            class="flex items-center justify-between sm:justify-start gap-2 border-l-0 sm:border-l border-gray-200 dark:border-white/10 pl-0 sm:pl-4 transition-colors">
                            <label
                                class="text-xs font-bold uppercase text-gray-600 dark:text-slate-400">Program:</label>
                            <select name="program" onchange="this.form.submit()"
                                class="text-sm rounded-lg border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-blue-500 dark:focus:ring-indigo-500 focus:border-blue-500 dark:focus:border-indigo-500 py-1.5 pl-3 pr-10 shadow-sm w-full sm:w-auto text-center sm:text-left transition-colors">
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
                <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl shadow-sm border border-gray-200 dark:border-white/5 w-full md:w-auto transition-colors" x-data="{ 
                    open: false, 
                    selectedAction: '', 
                    selectedLabel: 'Select Action...',
                    executeBulkAction() {
                        if (!this.selectedAction) {
                            alert('Please select an action from the dropdown first.');
                            return;
                        }
                        
                        var checkedCount = document.querySelectorAll('.project-checkbox:checked').length;
                        if (checkedCount === 0) {
                            alert('Please select at least one project first.');
                            return;
                        }

                        if (this.selectedAction === 'delete') {
                            Alpine.store('deleteModal').show(
                                'Bulk Delete', 
                                checkedCount + ' selected projects will be removed. How would you like to proceed?', 
                                'bulkActionForm'
                            );
                        } else {
                            if (confirm('Apply ' + this.selectedLabel + ' to ' + checkedCount + ' projects?')) {
                                this.$refs.bulkForm.submit();
                            }
                        }
                    }
                }">
                    <form id="bulkActionForm" x-ref="bulkForm" method="POST" action="{{ route('admin.projects.bulk') }}"
                        class="flex flex-col sm:flex-row items-stretch sm:items-center m-0 gap-3">
                        @csrf
                        <input type="hidden" name="action" x-model="selectedAction">
                        <label class="text-xs font-bold uppercase text-gray-600 dark:text-slate-400 whitespace-nowrap">Bulk Actions</label>
                        
                        {{-- Custom Alpine Dropdown --}}
                        <div class="relative min-w-[200px]">
                            <button @click="open = !open" type="button" 
                                class="w-full flex items-center justify-between gap-3 px-4 py-2.5 bg-gray-50 dark:bg-slate-800/50 border border-gray-200 dark:border-white/5 rounded-xl text-xs font-black uppercase tracking-widest text-gray-900 dark:text-white transition-all hover:border-blue-500 dark:hover:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 shadow-inner">
                                <span x-text="selectedLabel"></span>
                                <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <div x-show="open" x-cloak @click.away="open = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                                class="absolute left-0 top-full mt-2 w-full bg-white dark:bg-slate-900 border border-gray-200 dark:border-white/10 rounded-2xl shadow-2xl z-[100] overflow-hidden py-1 transition-colors duration-300"
                                style="display: none;">
                                
                                {{-- Pending --}}
                                <button type="button" @click="selectedAction = 'pending'; selectedLabel = 'Move to Pending'; open = false" 
                                    class="w-full text-center px-4 py-3 text-[10px] font-black uppercase tracking-widest transition-all flex items-center justify-center gap-3 group hover:bg-amber-500 hover:text-white dark:text-gray-300">
                                    <span>Move to Pending</span>
                                    <div x-show="selectedAction === 'pending'" class="w-1.5 h-1.5 rounded-full bg-amber-500 group-hover:bg-white"></div>
                                </button>

                                {{-- Publish --}}
                                <button type="button" @click="selectedAction = 'publish'; selectedLabel = 'Publish All'; open = false" 
                                    class="w-full text-center px-4 py-3 text-[10px] font-black uppercase tracking-widest transition-all flex items-center justify-center gap-3 group hover:bg-emerald-500 hover:text-white dark:text-gray-300">
                                    <span>Publish All</span>
                                    <div x-show="selectedAction === 'publish'" class="w-1.5 h-1.5 rounded-full bg-emerald-500 group-hover:bg-white"></div>
                                </button>

                                {{-- Archive --}}
                                <button type="button" @click="selectedAction = 'archive'; selectedLabel = 'Archive All'; open = false" 
                                    class="w-full text-center px-4 py-3 text-[10px] font-black uppercase tracking-widest transition-all flex items-center justify-center gap-3 group hover:bg-gray-500 hover:text-white dark:text-gray-300">
                                    <span>Archive All</span>
                                    <div x-show="selectedAction === 'archive'" class="w-1.5 h-1.5 rounded-full bg-gray-400 group-hover:bg-white"></div>
                                </button>

                                {{-- Delete --}}
                                <button type="button" @click="selectedAction = 'delete'; selectedLabel = 'Delete All'; open = false" 
                                    class="w-full text-center px-4 py-3 text-[10px] font-black uppercase tracking-widest transition-all flex items-center justify-center gap-3 group hover:bg-rose-500 hover:text-white dark:text-gray-300">
                                    <span>Delete All</span>
                                    <div x-show="selectedAction === 'delete'" class="w-1.5 h-1.5 rounded-full bg-rose-500 group-hover:bg-white"></div>
                                </button>
                            </div>
                        </div>

                        <button type="button" @click="executeBulkAction()"
                            class="px-6 py-2 bg-blue-600 dark:bg-indigo-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-lg shadow-blue-500/20 dark:shadow-indigo-500/20 hover:bg-blue-700 dark:hover:bg-indigo-700 transition-all active:scale-95">
                            Apply
                        </button>
                    </form>
                </div>
                <!-- Direct Entry Dropdown -->
                <div class="relative w-full md:w-auto" x-data="{ open: false }">
                    <button @click="open = !open" @click.away="open = false" type="button"
                        class="inline-flex items-center justify-center gap-2 px-6 py-4 bg-blue-600 dark:bg-indigo-600 hover:bg-blue-700 dark:hover:bg-indigo-700 text-white font-black text-[10px] uppercase tracking-widest rounded-2xl shadow-lg hover:shadow-blue-500/30 dark:hover:shadow-indigo-500/30 transition transform hover:-translate-y-0.5 w-full md:w-auto whitespace-nowrap">
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
                        class="origin-top-right absolute right-0 mt-2 w-48 rounded-2xl shadow-lg bg-white dark:bg-slate-900 ring-1 ring-gray-200 dark:ring-white/10 border border-gray-200 dark:border-white/5 z-50 overflow-hidden text-[10px] font-black uppercase tracking-widest transition-colors"
                        style="display: none;">
                        <a href="{{ route('admin.projects.create') }}"
                            class="block px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-indigo-600 transition">
                            Single Entry
                        </a>
                        <a href="{{ route('admin.projects.bulk-create') }}"
                            class="block px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-indigo-600 transition">
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

            <div x-data="projectPolling()"
                class="bg-white dark:bg-slate-900 overflow-hidden shadow-sm dark:shadow-xl sm:rounded-3xl border border-gray-200 dark:border-white/5 transition-colors">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr
                                class="bg-gray-50 dark:bg-slate-800/50 border-b border-gray-200 dark:border-white/5 transition-colors">
                                <th class="px-6 py-4 w-12"><input type="checkbox"
                                        onchange="document.querySelectorAll('.project-checkbox').forEach(cb => cb.checked = this.checked)"
                                        class="rounded-md border-gray-400 dark:border-slate-600 bg-white dark:bg-slate-800 text-blue-600 dark:text-indigo-500 focus:ring-blue-500 dark:focus:ring-indigo-500 focus:ring-offset-white dark:focus:ring-offset-slate-900 shadow-sm transition-colors">
                                </th>
                                <th
                                    class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-slate-500">
                                    Project Title</th>
                                <th
                                    class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-slate-500">
                                    Project Information</th>
                                <th
                                    class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-slate-500 text-center">
                                    Project Status</th>
                                <th
                                    class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-slate-500 text-right">
                                    Administrative Actions</th>
                            </tr>
                        </thead>
                        <tbody id="projects-table-body" class="divide-y divide-gray-200 dark:divide-white/5">
                            @foreach($projects as $project)
                                <tr
                                    class="hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors duration-300 group">
                                    <td class="px-6 py-4">
                                        <input type="checkbox" name="project_ids[]" value="{{ $project->id }}"
                                            form="bulkActionForm"
                                            class="project-checkbox rounded-md border-gray-400 dark:border-slate-600 bg-white dark:bg-slate-800 text-blue-600 dark:text-indigo-500 focus:ring-blue-500 dark:focus:ring-indigo-500 focus:ring-offset-white dark:focus:ring-offset-slate-900 shadow-sm transition-colors">
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="max-w-md">
                                            <div class="flex items-center gap-2 max-w-full overflow-hidden">
                                                <a href="{{ route('projects.show', $project->id) }}"
                                                    class="font-bold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-indigo-400 hover:underline transition-colors truncate"
                                                    title="View Project Details">
                                                    {{ $project->title }}
                                                </a>

                                                {{-- Broken File Warning --}}
                                                @php
                                                    $isMissingFile = false;
                                                    foreach ($project->files as $f) {
                                                        if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($f->path)) {
                                                            $isMissingFile = true;
                                                            break;
                                                        }
                                                    }
                                                @endphp
                                                @if($isMissingFile)
                                                    <div class="flex-shrink-0 group/warn relative">
                                                        <span
                                                            class="flex h-5 w-5 items-center justify-center rounded-full bg-rose-500 text-white animate-pulse"
                                                            title="Missing Physical File">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="3"
                                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z">
                                                                </path>
                                                            </svg>
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div
                                                class="text-[10px] text-gray-500 dark:text-gray-400 uppercase font-bold mt-1 tracking-tighter">
                                                Authors:
                                                {{ $project->authors_list ?: $project->authors->pluck('name')->take(2)->join(', ') }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span
                                                class="text-sm font-bold text-gray-800 dark:text-gray-400">{{ $project->adviser->name ?? $project->adviser_name ?? 'None' }}</span>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span
                                                    class="text-[10px] text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest">Class
                                                    of {{ $project->year }}</span>
                                                <span
                                                    class="text-[9px] px-2 py-0.5 {{ $project->program === 'BSInT' ? 'bg-blue-50 dark:bg-indigo-500/10 text-blue-600 dark:text-indigo-400 border-blue-200 dark:border-indigo-500/20' : (stripos($project->program, 'COM-SCI') !== false || stripos($project->program, 'CS') !== false ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-200 dark:border-emerald-500/20' : 'bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300 border-gray-200 dark:border-slate-700') }} rounded-lg font-black uppercase tracking-tighter border shadow-sm transition-all duration-300">{{ $project->program }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($project->status === 'published')
                                            <span
                                                class="px-3 py-1 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-full text-[10px] font-black uppercase tracking-widest border border-emerald-200 dark:border-emerald-500/20 shadow-sm dark:shadow-[0_0_15px_-5px_rgba(16,185,129,0.3)]">Published</span>
                                        @elseif($project->status === 'archived')
                                            <span
                                                class="px-3 py-1 bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-slate-400 rounded-full text-[10px] font-black uppercase tracking-widest border border-gray-200 dark:border-white/5">Archived</span>
                                        @elseif($project->status === 'returned')
                                            <span
                                                class="px-3 py-1 bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-500 rounded-full text-[10px] font-black uppercase tracking-widest border border-rose-200 dark:border-rose-500/20 shadow-sm dark:shadow-[0_0_15px_-5px_rgba(244,63,94,0.3)]">Returned</span>
                                        @else
                                            <span
                                                class="px-3 py-1 bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-500 rounded-full text-[10px] font-black uppercase tracking-widest border border-amber-200 dark:border-amber-500/30 shadow-sm dark:shadow-[0_0_15px_-5px_rgba(245,158,11,0.3)]">Pending</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            @if(!in_array($project->status, ['published', 'archived', 'returned']))
                                                <div class="flex flex-col gap-1.5 min-w-[80px]">
                                                    <form method="POST"
                                                        action="{{ route('admin.projects.approve', $project->id) }}"
                                                        onsubmit="return confirm('Confirm this project for publication?');"
                                                        class="w-full">
                                                        @csrf
                                                        <button type="submit"
                                                            class="w-full px-3 py-1.5 bg-emerald-500 hover:bg-emerald-600 dark:hover:bg-emerald-400 text-white dark:text-slate-950 text-[10px] font-black uppercase rounded shadow-[0_4px_15px_-5px_rgba(16,185,129,0.5)] transition transform hover:-translate-y-0.5 active:scale-95"
                                                            title="Approve & Publish Project">
                                                            Confirm
                                                        </button>
                                                    </form>
                                                    <button type="button"
                                                        onclick="const reason = prompt('Please enter the reason for returning this project:'); if(reason) { const f = document.getElementById('return-form-{{ $project->id }}'); f.querySelector('input[name=return_reason]').value = reason; f.submit(); }"
                                                        class="w-full px-3 py-1.5 bg-rose-500 hover:bg-rose-600 dark:hover:bg-rose-400 text-white text-[10px] font-black uppercase rounded shadow-[0_4px_15px_-5px_rgba(244,63,94,0.5)] transition transform hover:-translate-y-0.5 active:scale-95"
                                                        title="Return for Revision">
                                                        Return
                                                    </button>
                                                    <form id="return-form-{{ $project->id }}" method="POST"
                                                        action="{{ route('admin.projects.return', $project->id) }}"
                                                        class="hidden">
                                                        @csrf
                                                        <input type="hidden" name="return_reason" value="">
                                                    </form>
                                                </div>
                                            @endif

                                            <div class="flex flex-col gap-1.5">
                                                <a href="{{ route('admin.projects.edit', $project->id) }}"
                                                    class="p-2 bg-gray-100 dark:bg-slate-800 text-gray-500 dark:text-slate-400 hover:text-white border-gray-200 dark:border-white/5 hover:bg-blue-600 dark:hover:bg-indigo-600 rounded-lg border transition-all duration-300 shadow-sm hover:shadow-blue-500/20 dark:hover:shadow-indigo-500/20"
                                                    title="Edit Metadata">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                        </path>
                                                    </svg>
                                                </a>
                                                <form id="delete-project-{{ $project->id }}" method="POST"
                                                    action="{{ route('admin.projects.destroy', $project->id) }}"
                                                    @submit.prevent="$store.deleteModal.show('Delete Project', '{{ addslashes($project->title) }} will be removed. How would you like to proceed?', 'delete-project-{{ $project->id }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="p-2 bg-gray-100 dark:bg-slate-800 text-gray-500 dark:text-slate-400 hover:text-white hover:bg-rose-600 rounded-lg border border-gray-200 dark:border-white/5 transition-all duration-300 shadow-sm hover:shadow-rose-500/20"
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
                    <div id="projects-pagination"
                        class="px-6 py-4 bg-gray-50 dark:bg-slate-900 border-t border-gray-200 dark:border-white/5 transition-colors">
                        {{ $projects->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('projectPolling', () => ({
                pollInterval: null,
                init() { this.startPolling(); },
                startPolling() {
                    this.pollInterval = setInterval(() => { this.fetchUpdate(); }, 5000);
                },
                async fetchUpdate() {
                    try {
                        const response = await fetch(window.location.href, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        if (!response.ok) return;
                        const html = await response.text();
                        const doc = new DOMParser().parseFromString(html, 'text/html');
                        ['projects-table-body', 'projects-pagination'].forEach(id => {
                            const newEl = doc.getElementById(id);
                            const currentEl = document.getElementById(id);
                            if (newEl && currentEl && currentEl.innerHTML !== newEl.innerHTML) {
                                currentEl.innerHTML = newEl.innerHTML;
                            }
                        });
                    } catch (e) { console.error('Project polling failed:', e); }
                }
            }));
        });
    </script>
</x-app-layout>