<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Integrated Header --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-2">
                <div>
                    <h2 class="font-black text-4xl text-white uppercase tracking-tighter leading-none">Support Tickets
                    </h2>
                    <p class="text-[10px] text-indigo-400 uppercase tracking-[0.4em] font-black mt-3 opacity-80">Manage
                        user inquiries and technical assistance</p>
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

            {{-- Stats Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-8">
                <div
                    class="bg-slate-900 rounded-2xl shadow-sm border border-white/5 p-5 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl bg-slate-800 flex items-center justify-center">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-2xl font-black text-white">{{ $stats['total'] }}</p>
                            <p class="text-[10px] uppercase font-bold tracking-wider text-slate-500">Total Inquiry</p>
                        </div>
                    </div>
                </div>
                <div
                    class="bg-slate-900 rounded-2xl shadow-sm border border-amber-500/20 p-5 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl bg-amber-500/10 flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-2xl font-black text-amber-500">{{ $stats['pending'] }}</p>
                            <p class="text-[10px] uppercase font-bold tracking-wider text-slate-500">Waitlisted</p>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-slate-900 rounded-2xl shadow-sm border border-emerald-500/20 p-5 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl bg-emerald-500/10 flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-2xl font-black text-emerald-500">{{ $stats['resolved'] }}</p>
                            <p class="text-[10px] uppercase font-bold tracking-wider text-slate-500">Completed</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filters --}}
            <div class="bg-slate-900 rounded-2xl shadow-sm border border-white/5 p-5 mb-6">
                <form method="GET" action="{{ route('admin.support.index') }}" class="flex flex-wrap gap-3 items-end">
                    <div class="flex-1 min-w-[200px]">
                        <label
                            class="block text-[10px] uppercase font-black tracking-wider text-slate-500 mb-1">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Subject, message, or user..."
                            class="w-full rounded-xl border-white/5 bg-slate-950 text-white focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm placeholder-slate-600">
                    </div>
                    <div>
                        <label
                            class="block text-[10px] uppercase font-black tracking-wider text-slate-500 mb-1">Status</label>
                        <select name="status"
                            class="rounded-xl border-white/5 bg-slate-950 text-white focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm">
                            <option value="">All</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending
                            </option>
                            <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved
                            </option>
                        </select>
                    </div>
                    <div>
                        <label
                            class="block text-[10px] uppercase font-black tracking-wider text-slate-500 mb-1">Category</label>
                        <select name="category"
                            class="rounded-xl border-white/5 bg-slate-950 text-white focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm">
                            <option value="">All</option>
                            <option value="bug" {{ request('category') === 'bug' ? 'selected' : '' }}>System Bug</option>
                            <option value="correction" {{ request('category') === 'correction' ? 'selected' : '' }}>Record
                                Correction</option>
                            <option value="account" {{ request('category') === 'account' ? 'selected' : '' }}>Account Issue</option>
                            <option value="general" {{ request('category') === 'general' ? 'selected' : '' }}>General</option>
                            <option value="others" {{ request('category') === 'others' ? 'selected' : '' }}>Others</option>
                        </select>
                    </div>
                    <button type="submit"
                        class="px-5 py-2.5 bg-slate-900 text-white rounded-xl text-sm font-bold hover:bg-black transition-colors shadow-sm">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'status', 'category']))
                        <a href="{{ route('admin.support.index') }}"
                            class="px-5 py-2.5 bg-slate-800 text-slate-300 rounded-xl text-sm font-bold hover:bg-slate-700 transition-colors">
                            Clear
                        </a>
                    @endif
                </form>
            </div>

            {{-- Tickets List --}}
            <div class="bg-slate-900 rounded-2xl shadow-sm border border-white/5 overflow-hidden" 
                 x-data="{ 
                    selected: [], 
                    allSelected: false,
                    toggleAll() {
                        if (this.allSelected) {
                            this.selected = @js($tickets->pluck('id'));
                        } else {
                            this.selected = [];
                        }
                    }
                 }">
                
                {{-- Bulk Action Bar --}}
                <div x-show="selected.length > 0" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 -translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="p-4 bg-indigo-600 border-b border-indigo-500 flex items-center justify-between">
                    <p class="text-xs font-black text-white uppercase tracking-widest">
                        <span x-text="selected.length"></span> Tickets Selected
                    </p>
                    <form action="{{ route('admin.support.bulk-delete') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete these ' + selected.length + ' tickets? This cannot be undone.')">
                        @csrf
                        <template x-for="id in selected" :key="id">
                            <input type="hidden" name="ids[]" :value="id">
                        </template>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-red-700 transition-all shadow-lg flex items-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Delete Selected
                        </button>
                    </form>
                </div>

                @if($tickets->isEmpty())
                    <div class="p-12 text-center bg-slate-950">
                        <div
                            class="w-16 h-16 bg-slate-900 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-white/5 shadow-inner">
                            <svg class="w-8 h-8 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                </path>
                            </svg>
                        </div>
                        <p class="text-white font-black uppercase tracking-widest text-sm">Clear Horizons</p>
                        <p class="text-xs text-slate-500 mt-2">No support inquiries require immediate intervention.</p>
                    </div>
                @else
                    {{-- Select All Header --}}
                    <div class="p-4 bg-slate-950/50 border-b border-white/5 flex items-center gap-4">
                        <div class="flex items-center">
                            <input type="checkbox" x-model="allSelected" @change="toggleAll()" class="w-4 h-4 rounded border-white/10 bg-slate-900 text-indigo-600 focus:ring-indigo-500">
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">Selection Matrix / Archive Control</span>
                    </div>

                    <div class="divide-y divide-white/5">
                        @foreach($tickets as $ticket)
                            <div class="relative flex items-center gap-4 p-5 hover:bg-white/[0.02] transition-colors group">
                                {{-- Checkbox --}}
                                <div class="flex-shrink-0 z-10">
                                    <input type="checkbox" :value="{{ $ticket->id }}" x-model="selected" class="w-4 h-4 rounded border-white/10 bg-slate-900 text-indigo-600 focus:ring-indigo-500">
                                </div>

                                {{-- Status Indicator --}}
                                <div class="flex-shrink-0 cursor-pointer" onclick="window.location.href='{{ route('admin.support.show', $ticket) }}'">
                                    @if($ticket->category === 'security')
                                        <div class="w-3 h-3 bg-rose-500 rounded-full ring-4 ring-rose-500/20"></div>
                                    @elseif($ticket->status === 'pending')
                                        <div class="w-3 h-3 bg-amber-500 rounded-full ring-4 ring-amber-500/20 animate-pulse"></div>
                                    @else
                                        <div class="w-3 h-3 bg-emerald-500 rounded-full ring-4 ring-emerald-500/20"></div>
                                    @endif
                                </div>

                                {{-- Ticket Info --}}
                                <a href="{{ route('admin.support.show', $ticket) }}" class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <h4 class="font-black text-sm text-white truncate group-hover:text-indigo-400 transition-colors">
                                            {{ $ticket->category_label }}</h4>
                                        <span
                                            class="flex-shrink-0 text-[9px] font-bold uppercase px-2 py-0.5 rounded-full border {{ $ticket->status_badge }}">
                                            {{ $ticket->status_label }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-500 truncate">{{ $ticket->subject }}</p>
                                    <div class="flex items-center gap-3 mt-1.5">
                                        <span class="text-[10px] font-bold text-slate-500">
                                            <span class="text-slate-300">{{ $ticket->user?->name ?? $ticket->email }}</span>
                                        </span>
                                    </div>
                                </a>

                                {{-- Actions --}}
                                <div class="flex items-center gap-4">
                                    {{-- Timestamp --}}
                                    <div class="hidden sm:block flex-shrink-0 text-right">
                                        <p class="text-xs font-bold text-gray-400">{{ $ticket->created_at->diffForHumans() }}</p>
                                        @if($ticket->replied_at)
                                            <p class="text-[10px] text-green-500 font-bold mt-0.5">✓ Replied</p>
                                        @endif
                                    </div>

                                    {{-- Individual Delete --}}
                                    <form action="{{ route('admin.support.destroy', $ticket) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this ticket?')" class="flex-shrink-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-600 hover:text-red-500 hover:bg-red-500/10 rounded-lg transition-all opacity-0 group-hover:opacity-100 focus:opacity-100" title="Delete Ticket">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>

                                    {{-- Arrow --}}
                                    <a href="{{ route('admin.support.show', $ticket) }}" class="flex-shrink-0">
                                        <svg class="w-5 h-5 text-slate-700 group-hover:text-white transition-all transform group-hover:translate-x-1 duration-300"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7">
                                            </path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="p-5 border-t border-white/5">
                        {{ $tickets->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>