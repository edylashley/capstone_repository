<section>
    <header class="hidden">
        <h2 class="text-lg font-medium text-slate-900">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-slate-600">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-8">
        @csrf
        @method('put')

        {{-- Current Password --}}
        <div class="relative group">
            <label for="update_password_current_password" class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 mb-2 group-focus-within:text-indigo-400 transition-colors">
                Current Password
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-slate-500 group-focus-within:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <input id="update_password_current_password" name="current_password" type="password" 
                    class="block w-full pl-11 pr-4 py-3.5 bg-gray-50 dark:bg-slate-950/50 border border-gray-200 dark:border-white/5 rounded-2xl text-gray-900 dark:text-white text-sm font-bold placeholder-gray-400 dark:placeholder-slate-600 focus:ring-2 focus:ring-blue-500/20 dark:focus:ring-indigo-500/20 focus:border-blue-500/50 dark:focus:border-indigo-500/50 transition-all shadow-sm dark:shadow-inner" 
                    autocomplete="current-password" placeholder="Enter current password">
            </div>
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        {{-- New Password --}}
        <div class="relative group">
            <label for="update_password_password" class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 mb-2 group-focus-within:text-indigo-400 transition-colors">
                New Password
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-slate-500 group-focus-within:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <input id="update_password_password" name="password" type="password" 
                    class="block w-full pl-11 pr-4 py-3.5 bg-gray-50 dark:bg-slate-950/50 border border-gray-200 dark:border-white/5 rounded-2xl text-gray-900 dark:text-white text-sm font-bold placeholder-gray-400 dark:placeholder-slate-600 focus:ring-2 focus:ring-blue-500/20 dark:focus:ring-indigo-500/20 focus:border-blue-500/50 dark:focus:border-indigo-500/50 transition-all shadow-sm dark:shadow-inner" 
                    autocomplete="new-password" placeholder="New strong password">
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        {{-- Confirm Password --}}
        <div class="relative group">
            <label for="update_password_password_confirmation" class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 mb-2 group-focus-within:text-indigo-400 transition-colors">
                Confirm Password
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-slate-500 group-focus-within:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <input id="update_password_password_confirmation" name="password_confirmation" type="password" 
                    class="block w-full pl-11 pr-4 py-3.5 bg-gray-50 dark:bg-slate-950/50 border border-gray-200 dark:border-white/5 rounded-2xl text-gray-900 dark:text-white text-sm font-bold placeholder-gray-400 dark:placeholder-slate-600 focus:ring-2 focus:ring-blue-500/20 dark:focus:ring-indigo-500/20 focus:border-blue-500/50 dark:focus:border-indigo-500/50 transition-all shadow-sm dark:shadow-inner" 
                    autocomplete="new-password" placeholder="Re-type new password">
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-6 pt-4">
            <button type="submit" class="px-10 py-4 bg-blue-600 hover:bg-blue-700 dark:bg-indigo-600 dark:hover:bg-indigo-700 text-white font-black rounded-2xl shadow-lg shadow-blue-900/20 dark:shadow-xl dark:shadow-indigo-900/40 transition-all transform hover:-translate-y-1 active:scale-95 uppercase text-xs tracking-widest">
                Authorize Update
            </button>

            @if (session('status') === 'password-updated')
                <div
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-x-4"
                    x-transition:enter-end="opacity-100 translate-x-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-x-0"
                    x-transition:leave-end="opacity-0 translate-x-4"
                    x-init="setTimeout(() => show = false, 3000)"
                    class="flex items-center gap-2 text-emerald-400 font-black text-[10px] uppercase tracking-widest"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    Cipher Updated
                </div>
            @endif
        </div>
    </form>
</section>
