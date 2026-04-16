<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">
                Ticket #{{ $ticket->id }}
            </h2>
            <a href="{{ route('admin.support.index') }}" class="px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition ease-in-out duration-150">
                ← All Tickets
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            {{-- Ticket Header Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <div>
                            <h3 class="text-lg font-black text-gray-800">{{ $ticket->subject }}</h3>
                            <div class="flex items-center gap-2 mt-1.5">
                                <span class="text-[10px] font-bold uppercase px-2.5 py-0.5 rounded-full border {{ $ticket->status_badge }}">
                                    {{ str_replace('_', ' ', $ticket->status) }}
                                </span>
                                <span class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2.5 py-0.5 rounded-full border border-indigo-200">
                                    {{ $ticket->category_label }}
                                </span>
                                @if($ticket->expires_at)
                                    <span class="text-[10px] font-bold text-gray-400 bg-gray-50 px-2.5 py-0.5 rounded-full border border-gray-200">
                                        Auto-deletes {{ $ticket->expires_at->diffForHumans() }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Status & Actions --}}
                        <div class="flex items-center gap-2 flex-shrink-0" x-data="{ confirmDelete: false }">
                            @if($ticket->status === 'resolved')
                            <form method="POST" action="{{ route('admin.support.status', $ticket) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="pending">
                                <button type="submit" class="px-4 py-2 text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200 rounded-lg hover:bg-amber-100 transition-colors flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                    Reopen
                                </button>
                            </form>
                            @endif

                            {{-- Delete --}}
                            <button @click="confirmDelete = true" class="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete ticket">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>

                            {{-- Delete Confirm Modal --}}
                            <div x-show="confirmDelete" x-cloak class="fixed inset-0 z-[9999] flex items-center justify-center p-4" style="display: none;">
                                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="confirmDelete = false"></div>
                                <div class="relative bg-white rounded-2xl shadow-2xl max-w-sm w-full p-8 text-center">
                                    <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>
                                    </div>
                                    <h4 class="text-lg font-black text-gray-800 mb-2">Delete Ticket?</h4>
                                    <p class="text-sm text-gray-500 mb-6">This action cannot be undone. The ticket will be permanently removed.</p>
                                    <div class="flex gap-3">
                                        <button @click="confirmDelete = false" class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-bold text-sm hover:bg-gray-200 transition-colors">Cancel</button>
                                        <form method="POST" action="{{ route('admin.support.destroy', $ticket) }}" class="flex-1">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full px-4 py-2.5 bg-red-600 text-white rounded-xl font-bold text-sm hover:bg-red-700 transition-colors">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Submitter Info --}}
                    <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl border border-gray-100">
                        <div class="w-10 h-10 rounded-full bg-indigo-500 flex items-center justify-center text-white font-bold text-sm flex-shrink-0 shadow-sm">
                            {{ substr($ticket->user?->name ?? $ticket->email ?? '?', 0, 1) }}
                        </div>
                        <div>
                            <p class="font-black text-sm text-gray-800">{{ $ticket->user?->name ?? 'Guest User' }}</p>
                            <div class="flex items-center gap-2">
                                <p class="text-xs text-gray-500">{{ $ticket->email ?? $ticket->user?->email ?? 'No email' }}</p>
                                @if($ticket->user?->role)
                                    <span class="text-[9px] font-bold uppercase px-1.5 py-0.5 bg-gray-200 text-gray-600 rounded-full">{{ $ticket->user->role }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="ml-auto text-right">
                            <p class="text-xs font-bold text-gray-500">{{ $ticket->created_at->format('M d, Y') }}</p>
                            <p class="text-[10px] text-gray-400 font-semibold">{{ $ticket->created_at->format('h:i A') }} · {{ $ticket->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Ticket Message --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-5 border-b border-gray-100 bg-gray-50">
                    <h4 class="font-black text-sm text-gray-800 flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>
                        Issue Details
                    </h4>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $ticket->message }}</p>
                </div>
            </div>

            {{-- Admin Reply / Resolve Panel --}}
            @if($ticket->status !== 'resolved')
            <div class="bg-white rounded-2xl shadow-sm border border-indigo-100 overflow-hidden">
                <div class="p-5 border-b border-indigo-50 bg-indigo-50">
                    <h4 class="font-black text-sm text-indigo-800 flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                        Send Reply & Resolve Ticket
                    </h4>
                    <p class="text-xs text-indigo-500 mt-0.5">The user will see this message in the System Support panel. The ticket auto-deletes after 3 days.</p>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.support.status', $ticket) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="resolved">
                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Your Reply to the User <span class="text-red-500">*</span></label>
                            <textarea
                                name="admin_reply"
                                rows="5"
                                required
                                class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm resize-none"
                            >{{ old('admin_reply', "Hello,\n\nWe have received your support ticket regarding '" . $ticket->subject . "'.\n\nWe have looked into the issue and resolved it according to your request. If you have further issues, please don't hesitate to reach out again.\n\nThank you,\nAdmin Team") }}</textarea>
                            <p class="text-[10px] text-gray-400 mt-1 font-semibold">This message will be shown to the user inside the System Support (?) panel.</p>
                        </div>
                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-5 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold text-sm transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Send Reply & Mark Resolved
                        </button>
                    </form>
                </div>
            </div>
            @else
            <div class="bg-white rounded-2xl shadow-sm border border-green-100 overflow-hidden">
                <div class="p-5 border-b border-green-50 bg-green-50">
                    <h4 class="font-black text-sm text-green-800 flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Admin Reply Sent
                    </h4>
                    @if($ticket->expires_at)
                        <p class="text-xs text-green-500 mt-0.5">This ticket will be automatically deleted {{ $ticket->expires_at->diffForHumans() }}.</p>
                    @endif
                </div>
                <div class="p-6">
                    @if($ticket->admin_reply)
                        <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $ticket->admin_reply }}</p>
                    @else
                        <p class="text-sm text-gray-400 italic">No reply message was recorded.</p>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
