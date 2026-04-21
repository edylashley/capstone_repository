<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            {{-- Integrated Header --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-2">
                <div>
                    <h2 class="font-black text-4xl text-white uppercase tracking-tighter leading-none">System Profile</h2>
                    <p class="text-[10px] text-indigo-400 uppercase tracking-[0.4em] font-black mt-3 opacity-80">Security Protocol & Identity Registry</p>
                </div>
                <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 px-6 py-3 bg-slate-900/50 text-slate-400 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-slate-800 hover:text-white transition-all shadow-inner border border-white/5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Go Back
                </a>
            </div>
            
            {{-- Profile Information Card --}}
            <div class="bg-slate-900 rounded-3xl shadow-2xl border border-white/5 overflow-hidden">
                <div class="px-8 py-6 border-b border-white/5 bg-slate-950/50">
                    <h3 class="text-xl font-black text-white uppercase tracking-widest">Profile Information</h3>
                    <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mt-1.5">Manage your public
                        identity and institutional record.</p>
                </div>
                <div class="p-8 max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- Security Protocol Card --}}
            <div class="bg-slate-900 rounded-3xl shadow-2xl border border-white/5 overflow-hidden">
                <div class="px-8 py-6 border-b border-white/5 bg-slate-950/50">
                    <h3 class="text-xl font-black text-white uppercase tracking-widest">Update Password</h3>
                    <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mt-1.5">Secure your account with
                        a high-entropy credential.</p>
                </div>
                <div class="p-8 max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

        </div>
    </div>
</x-app-layout>