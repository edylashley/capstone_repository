<section class="space-y-6">
    <p class="text-sm text-slate-400 leading-relaxed font-medium">
        {{ __('Once your account is deleted, all of its resources and data will be permanently purged from the institutional memory. This action is irreversible.') }}
    </p>

    <button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="px-8 py-3.5 bg-red-500/10 hover:bg-red-500/20 text-red-500 font-black rounded-2xl border border-red-500/20 transition-all uppercase text-[10px] tracking-widest"
    >{{ __('Initiate Termination') }}</button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-8 bg-slate-900 border border-white/5 rounded-3xl overflow-hidden">
            @csrf
            @method('delete')

            <h2 class="text-2xl font-black text-white uppercase tracking-tighter mb-4">
                {{ __('Confirm Deletion') }}
            </h2>

            <p class="text-sm text-slate-400 leading-relaxed font-medium mb-8">
                {{ __('Please enter your credentials to authorize the permanent deletion of your profile. All associated metadata and file pointers will be destroyed.') }}
            </p>

            <div class="relative group">
                <label for="password" class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 mb-2 group-focus-within:text-red-400 transition-colors">
                    Confirm Password
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-slate-500 group-focus-within:text-red-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <input id="password" name="password" type="password" 
                        class="block w-full pl-11 pr-4 py-3.5 bg-slate-950/50 border border-white/5 rounded-2xl text-white text-sm font-bold placeholder-slate-600 focus:ring-2 focus:ring-red-500/20 focus:border-red-500/50 transition-all shadow-inner" 
                        placeholder="Enter password to confirm">
                </div>
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-10 flex justify-end gap-4">
                <button type="button" x-on:click="$dispatch('close')" 
                    class="px-6 py-3 text-slate-400 hover:text-white font-black uppercase text-[10px] tracking-widest transition-colors">
                    {{ __('Abort') }}
                </button>

                <button type="submit" class="px-8 py-3.5 bg-red-600 hover:bg-red-700 text-white font-black rounded-2xl shadow-xl shadow-red-900/40 transition-all transform hover:-translate-y-1 uppercase text-[10px] tracking-widest">
                    {{ __('Finalize Purge') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>
