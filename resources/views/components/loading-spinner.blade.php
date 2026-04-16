{{-- ── Global Button Loading Spinner ─────────────────────────────────────────── --}}
{{-- Automatically adds a spinner to any form submit button on submission.     --}}
{{-- Buttons with the class "no-loading-spinner" are excluded.                 --}}
{{-- Forms with the id "project-form" are excluded (has its own custom overlay). --}}

<style>
    @keyframes btn-spin {
        to { transform: rotate(360deg); }
    }
    .btn-spinner-icon {
        animation: btn-spin 0.7s linear infinite;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.addEventListener('submit', function (e) {
            const form = e.target;
            if (!(form instanceof HTMLFormElement)) return;

            // Skip forms that have their own custom loading (e.g. project submission)
            if (form.id === 'project-form') return;

            // Find the submit button within this form
            const btn = form.querySelector('button[type="submit"]');
            if (!btn) return;

            // Skip if already processing or if the button opts out
            if (btn.dataset.loading === 'true') return;
            if (btn.classList.contains('no-loading-spinner')) return;

            // Mark as loading
            btn.dataset.loading = 'true';

            // Save original content
            btn.dataset.originalHtml = btn.innerHTML;
            const originalMinWidth = btn.style.minWidth;

            // Lock the button's width so it doesn't jump
            const rect = btn.getBoundingClientRect();
            btn.style.minWidth = rect.width + 'px';

            // Build the spinner SVG
            const spinnerSvg = '<svg class="btn-spinner-icon inline-block w-4 h-4" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>';

            // Check if button is "small" (primarily icon-only or short text)
            const isCompact = rect.width < 120 || btn.textContent.trim().length <= 8;

            if (isCompact) {
                // For small buttons, just show the spinner icon
                btn.innerHTML = spinnerSvg;
            } else {
                // For larger buttons, show spinner + "Processing..."
                btn.innerHTML = '<span class="inline-flex items-center gap-2">' + spinnerSvg + ' <span>Processing...</span></span>';
            }

            // Disable the button
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');

            // Safety: re-enable after 15 seconds in case form doesn't navigate
            setTimeout(function () {
                if (btn.dataset.loading === 'true') {
                    btn.innerHTML = btn.dataset.originalHtml;
                    btn.style.minWidth = originalMinWidth;
                    btn.disabled = false;
                    btn.dataset.loading = 'false';
                    btn.classList.remove('opacity-75', 'cursor-not-allowed');
                }
            }, 15000);
        });
    });
</script>
