<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Field -->
        <div>
            <label for="email" class="block text-sm font-semibold text-slate-300 mb-2">
                Email Address
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                    </svg>
                </div>
                <input id="email" 
                       class="block w-full pl-10 pr-4 py-3 border border-slate-700 rounded-xl bg-slate-800/50 text-white placeholder-slate-500 focus:bg-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all" 
                       type="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       autofocus 
                       placeholder="Enter your email" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password Field -->
        <div>
            <label for="password" class="block text-sm font-semibold text-slate-300 mb-2">
                Password
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <input id="password" 
                       class="block w-full pl-10 pr-12 py-3 border border-slate-700 rounded-xl bg-slate-800/50 text-white placeholder-slate-500 focus:bg-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                       type="password" 
                       name="password" 
                       required 
                       autocomplete="current-password"
                       placeholder="Enter your password" />
                <button type="button" 
                        onclick="togglePasswordVisibility()" 
                        class="absolute inset-y-0 right-0 pr-4 flex items-center cursor-pointer z-10 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between">
            <label class="flex items-center">
                <input type="checkbox" name="remember" class="rounded border-slate-700 bg-slate-800 text-indigo-600 shadow-sm focus:ring-indigo-500">
                <span class="ml-2 text-sm text-slate-400">Remember me</span>
            </label>
            @if (Route::has('password.request'))
                <a class="text-sm font-semibold text-indigo-400 hover:text-indigo-300 hover:underline transition-colors" href="{{ route('password.request') }}">
                    Forgot Password?
                </a>
            @endif
        </div>

        <!-- Submit Button -->
        <button type="submit" class="w-full py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition-all duration-200 shadow-lg shadow-indigo-900/30 hover:shadow-xl transform hover:-translate-y-0.5">
            Sign In
        </button>

        <!-- Register Link -->
        <div class="text-center pt-4 border-t border-slate-800">
            <p class="text-sm text-slate-400">
                Don't have an account? 
                <a href="{{ route('register') }}" class="font-semibold text-indigo-400 hover:text-indigo-300 hover:underline transition-colors">
                    Sign up here
                </a>
            </p>
        </div>
    </form>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.956 9.956 0 012.293-3.95m3.161-2.362A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.956 9.956 0 01-4.421 5.568M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />`;
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />`;
            }
        }
    </script>
</x-guest-layout>