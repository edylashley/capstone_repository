<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Support Tickets') }}
            </h2>
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition ease-in-out duration-150">
                Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Stats Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl bg-indigo-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>
                        </div>
                        <div>
                            <p class="text-2xl font-black text-gray-800">{{ $stats['total'] }}</p>
                            <p class="text-[10px] uppercase font-bold tracking-wider text-gray-400">Total</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-amber-100 p-5 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl bg-amber-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-2xl font-black text-amber-600">{{ $stats['pending'] }}</p>
                            <p class="text-[10px] uppercase font-bold tracking-wider text-gray-400">Pending</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-green-100 p-5 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl bg-green-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-2xl font-black text-green-600">{{ $stats['resolved'] }}</p>
                            <p class="text-[10px] uppercase font-bold tracking-wider text-gray-400">Resolved</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filters --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
                <form method="GET" action="{{ route('admin.support.index') }}" class="flex flex-wrap gap-3 items-end">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-[10px] uppercase font-black tracking-wider text-gray-400 mb-1">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Subject, message, or user..." class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-black tracking-wider text-gray-400 mb-1">Status</label>
                        <select name="status" class="rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm">
                            <option value="">All</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>

                            <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-black tracking-wider text-gray-400 mb-1">Category</label>
                        <select name="category" class="rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm">
                            <option value="">All</option>
                            <option value="bug" {{ request('category') === 'bug' ? 'selected' : '' }}>System Bug</option>
                            <option value="correction" {{ request('category') === 'correction' ? 'selected' : '' }}>Record Correction</option>
                            <option value="account" {{ request('category') === 'account' ? 'selected' : '' }}>Account Issue</option>
                            <option value="general" {{ request('category') === 'general' ? 'selected' : '' }}>General</option>
                        </select>
                    </div>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-bold hover:bg-indigo-700 transition-colors shadow-sm">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'status', 'category']))
                        <a href="{{ route('admin.support.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl text-sm font-bold hover:bg-gray-200 transition-colors">
                            Clear
                        </a>
                    @endif
                </form>
            </div>

            {{-- Tickets List --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                @if($tickets->isEmpty())
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                        </div>
                        <p class="text-gray-500 font-bold">No support tickets found.</p>
                        <p class="text-sm text-gray-400 mt-1">Tickets submitted by users will appear here.</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach($tickets as $ticket)
                            <a href="{{ route('admin.support.show', $ticket) }}" class="flex items-center gap-4 p-5 hover:bg-gray-50 transition-colors group">
                                {{-- Status Indicator --}}
                                <div class="flex-shrink-0">
                                    @if($ticket->status === 'pending')
                                        <div class="w-3 h-3 bg-amber-400 rounded-full ring-4 ring-amber-100 animate-pulse"></div>

                                    @else
                                        <div class="w-3 h-3 bg-green-400 rounded-full ring-4 ring-green-100"></div>
                                    @endif
                                </div>

                                {{-- Ticket Info --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <h4 class="font-black text-sm text-gray-800 truncate group-hover:text-indigo-600 transition-colors">{{ $ticket->subject }}</h4>
                                        <span class="flex-shrink-0 text-[9px] font-bold uppercase px-2 py-0.5 rounded-full border {{ $ticket->status_badge }}">
                                            {{ str_replace('_', ' ', $ticket->status) }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500 truncate">{{ Str::limit($ticket->message, 100) }}</p>
                                    <div class="flex items-center gap-3 mt-1.5">
                                        <span class="text-[10px] font-bold text-gray-400">
                                            <span class="text-gray-800">{{ $ticket->user?->name ?? $ticket->email }}</span>
                                        </span>
                                        <span class="text-[10px] font-bold text-indigo-500 bg-indigo-50 px-2 py-0.5 rounded-full">{{ $ticket->category_label }}</span>
                                    </div>
                                </div>

                                {{-- Timestamp --}}
                                <div class="flex-shrink-0 text-right">
                                    <p class="text-xs font-bold text-gray-400">{{ $ticket->created_at->diffForHumans() }}</p>
                                    @if($ticket->replied_at)
                                        <p class="text-[10px] text-green-500 font-bold mt-0.5">✓ Replied</p>
                                    @endif
                                </div>

                                {{-- Arrow --}}
                                <svg class="w-5 h-5 text-gray-300 group-hover:text-indigo-500 transition-colors flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="p-5 border-t border-gray-100">
                        {{ $tickets->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
