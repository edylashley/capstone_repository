<style>
    /* Global Safety Reset: Prevents any image from scaling out of control */
    img {
        max-width: 100%;
        height: auto;
    }

    /* ════════════════════════════════════════════════════════════════
       PREMIUM THEME CORE ENGINE
       Handles dynamic variables for Dark/Light mode transitions.
    ════════════════════════════════════════════════════════════════ */
    :root {
        /* Default Dark Mode (Legacy Base) */
        --bg-main: #020617; /* slate-950 */
        --bg-card: #0f172a; /* slate-900 */
        --text-main: #f8fafc; /* slate-50 */
        --text-muted: #94a3b8; /* slate-400 */
        --border-main: rgba(255, 255, 255, 0.05);
        --theme-accent: rgba(79, 70, 229, 0.1); /* indigo-600/10 */
        --shadow-card: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
    }

    /* ═══ PREMIUM LIGHT MODE OVERRIDES ═══ */
    .light-mode:root,
    .light-mode {
        --bg-main: #e2e8f0; /* Slate 200 - richer background for maximum card contrast */
        --bg-card: #ffffff; /* Crisp white cards */
        --text-main: #0f172a; /* Slate 900 - high contrast */
        --text-muted: #475569; /* Slate 600 */
        --border-main: #cbd5e1; /* Slate 300 - clear borders */
        --theme-accent: #f1f5f9; /* Slate 100 */
        --shadow-card: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    /* ═══ SEMANTIC UTILITY CLASSES ═══ */
    .bg-main { background-color: var(--bg-main) !important; }
    .bg-theme-card { background-color: var(--bg-card) !important; }
    .text-main { color: var(--text-main) !important; }
    .text-muted { color: var(--text-muted) !important; }
    .border-theme { border-color: var(--border-main) !important; }
    .bg-theme-accent { background-color: var(--theme-accent) !important; }

    /* Core Card Component */
    .card-bg {
        background-color: var(--bg-card) !important;
        border-color: var(--border-main) !important;
        box-shadow: var(--shadow-card) !important;
        transition: all 0.3s ease;
    }

    .sidebar-bg {
        background-color: var(--bg-card) !important;
        border-color: var(--border-main) !important;
    }

    .header-bg {
        background-color: var(--bg-card) !important;
        border-color: var(--border-main) !important;
        backdrop-blur: 12px;
    }

    /* ═══ LIGHT MODE COMPATIBILITY LAYER ═══ */
    .light-mode .light-mode\:bg-slate-50\/50 { background-color: rgba(248, 250, 252, 0.5) !important; }
    .light-mode .light-mode\:text-slate-600 { color: #475569 !important; }
    .light-mode .light-mode\:border-slate-200 { border-color: #e2e8f0 !important; }

    /* Dark Gradient Overrides for Light Mode */
    .light-mode .from-slate-900,
    .light-mode .from-slate-950,
    .light-mode .from-gray-900 {
        --tw-gradient-from: #ffffff !important;
        --tw-gradient-to: rgba(255, 255, 255, 0) !important;
        --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to) !important;
    }

    .light-mode .bg-slate-900,
    .light-mode .bg-slate-950,
    .light-mode .bg-gray-900 {
        background-color: #ffffff !important;
    }

    /* Table Header Visibility */
    .light-mode thead tr {
        background-color: #f1f5f9 !important;
        color: #0f172a !important;
    }

    /* Transition Engine */
    .theme-transition {
        transition: background-color 0.5s ease, border-color 0.5s ease, color 0.4s ease, box-shadow 0.5s ease !important;
    }

    /* Badge & Pill Optimization */
    .light-mode .bg-indigo-500\/10, 
    .light-mode .bg-indigo-500\/20,
    .light-mode .bg-indigo-600\/10,
    .light-mode .bg-indigo-600\/20 {
        background-color: #e0e7ff !important;
        color: #4338ca !important;
        border: 1px solid #c7d2fe !important;
    }

    .light-mode .bg-rose-500\/10,
    .light-mode .bg-rose-600\/10 {
        background-color: #ffe4e6 !important;
        color: #be123c !important;
        border: 1px solid #fecdd3 !important;
    }
</style>
