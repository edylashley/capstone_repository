<section>
    <header class="hidden">
        <h2 class="text-lg font-medium text-slate-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-slate-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-8">
        @csrf
        @method('patch')

        {{-- Name --}}
        <div class="relative group">
            <label for="name" class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 mb-2 group-focus-within:text-indigo-400 transition-colors">
                Full Name
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-slate-500 group-focus-within:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <input id="name" name="name" type="text" 
                    class="block w-full pl-11 pr-4 py-3.5 bg-slate-950/50 border border-white/5 rounded-2xl text-white text-sm font-bold placeholder-slate-600 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500/50 transition-all shadow-inner" 
                    value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" placeholder="Full Name">
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        {{-- ID Number --}}
        <div class="relative group">
            <label for="student_id" class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 mb-2">
                ID Number
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                </div>
                <input id="student_id" name="student_id" type="text" 
                    class="block w-full pl-11 pr-4 py-3.5 bg-slate-950/50 border border-white/5 rounded-2xl text-slate-400 text-sm font-bold cursor-not-allowed shadow-inner" 
                    value="{{ old('student_id', $user->student_id) }}" readonly placeholder="9-digit ID">
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
            </div>
            @if($user->isStudent())
                <p class="text-[9px] text-slate-600 mt-2 font-bold uppercase tracking-widest italic flex items-center gap-1.5">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Field locked for data integrity.
                </p>
            @endif
            <x-input-error class="mt-2" :messages="$errors->get('student_id')" />
        </div>

        {{-- Program --}}
        <div class="relative group">
            <label for="program" class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 mb-2 group-focus-within:text-indigo-400 transition-colors">
                Degree Program
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-slate-500 group-focus-within:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <select id="program" name="program" 
                    class="block w-full pl-11 pr-4 py-3.5 bg-slate-950/50 border border-white/5 rounded-2xl text-white text-sm font-bold focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500/50 transition-all appearance-none shadow-inner">
                    <option value="" disabled {{ $user->program ? '' : 'selected' }}>Select Designation</option>
                    <option value="BSInT" {{ old('program', $user->program) == 'BSInT' ? 'selected' : '' }}>BS in Information Technology</option>
                    <option value="Com-Sci" {{ old('program', $user->program) == 'Com-Sci' ? 'selected' : '' }}>BS in Computer Science</option>
                </select>
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('program')" />
        </div>

        {{-- Email --}}
        <div class="relative group">
            <label for="email" class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 mb-2 group-focus-within:text-indigo-400 transition-colors">
                Email Address
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-slate-500 group-focus-within:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <input id="email" name="email" type="email" 
                    class="block w-full pl-11 pr-4 py-3.5 bg-slate-950/50 border border-white/5 rounded-2xl text-white text-sm font-bold placeholder-slate-600 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500/50 transition-all shadow-inner" 
                    value="{{ old('email', $user->email) }}" required autocomplete="username" placeholder="name@domain.com">
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <div class="flex items-center gap-6 pt-4">
            <button type="submit" class="px-10 py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-2xl shadow-xl shadow-indigo-900/40 transition-all transform hover:-translate-y-1 active:scale-95 uppercase text-xs tracking-widest">
                Update Identity
            </button>

            @if (session('status') === 'profile-updated')
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
                    Sync Complete
                </div>
            @endif
        </div>
    </form>
</section>
