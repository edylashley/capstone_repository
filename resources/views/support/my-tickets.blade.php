<x-app-layout>
    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            {{-- Integrated Header --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-2">
                <div>
                    <h2 class="font-black text-4xl text-white uppercase tracking-tighter leading-none">Support Portal</h2>
                    <p class="text-[10px] text-indigo-400 uppercase tracking-[0.4em] font-black mt-3 opacity-80">Communication Node & Resolution Center</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-slate-900/50 text-slate-400 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-slate-800 hover:text-white transition-all shadow-inner border border-white/5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Dashboard
                    </a>
                </div>
            </div>
            
            @if($tickets->isEmpty())
                <div class="bg-slate-900 rounded-2xl shadow-sm border border-white/5 p-12 text-center">
                    <div class="w-20 h-20 bg-indigo-900/20 rounded-full flex items-center justify-center mx-auto mb-5">
                        <svg class="w-10 h-10 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                    </div>
                    <h3 class="text-lg font-black text-white mb-2">No Tickets Yet</h3>
                    <p class="text-sm text-slate-500 max-w-sm mx-auto">You haven't submitted any support tickets. Use the <span class="font-bold text-indigo-400">?</span> button at the bottom-right corner to submit one.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($tickets as $ticket)
                        <div class="bg-slate-900 rounded-2xl shadow-sm border border-white/5 overflow-hidden hover:shadow-md transition-shadow">
                            <div class="p-5">
                                <div class="flex items-start justify-between gap-3 mb-3">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                                            <h4 class="font-black text-sm text-white">{{ $ticket->subject }}</h4>
                                            <span class="text-[9px] font-bold uppercase px-2 py-0.5 rounded-full border {{ $ticket->status_badge }}">
                                                {{ str_replace('_', ' ', $ticket->status) }}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-bold text-indigo-400 bg-indigo-900/30 px-2 py-0.5 rounded-full border border-indigo-500/20">{{ $ticket->category_label }}</span>
                                            <span class="text-[10px] font-bold text-gray-400">Submitted {{ $ticket->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                    <span class="text-xs font-bold text-gray-300 flex-shrink-0">#{{ $ticket->id }}</span>
                                </div>

                                {{-- Message --}}
                                <div class="bg-slate-950/50 rounded-xl p-4 border border-white/5">
                                    <p class="text-sm text-slate-300 leading-relaxed whitespace-pre-wrap">{{ $ticket->message }}</p>
                                </div>

                                {{-- Status Info --}}
                                @if($ticket->status === 'resolved')
                                    <div class="flex items-center gap-2 mt-3 p-3 bg-green-900/20 rounded-xl border border-green-500/20">
                                        <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <p class="text-xs font-semibold text-green-400">This ticket has been resolved by the administrator.</p>
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
