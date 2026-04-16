<x-guest-layout>
    <div class="text-center mb-6">
        <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
        </div>
        <h2 class="text-xl font-bold text-gray-800">Verify Your Email</h2>
        <p class="text-sm text-gray-500 mt-2">
            We've sent a 6-digit verification code to
        </p>
        <p class="text-sm font-semibold text-indigo-600 mt-1">{{ $email }}</p>
    </div>

    @if(isset($resent) && $resent)
        <div class="bg-green-50 border border-green-200 rounded-xl p-3 mb-4">
            <p class="text-xs text-green-700 text-center font-medium">
                ✓ A new verification code has been sent to your email.
            </p>
        </div>
    @endif

    <form method="POST" action="{{ route('register.verify-code') }}" class="space-y-5">
        @csrf

        <!-- Verification Code Input -->
        <div>
            <label for="code" class="block text-sm font-semibold text-gray-700 mb-2">
                Verification Code
            </label>
            <div class="flex justify-center gap-2" id="code-inputs">
                <input type="text" maxlength="1" class="code-digit w-12 h-14 text-center text-xl font-bold border-2 border-gray-300 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all" data-index="0" inputmode="numeric" pattern="[0-9]" autocomplete="off" />
                <input type="text" maxlength="1" class="code-digit w-12 h-14 text-center text-xl font-bold border-2 border-gray-300 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all" data-index="1" inputmode="numeric" pattern="[0-9]" autocomplete="off" />
                <input type="text" maxlength="1" class="code-digit w-12 h-14 text-center text-xl font-bold border-2 border-gray-300 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all" data-index="2" inputmode="numeric" pattern="[0-9]" autocomplete="off" />
                <div class="flex items-center">
                    <span class="text-gray-400 font-bold text-xl">–</span>
                </div>
                <input type="text" maxlength="1" class="code-digit w-12 h-14 text-center text-xl font-bold border-2 border-gray-300 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all" data-index="3" inputmode="numeric" pattern="[0-9]" autocomplete="off" />
                <input type="text" maxlength="1" class="code-digit w-12 h-14 text-center text-xl font-bold border-2 border-gray-300 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all" data-index="4" inputmode="numeric" pattern="[0-9]" autocomplete="off" />
                <input type="text" maxlength="1" class="code-digit w-12 h-14 text-center text-xl font-bold border-2 border-gray-300 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all" data-index="5" inputmode="numeric" pattern="[0-9]" autocomplete="off" />
            </div>
            <!-- Hidden field to hold the complete code -->
            <input type="hidden" name="code" id="verification-code" />
            <x-input-error :messages="$errors->get('code')" class="mt-2 text-center" />
        </div>

        <!-- Timer -->
        <div class="text-center">
            <p class="text-xs text-gray-400" id="timer-text">
                Code expires in <span id="countdown" class="font-semibold text-indigo-600">--:--</span>
            </p>
        </div>

        <!-- Verify Button -->
        <button type="submit" id="verify-btn" class="w-full py-3.5 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white font-bold rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none disabled:hover:shadow-lg" disabled>
            Verify & Create Account
        </button>
    </form>

    <!-- Resend Code -->
    <div class="text-center mt-5 pt-4 border-t border-gray-200">
        <p class="text-sm text-gray-500 mb-2">Didn't receive the code?</p>
        <form method="POST" action="{{ route('register.resend-code') }}" class="inline">
            @csrf
            <button type="submit" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 hover:underline transition-colors">
                Resend Code
            </button>
        </form>
    </div>

    <!-- Back to Registration -->
    <div class="text-center mt-3">
        <a href="{{ route('register') }}" class="text-xs text-gray-400 hover:text-gray-600 transition-colors">
            ← Back to registration
        </a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const digits = document.querySelectorAll('.code-digit');
            const hiddenInput = document.getElementById('verification-code');
            const verifyBtn = document.getElementById('verify-btn');

            function updateHiddenInput() {
                let code = '';
                digits.forEach(d => code += d.value);
                hiddenInput.value = code;
                verifyBtn.disabled = code.length !== 6;
            }

            digits.forEach((input, idx) => {
                // Handle regular input
                input.addEventListener('input', function (e) {
                    // Only allow digits
                    this.value = this.value.replace(/[^0-9]/g, '');
                    if (this.value && idx < digits.length - 1) {
                        digits[idx + 1].focus();
                    }
                    updateHiddenInput();
                });

                // Handle backspace
                input.addEventListener('keydown', function (e) {
                    if (e.key === 'Backspace' && !this.value && idx > 0) {
                        digits[idx - 1].focus();
                        digits[idx - 1].value = '';
                        updateHiddenInput();
                    }
                });

                // Handle paste (e.g. pasting entire code)
                input.addEventListener('paste', function (e) {
                    e.preventDefault();
                    const paste = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '');
                    if (paste.length === 6) {
                        digits.forEach((d, i) => d.value = paste[i] || '');
                        digits[5].focus();
                        updateHiddenInput();
                    }
                });

                // Select text on focus for easy replacement
                input.addEventListener('focus', function () {
                    this.select();
                });
            });

            // Auto-focus first digit
            digits[0].focus();

            // Countdown timer based on server expiry timestamp
            const expiresAt = {{ $expiresAt }};
            const countdownEl = document.getElementById('countdown');
            const timerTextEl = document.getElementById('timer-text');

            function updateCountdown() {
                const now = Math.floor(Date.now() / 1000);
                let timeLeft = expiresAt - now;
                if (timeLeft < 0) timeLeft = 0;

                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                countdownEl.textContent = minutes + ':' + (seconds < 10 ? '0' : '') + seconds;

                if (timeLeft <= 30) {
                    countdownEl.classList.remove('text-indigo-600');
                    countdownEl.classList.add('text-red-500');
                }

                if (timeLeft <= 0) {
                    clearInterval(timer);
                    timerTextEl.innerHTML = '<span class="text-red-500 font-semibold">Code expired. Please request a new one.</span>';
                    verifyBtn.disabled = true;
                }
            }

            updateCountdown();
            const timer = setInterval(updateCountdown, 1000);
        });
    </script>
</x-guest-layout>
