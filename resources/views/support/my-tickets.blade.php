<x-app-layout>
    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            {{-- Integrated Header --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-2">
                <div>
                    <h2 class="font-black text-4xl text-gray-900 dark:text-white uppercase tracking-tighter leading-none">Support Portal</h2>
                    <p class="text-[10px] text-blue-600 dark:text-indigo-400 uppercase tracking-[0.4em] font-black mt-3 opacity-80">Communication Node & Resolution Center</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gray-100 dark:bg-slate-900/50 text-gray-500 dark:text-slate-400 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-gray-200 dark:hover:bg-slate-800 hover:text-gray-900 dark:hover:text-white transition-all shadow-sm dark:shadow-inner border border-gray-200 dark:border-white/5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Dashboard
                    </a>
                </div>
            </div>
            
            @if($tickets->isEmpty())
                <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-200 dark:border-white/5 p-12 text-center transition-colors">
                    <div class="w-20 h-20 bg-blue-50 dark:bg-indigo-900/20 rounded-full flex items-center justify-center mx-auto mb-5">
                        <svg class="w-10 h-10 text-blue-500 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                    </div>
                    <h3 class="text-lg font-black text-gray-900 dark:text-white mb-2">No Tickets Yet</h3>
                    <p class="text-sm text-gray-500 dark:text-slate-500 max-w-sm mx-auto">You haven't submitted any support tickets. Use the <span class="font-bold text-blue-500 dark:text-indigo-400">?</span> button at the bottom-right corner to submit one.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($tickets as $ticket)
                        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-200 dark:border-white/5 overflow-hidden hover:shadow-md transition-all">
                            <div class="p-5">
                                <div class="flex items-start justify-between gap-3 mb-3">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                                            <h4 class="font-black text-sm text-gray-900 dark:text-white">{{ $ticket->subject }}</h4>
                                            <span class="text-[9px] font-bold uppercase px-2 py-0.5 rounded-full border {{ $ticket->status_badge }}">
                                                {{ str_replace('_', ' ', $ticket->status) }}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-bold text-blue-600 dark:text-indigo-400 bg-blue-50 dark:bg-indigo-900/30 px-2 py-0.5 rounded-full border border-blue-200 dark:border-indigo-500/20">{{ $ticket->category_label }}</span>
                                            <span class="text-[10px] font-bold text-gray-500 dark:text-gray-400">Submitted {{ $ticket->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                    <span class="text-xs font-bold text-gray-400 dark:text-gray-300 flex-shrink-0">#{{ $ticket->id }}</span>
                                </div>

                                {{-- Message --}}
                                <div class="bg-gray-50 dark:bg-slate-950/50 rounded-xl p-4 border border-gray-200 dark:border-white/5 transition-colors">
                                    <p class="text-sm text-gray-700 dark:text-slate-300 leading-relaxed whitespace-pre-wrap">{{ $ticket->message }}</p>
                                </div>

                                {{-- Status Info --}}
                                @if($ticket->status === 'resolved')
                                    <div class="mt-3 space-y-3">
                                        <div class="flex items-center gap-2 p-3 bg-emerald-500/10 rounded-xl border border-emerald-500/20">
                                            <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <p class="text-xs font-semibold text-emerald-400">This ticket has been resolved by the administrator.</p>
                                        </div>
                                        
                                        @if($ticket->admin_reply)
                                            <div class="p-6 bg-blue-50/50 dark:bg-indigo-500/5 rounded-2xl border border-blue-100 dark:border-indigo-500/10 border-l-4 border-l-blue-500 dark:border-l-indigo-500/50 shadow-sm dark:shadow-inner transition-colors">
                                                <div class="flex items-center gap-3 mb-4">
                                                    {{-- Premium Badge Icon --}}
                                                    <div class="w-7 h-7 rounded-lg bg-blue-600 dark:bg-indigo-600 flex items-center justify-center shadow-sm dark:shadow-lg dark:shadow-indigo-600/20">
                                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                                        </svg>
                                                    </div>
                                                    <h5 class="text-[10px] font-black uppercase text-blue-600 dark:text-indigo-400 tracking-[0.25em]">Administrator Response</h5>
                                                </div>
                                                <p class="text-sm text-gray-700 dark:text-slate-300 leading-relaxed italic whitespace-pre-wrap pl-1">"{{ $ticket->admin_reply }}"</p>
                                            </div>
                                        @endif
                                    </div>
                                @elseif($ticket->status === 'pending')
                                    <div class="flex items-center gap-2 mt-3 p-3 bg-amber-900/20 rounded-xl border border-amber-500/20">
                                        <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <p class="text-xs font-semibold text-amber-400">Awaiting admin review.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $tickets->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
