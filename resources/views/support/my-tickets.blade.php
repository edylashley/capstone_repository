<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('My Support Tickets') }}
            </h2>
            <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition ease-in-out duration-150">
                Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($tickets->isEmpty())
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                    <div class="w-20 h-20 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-5">
                        <svg class="w-10 h-10 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                    </div>
                    <h3 class="text-lg font-black text-gray-800 mb-2">No Tickets Yet</h3>
                    <p class="text-sm text-gray-500 max-w-sm mx-auto">You haven't submitted any support tickets. Use the <span class="font-bold text-indigo-600">?</span> button at the bottom-right corner to submit one.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($tickets as $ticket)
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                            <div class="p-5">
                                <div class="flex items-start justify-between gap-3 mb-3">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                                            <h4 class="font-black text-sm text-gray-800">{{ $ticket->subject }}</h4>
                                            <span class="text-[9px] font-bold uppercase px-2 py-0.5 rounded-full border {{ $ticket->status_badge }}">
                                                {{ str_replace('_', ' ', $ticket->status) }}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full">{{ $ticket->category_label }}</span>
                                            <span class="text-[10px] font-bold text-gray-400">Submitted {{ $ticket->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                    <span class="text-xs font-bold text-gray-300 flex-shrink-0">#{{ $ticket->id }}</span>
                                </div>

                                {{-- Message --}}
                                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                                    <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $ticket->message }}</p>
                                </div>

                                {{-- Status Info --}}
                                @if($ticket->status === 'resolved')
                                    <div class="flex items-center gap-2 mt-3 p-3 bg-green-50 rounded-xl border border-green-100">
                                        <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <p class="text-xs font-semibold text-green-700">This ticket has been resolved by the administrator.</p>
                                    </div>
                                @elseif($ticket->status === 'pending')
                                    <div class="flex items-center gap-2 mt-3 p-3 bg-amber-50 rounded-xl border border-amber-100">
                                        <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <p class="text-xs font-semibold text-amber-700">Awaiting admin review.</p>
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
