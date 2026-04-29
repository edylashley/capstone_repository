<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight flex-1 break-words opacity-90 italic">Project
                Manuscript</h2>
            <a href="{{ url()->previous() }}"
                class="inline-flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-gray-50 dark:hover:bg-gray-600 transition-all shadow-sm hover:shadow-md border border-gray-200 dark:border-gray-600 shrink-0 whitespace-nowrap mt-1 sm:mt-0">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Go Back
            </a>
        </div>
    </x-slot>

    <div class="py-6 md:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($project->trashed())
                <div
                    class="mb-8 bg-rose-500/10 border border-rose-500/30 rounded-3xl p-6 flex items-center justify-between shadow-2xl shadow-rose-500/10 animate-pulse">
                    <div class="flex items-center gap-4">
                        <span class="text-3xl">⚠️</span>
                        <div>
                            <h3 class="text-rose-500 font-black uppercase tracking-widest text-sm">Archived Record</h3>
                            <p class="text-rose-400/70 text-xs font-bold">This project is currently in the Central Archive.
                                It is hidden from the public library.</p>
                        </div>
                    </div>
                    <form action="{{ route('admin.archive.restore', ['type' => 'project', 'id' => $project->id]) }}"
                        method="POST">
                        @csrf
                        <button type="submit"
                            class="px-6 py-2.5 bg-rose-500 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:bg-rose-600 transition-all shadow-lg shadow-rose-500/40">
                            Restore Now
                        </button>
                    </form>
                </div>
            @endif
            <div class="flex flex-col gap-6 @if(optional(auth()->user())->isAdmin()) lg:flex-row @endif">

                <!-- Metadata Side -->
                <div
                    class="bg-white dark:bg-slate-900 overflow-hidden shadow-sm dark:shadow-sm sm:rounded-xl p-4 md:p-6 @if(optional(auth()->user())->isAdmin()) lg:w-1/3 lg:min-w-[350px] @else w-full @endif border border-gray-200 dark:border-white/5 h-fit transition-colors duration-300">
                    <div class="mb-4 space-y-2">
                        <div class="flex justify-between items-start border-b border-gray-200 dark:border-white/5 pb-6 mb-6">
                            <div>
                                <h3 class="text-xs font-black uppercase tracking-widest text-blue-600 dark:text-indigo-500 mb-3">Project
                                    Title</h3>
                                <h1 class="text-2xl font-black text-gray-900 dark:text-white leading-tight mb-3">{{ $project->title }}</h1>
                                <p class="text-[10px] text-gray-500 dark:text-slate-500 font-bold uppercase tracking-widest">Digital Record
                                    #{{ str_pad($project->id, 5, '0', STR_PAD_LEFT) }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-4">
                            <p class="text-sm text-gray-600 dark:text-slate-400"><strong
                                    class="text-gray-900 dark:text-white uppercase text-[10px] tracking-widest block mb-1">Year
                                    Defended</strong> {{ $project->year }}</p>
                            <p class="text-sm text-gray-600 dark:text-slate-400"><strong
                                    class="text-gray-900 dark:text-white uppercase text-[10px] tracking-widest block mb-1">Adviser</strong>
                                {{ $project->adviser_name ?? $project->adviser->name ?? '-' }}</p>
                            <p class="text-sm text-gray-600 dark:text-slate-400"><strong
                                    class="text-gray-900 dark:text-white uppercase text-[10px] tracking-widest block mb-1">Program</strong>
                                {{ $project->program ?? '-' }}</p>
                            <p class="text-sm text-gray-600 dark:text-slate-400"><strong
                                    class="text-gray-900 dark:text-white uppercase text-[10px] tracking-widest block mb-1">Authors</strong>
                                {{ $project->authors_list ?: $project->authors->pluck('name')->join(', ') }}</p>
                        </div>
                        <div class="pt-4 border-t border-gray-200 dark:border-white/5 mt-4">
                            <strong
                                class="text-blue-600 dark:text-indigo-500 uppercase text-[10px] tracking-widest block mb-2">Abstract</strong>
                            <p class="text-sm text-gray-700 dark:text-slate-300 italic leading-relaxed">{{ $project->abstract }}</p>
                        </div>

                        <div class="pt-4 border-t border-gray-200 dark:border-white/5 mt-4">
                            <strong
                                class="text-gray-900 dark:text-white uppercase text-[10px] tracking-widest block mb-2">Categories</strong>
                            <div class="text-sm mt-1 flex flex-wrap gap-2">
                                @foreach($project->categories as $category)
                                    <span
                                        class="bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-[10px] font-black px-2 py-1 rounded-lg border border-indigo-100 dark:border-indigo-800 uppercase tracking-tighter">
                                        {{ $category->name }}
                                    </span>
                                @endforeach
                                @if($project->custom_category)
                                    <span
                                        class="bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-[10px] font-black px-2 py-1 rounded-lg border border-indigo-100 dark:border-indigo-800 uppercase tracking-tighter">
                                        {{ $project->custom_category }}
                                    </span>
                                @endif
                                @if(is_array($project->keywords))
                                    @foreach($project->keywords as $kw)
                                        <span
                                            class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-[10px] px-2 py-1 rounded-lg border border-gray-100 dark:border-gray-600 font-bold">#{{ $kw }}</span>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        @if(!$project->trashed() && ($project->status === 'published' || (auth()->check() && (auth()->user()->isAdmin() || $project->authors->contains(auth()->user())))))
                            @if($project->status === 'published')
                                <div class="mt-4 p-5 border-2 border-emerald-500/30 bg-emerald-500/10 rounded-2xl shadow-sm">
                                    <span
                                        class="text-white/50 uppercase text-[10px] font-bold tracking-widest block mb-1">RECORD
                                        STATUS:</span>
                                    <div class="flex items-center gap-3">
                                        <div class="relative flex h-3 w-3">
                                            <span
                                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-500 opacity-75"></span>
                                            <span
                                                class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.4)]"></span>
                                        </div>
                                        <span
                                            class="text-emerald-400 font-black text-sm uppercase tracking-tight italic">Official
                                            Published Record</span>
                                    </div>
                                </div>
                            @elseif($project->status === 'approved')
                                <div class="mt-4 p-5 border-2 border-indigo-500/30 bg-indigo-500/10 rounded-2xl shadow-sm">
                                    <span
                                        class="text-white/50 uppercase text-[10px] font-bold tracking-widest block mb-1">RECORD
                                        STATUS:</span>
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="relative inline-flex rounded-full h-3 w-3 bg-indigo-500 shadow-[0_0_10px_rgba(99,102,241,0.4)]"></span>
                                        <span
                                            class="text-indigo-400 font-black text-sm uppercase tracking-tight italic">Confirmed
                                            Final Version</span>
                                    </div>
                                </div>
                            @elseif($project->status === 'returned')
                                <div class="mt-4 p-5 border-2 border-rose-500/30 bg-rose-500/10 rounded-2xl shadow-sm">
                                    <span
                                        class="text-white/50 dark:text-white/30 uppercase text-[10px] font-bold tracking-widest block mb-1">RECORD
                                        STATUS:</span>
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="relative inline-flex rounded-full h-3 w-3 bg-rose-500 shadow-[0_0_10px_rgba(244,63,94,0.4)]"></span>
                                        <span
                                            class="text-rose-600 dark:text-rose-400 font-black text-sm uppercase tracking-tight italic">Returned for Revision</span>
                                    </div>
                                    @if($project->rejection_reason)
                                        <div class="mt-4 pt-4 border-t border-rose-500/20">
                                            <p class="text-[10px] text-gray-500 dark:text-gray-400 uppercase font-bold tracking-widest mb-2">
                                                Administrator's Feedback:</p>
                                            <p
                                                class="text-sm text-gray-700 dark:text-rose-200/80 leading-relaxed whitespace-pre-wrap bg-rose-500/5 rounded-lg p-3 border border-rose-500/10">
                                                {{ $project->rejection_reason }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="mt-4 p-5 border-2 border-yellow-500/30 bg-yellow-500/10 rounded-2xl shadow-sm">
                                    <span
                                        class="text-white/50 uppercase text-[10px] font-bold tracking-widest block mb-1">RECORD
                                        STATUS:</span>
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="relative inline-flex rounded-full h-3 w-3 bg-yellow-500 shadow-[0_0_10px_rgba(234,179,8,0.4)]"></span>
                                        <span class="text-yellow-300 font-black text-sm uppercase tracking-tight italic">Archive
                                            Processing</span>
                                    </div>
                                </div>
                            @endif

                            <style>
                                @media (max-width: 767px) {
                                    .desktop-only {
                                        display: none !important;
                                    }

                                    .mobile-only {
                                        display: block !important;
                                    }
                                }

                                @media (min-width: 768px) {
                                    .desktop-only {
                                        display: block !important;
                                    }

                                    .mobile-only {
                                        display: none !important;
                                    }
                                }
                            </style>
                            <!-- ADVISER DECISION HUB ACTION -->
                            <!-- SYSTEM VERIFICATION REPORT (Visible to Faculty/Admin always if notes exist) -->
                            @if(!$project->trashed() && optional(auth()->user())->isAdmin() && $project->manuscript_validation_notes)
                                <div class="mt-8 pt-6 border-t border-gray-700/50">
                                    <!-- Assistant's Report Card -->
                                    <div
                                        class="mb-6 p-4 rounded-xl {{ $project->manuscript_validated ? 'bg-emerald-500/10 border border-emerald-500/20' : 'bg-yellow-500/10 border border-yellow-500/20' }}">
                                        <div class="flex items-center gap-2 mb-3">
                                            <span class="text-lg">⚙️</span>
                                            <h4
                                                class="font-bold text-[11px] tracking-widest uppercase {{ $project->manuscript_validated ? 'text-emerald-400' : 'text-yellow-400' }}">
                                                System Verification Report
                                            </h4>
                                        </div>

                                        <div class="space-y-2">
                                            @php
                                                $notes = explode("\n", $project->manuscript_validation_notes);
                                                $hasPrintedHeader = false;
                                            @endphp
                                            @foreach($notes as $note)
                                                @if(str_starts_with($note, 'Detected:') && !$hasPrintedHeader)
                                                    <div
                                                        class="mt-3 mb-1 text-[9px] font-bold text-gray-500 uppercase tracking-widest border-b border-gray-700/50 pb-1">
                                                        Keywords Found
                                                    </div>
                                                    @php $hasPrintedHeader = true; @endphp
                                                @endif
                                                <div class="flex items-start gap-2">
                                                    <span
                                                        class="text-xs {{ str_contains($note, '[ERROR]') ? 'text-red-500' : (str_contains($note, 'Warning') || str_contains($note, '!') || str_contains($note, '(i)') ? 'text-yellow-500' : 'text-emerald-500') }}">●</span>
                                                    <p class="text-[11px] leading-tight text-gray-300">
                                                        @if(str_starts_with($note, 'Detected:'))
                                                            @php
                                                                // Extract keyword and page list: "Detected: approval (Pages 1, 2, 3)"
                                                                $formattedNote = preg_replace_callback('/Detected:\s+(.+?)\s+\(Pages\s+([\d,\s]+)\)/', function ($matches) {
                                                                    $keyword = trim($matches[1]);
                                                                    $pageListStr = $matches[2]; // "1, 2, 3"
                                                                    $pages = explode(',', $pageListStr);

                                                                    $links = [];
                                                                    $jsKeyword = addslashes($keyword);
                                                                    foreach ($pages as $p) {
                                                                        $p = trim($p);
                                                                        $links[] = '<a href="#" onclick="jumpToPage(' . $p . ', \'' . $jsKeyword . '\'); return false;" class="text-indigo-400 hover:text-indigo-300 underline font-bold" title="Jump to Page ' . $p . '">' . $p . '</a>';
                                                                    }

                                                                    return '<strong class="text-gray-400">' . $keyword . '</strong> found on pages: ' . implode(', ', $links);
                                                                }, ltrim($note, '✓ ●- '));
                                                            @endphp
                                                            {!! $formattedNote !!}
                                                        @else
                                                            {{ ltrim($note, '✓ ●- ') }}
                                                        @endif
                                                    </p>
                                                </div>
                                            @endforeach
                                        </div>

                                        @if(!$project->manuscript_validated)
                                            <div class="mt-4 p-2 bg-yellow-500/20 rounded-lg border border-yellow-500/30">
                                                <p class="text-[9px] text-gray-300 leading-tight italic">
                                                    <strong>Note:</strong> Required page (Approval Sheet) not detected in text.
                                                    Please manually verify the uploaded PDF (specifically the Approval Sheet) before
                                                    confirming.
                                                </p>
                                            </div>
                                        @endif

                                        @if($project->status === 'pending')
                                            <form method="POST" action="{{ route('admin.projects.verify-pdf', $project->id) }}"
                                                class="mt-4 border-t border-dashed border-gray-600/50 pt-2">
                                                @csrf
                                                <button type="submit"
                                                    class="flex items-center gap-2 text-[10px] text-gray-400 hover:text-indigo-400 transition-colors">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                                        </path>
                                                    </svg>
                                                    Run System Scan Again
                                                </button>
                                            </form>
                                        @endif
                                    </div>

                                    @if(!$project->trashed() && $project->status === 'pending' && auth()->user()->isAdmin())
                                        <h4 class="font-black text-gray-400 uppercase text-xs mb-4">Verification Actions</h4>
                                        <form method="POST" action="{{ route('admin.projects.approve', $project) }}"
                                            onsubmit="return confirm('Confirm that this is the final, defended version of the project?');">
                                            @csrf
                                            <button type="submit"
                                                class="w-full bg-indigo-600 text-white font-black py-4 rounded-lg shadow-lg hover:shadow-xl hover:bg-indigo-700 transition-all border-b-4 border-indigo-800 active:translate-y-1 active:border-b-0 uppercase tracking-widest text-sm text-nowrap">
                                                Confirm Final Record
                                            </button>
                                        </form>
                                        <p class="text-[10px] text-gray-500 mt-2 text-center italic">Digital signature will be
                                            recorded upon confirmation.</p>
                                    @endif
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- PDF Layout (Side or Bottom) -->
                <div class="flex-1 min-w-0 space-y-6">
                    <div class="bg-white dark:bg-slate-900 overflow-hidden shadow-sm sm:rounded-xl p-4 md:p-8 border border-gray-200 dark:border-white/5 transition-colors duration-300">
                        <div
                            class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 border-b border-gray-200 dark:border-white/5 pb-4">
                            <h3 class="font-black text-lg text-gray-900 dark:text-white uppercase tracking-tight italic">Manuscript Viewer
                            </h3>
                            @auth
                                <div class="flex items-center gap-2 desktop-only">
                                    <button onclick="window.open('{{ route('projects.viewer', $project) }}', '_blank')"
                                        class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2 bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-gray-200 dark:hover:bg-slate-700 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                        Open Full View
                                    </button>
                                </div>
                            @endauth
                        </div>

                        @php
                            $manuscript = $project->files->firstWhere('type', 'manuscript');
                            $attachments = $project->files->where('type', 'attachment');
                        @endphp

                        @if($manuscript)
                            @guest
                                <!-- Guest Preview Banner -->
                                <div
                                    class="mb-6 p-6 bg-indigo-600 rounded-3xl shadow-xl shadow-indigo-500/20 flex flex-col md:flex-row items-center justify-between gap-4 border border-white/10 relative overflow-hidden group">
                                    <div
                                        class="absolute -right-8 -top-8 w-32 h-32 bg-white/10 rounded-full blur-3xl group-hover:bg-white/20 transition-all duration-700">
                                    </div>
                                    <div class="flex items-center gap-4 relative z-10">
                                        <div
                                            class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center backdrop-blur-md border border-white/20">
                                            <span class="text-2xl">🔓</span>
                                        </div>
                                        <div>
                                            <h4 class="text-white font-black uppercase tracking-widest text-sm leading-tight">
                                                Limited Preview Mode</h4>
                                            <p class="text-indigo-100 text-[11px] font-bold mt-1">Guests can only view the first
                                                5 pages. Sign in for full access.</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('login') }}"
                                        class="px-6 py-3 bg-white text-indigo-600 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-50 transition-all shadow-lg active:scale-95 relative z-10">
                                        Sign In for Full View
                                    </a>
                                </div>
                            @endguest
                            <!-- Universal Viewer Wrapper -->
                            <div id="pdf-viewer-wrapper">
                                <!-- Desktop Viewer -->
                                <div class="desktop-only relative border-4 border-gray-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-inner bg-gray-100 dark:bg-slate-950 min-h-[800px]"
                                    id="pdf-container">
                                    @auth
                                        <iframe id="manuscript-viewer" src="{{ route('files.view', $manuscript) }}" width="100%"
                                            height="800px" style="min-height: 800px;"
                                            class="w-full h-[600px] md:h-[800px] bg-gray-200 dark:bg-gray-500 border-0"></iframe>
                                    @else
                                        <!-- Guest Desktop Viewer (Locked to 5 Pages) -->
                                        <div id="desktop-guest-viewer"
                                            class="bg-gray-100 dark:bg-slate-950 flex flex-col items-center justify-start h-[800px] overflow-y-auto p-8 custom-scrollbar scroll-smooth">
                                            <div id="desktop-canvas-container"
                                                class="flex flex-col gap-12 items-center pb-20 w-full">
                                                <!-- Pages will be rendered here -->
                                            </div>

                                            <!-- Loading Overlay -->
                                            <div id="desktop-pdf-loading"
                                                class="absolute inset-0 flex flex-col items-center justify-center bg-gray-100 dark:bg-slate-900 z-10">
                                                <div
                                                    class="w-12 h-12 border-4 border-blue-500 dark:border-indigo-500 border-t-transparent rounded-full animate-spin mb-4">
                                                </div>
                                                <p
                                                    class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest animate-pulse">
                                                    Loading Secured Preview...</p>
                                            </div>
                                        </div>
                                    @endauth
                                </div>

                                <!-- Mobile Viewer Fallback (Renders PDF to Canvas) -->
                                <div class="mobile-only">
                                    <div class="bg-gray-900 border-2 border-indigo-500/30 rounded-2xl overflow-hidden shadow-2xl relative"
                                        id="mobile-pdf-wrapper" style="min-height: 400px;">
                                        <!-- Canvas for PDF Rendering -->
                                        <div id="mobile-canvas-container"
                                            class="flex justify-center bg-gray-800 p-2 overflow-hidden">
                                            <canvas id="mobile-pdf-canvas" class="shadow-xl max-w-full h-auto"></canvas>
                                        </div>

                                        <!-- Mobile Teaser (Locked Page 6) -->
                                        <div id="mobile-teaser-container"
                                            class="hidden relative w-full min-h-[400px] overflow-hidden bg-slate-900 flex items-center justify-center">
                                            <canvas id="mobile-teaser-canvas"
                                                class="absolute inset-0 w-full h-full object-cover blur-xl opacity-50"></canvas>
                                            <div
                                                class="absolute inset-0 z-20 flex flex-col items-center justify-center text-center p-6 bg-slate-900/60 backdrop-blur-md">
                                                <div class="relative">
                                                    <div
                                                        class="absolute -inset-4 bg-indigo-500/20 blur-2xl rounded-full animate-pulse">
                                                    </div>
                                                    <div
                                                        class="relative w-16 h-16 bg-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-2xl shadow-indigo-500/40 border border-white/20">
                                                        <span class="text-2xl">🔐</span>
                                                    </div>
                                                </div>
                                                <h5 class="text-xl font-black text-white uppercase tracking-[0.2em] mb-2">
                                                    Keep Reading?</h5>
                                                <p
                                                    class="text-indigo-100/70 text-[10px] font-bold uppercase tracking-widest mb-8 max-w-[240px] mx-auto leading-relaxed">
                                                    This manuscript contains <span class="text-white font-black"
                                                        id="mobile-teaser-page-count">0</span> pages of research.
                                                    Sign in to unlock the full document.
                                                </p>
                                                <a href="{{ route('login') }}"
                                                    class="inline-flex items-center gap-3 px-6 py-4 bg-white text-indigo-600 rounded-xl text-[9px] font-black uppercase tracking-[0.2em] shadow-2xl active:scale-95 transition-all">
                                                    <span>Unlock Full Access</span>
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="3" d="M14 5l7 7-7 7M3 12h18" />
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>

                                        <!-- Mobile Controls -->
                                        <div
                                            class="bg-gray-900/90 backdrop-blur-md p-4 border-t border-white/10 flex items-center justify-between">
                                            <div class="flex items-center gap-2">
                                                <button onclick="mobilePrevPage()"
                                                    class="p-2 bg-gray-800 text-white rounded-lg active:scale-90 transition"><svg
                                                        class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 19l-7-7 7-7" />
                                                    </svg></button>
                                                <span
                                                    class="text-[10px] font-black text-white uppercase tracking-widest px-2"><span
                                                        id="mobile-page-num">1</span> / <span
                                                        id="mobile-page-count">-</span></span>
                                                <button onclick="mobileNextPage()"
                                                    class="p-2 bg-gray-800 text-white rounded-lg active:scale-90 transition"><svg
                                                        class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M9 5l7 7-7 7" />
                                                    </svg></button>
                                            </div>
                                            @auth
                                                <button
                                                    onclick="window.open('{{ route('projects.viewer', $project) }}', '_blank')"
                                                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-[10px] font-black uppercase tracking-widest flex items-center gap-2">
                                                    <span>Full View</span>
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                    </svg>
                                                </button>
                                            @endauth
                                        </div>

                                        <!-- Loading Overlay -->
                                        <div id="mobile-pdf-loading"
                                            class="absolute inset-0 flex flex-col items-center justify-center bg-gray-900 z-10 transition-opacity duration-500">
                                            <div
                                                class="w-10 h-10 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin mb-4">
                                            </div>
                                            <p
                                                class="text-[9px] font-black text-white uppercase tracking-widest animate-pulse">
                                                Loading Preview...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <script src="{{ asset('assets/vendor/pdfjs/pdf.min.js') }}"></script>
                            <script>
                                                // Mobile PDF Engine
                                    const m_pdfjsLib = window['pdfjs-dist/build/pdf'];
                                    m_pdfjsLib.GlobalWorkerOptions.workerSrc = "{{ asset('assets/vendor/pdfjs/pdf.worker.min.js') }}";

                                    let m_pdfDoc = null, m_pageNum = 1, m_pageRendering = false;
                                    const m_canvas = document.getElementById('mobile-pdf-canvas');
                                    const m_ctx = m_canvas.getContext('2d');
                                    const isGuest = {{ auth()->check() ? 'false' : 'true' }};

                                    function renderMobilePage(num) {
                                        if (!m_pdfDoc) return;

                                        const canvasContainer = document.getElementById('mobile-canvas-container');
                                        const teaserContainer = document.getElementById('mobile-teaser-container');

                                        // Mobile Teaser Logic for Guests
                                        if (isGuest && num === 6) {
                                            if (canvasContainer) canvasContainer.classList.add('hidden');
                                            if (teaserContainer) {
                                                teaserContainer.classList.remove('hidden');

                                                // Render teaser canvas if not already rendered
                                                const teaserCanvas = document.getElementById('mobile-teaser-canvas');
                                                if (teaserCanvas && !teaserCanvas.dataset.rendered) {
                                                    const teaserPageNum = Math.min(m_pdfDoc.numPages, 6);
                                                    m_pdfDoc.getPage(teaserPageNum).then(function(page) {
                                                        const viewport = page.getViewport({ scale: 1.0 });
                                                        const ctx = teaserCanvas.getContext('2d');
                                                        teaserCanvas.height = viewport.height;
                                                        teaserCanvas.width = viewport.width;
                                                        page.render({ canvasContext: ctx, viewport: viewport }).promise.then(() => {
                                                            teaserCanvas.dataset.rendered = "true";
                                                        });
                                                    });
                                                }
                                            }

                                            document.getElementById('mobile-page-num').textContent = num;
                                            m_pageRendering = false;

                                            // Hide loading if it was visible
                                            const loading = document.getElementById('mobile-pdf-loading');
                                            if (loading) {
                                                loading.style.opacity = '0';
                                                setTimeout(() => loading.classList.add('hidden'), 500);
                                            }
                                            return;
                                        }

                                        // Normal page rendering
                                        if (canvasContainer) canvasContainer.classList.remove('hidden');
                                        if (teaserContainer) teaserContainer.classList.add('hidden');

                                        // Frontend Page Limit for Guests
                                        if (isGuest && num > 6) {
                                            return;
                                        }

                                        m_pageRendering = true;
                                        m_pdfDoc.getPage(num).then(function (page) {
                                            // HD Rendering for Mobile (Retina/DPR Support)
                                            const dpr = window.devicePixelRatio || 1;
                                            const containerWidth = document.getElementById('mobile-canvas-container').clientWidth - 20;

                                            const unscaledViewport = page.getViewport({ scale: 1.0 });
                                            const scale = containerWidth / unscaledViewport.width;
                                            const viewport = page.getViewport({ scale: scale });

                                            m_canvas.height = viewport.height * dpr;
                                            m_canvas.width = viewport.width * dpr;
                                            m_canvas.style.width = viewport.width + 'px';
                                            m_canvas.style.height = viewport.height + 'px';

                                            m_ctx.setTransform(dpr, 0, 0, dpr, 0, 0);

                                            const renderContext = { canvasContext: m_ctx, viewport: viewport };
                                            page.render(renderContext).promise.then(function () {
                                                m_pageRendering = false;
                                                document.getElementById('mobile-page-num').textContent = num;
                                                const loading = document.getElementById('mobile-pdf-loading');
                                                if (loading) {
                                                    loading.style.opacity = '0';
                                                    setTimeout(() => loading.classList.add('hidden'), 500);
                                                }
                                            });
                                        });
                                    }

                                    function mobilePrevPage() { if (m_pageNum <= 1 || m_pageRendering) return; m_pageNum--; renderMobilePage(m_pageNum); }
                                    function mobileNextPage() { 
                                        const maxPage = isGuest ? Math.min(m_pdfDoc.numPages, 6) : m_pdfDoc.numPages;
                                        if (m_pageNum >= maxPage || m_pageRendering) return; 
                                        m_pageNum++; 
                                        renderMobilePage(m_pageNum); 
                                    }

                                    // Start Mobile/Guest Engine
                                    if (isGuest || window.innerWidth < 768) {
                                        m_pdfjsLib.getDocument({
                                            url: "{{ route('files.view', $manuscript) }}",
                                            withCredentials: true
                                        }).promise.then(function (pdfDoc_) {
                                            m_pdfDoc = pdfDoc_;

                                            if (isGuest && window.innerWidth >= 768) {
                                                // DESKTOP GUEST: Render all 5 pages at once
                                                const pagesToRender = Math.min(m_pdfDoc.numPages, 5);
                                                const container = document.getElementById('desktop-canvas-container');

                                                for (let i = 1; i <= pagesToRender; i++) {
                                                    const canvas = document.createElement('canvas');
                                                    canvas.className = "shadow-2xl rounded-sm max-w-full h-auto border border-white/5";
                                                    container.appendChild(canvas);

                                                    m_pdfDoc.getPage(i).then(function (page) {
                                                        const dpr = window.devicePixelRatio || 1;
                                                        // Limit width to 850px to prevent "zoomed" appearance on large screens
                                                        const containerWidth = Math.min(container.clientWidth - 60, 850); 
                                                        const unscaledViewport = page.getViewport({ scale: 1.0 });
                                                        const scale = containerWidth / unscaledViewport.width;
                                                        const viewport = page.getViewport({ scale: scale });
                                                        const ctx = canvas.getContext('2d');
                                                        
                                                        canvas.height = viewport.height * dpr;
                                                        canvas.width = viewport.width * dpr;
                                                        canvas.style.width = viewport.width + 'px';
                                                        canvas.style.height = viewport.height + 'px';
                                                        
                                                        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
                                                        
                                                        page.render({ canvasContext: ctx, viewport: viewport }).promise.then(() => {
                                                            if (i === pagesToRender && m_pdfDoc.numPages > 5) {
                                                                // Add REAL Blurred Page 6 for Guests as a teaser
                                                                const teaserWrapper = document.createElement('div');
                                                                teaserWrapper.className = "relative w-full max-w-[800px] h-[800px] shrink-0 rounded-xl overflow-hidden border border-white/10 mb-12 shadow-2xl group";

                                                                // We'll render the actual page 6 inside this, but blur it
                                                                const teaserCanvas = document.createElement('canvas');
                                                                teaserCanvas.className = "absolute inset-0 w-full h-full object-cover blur-xl opacity-50";
                                                                teaserWrapper.appendChild(teaserCanvas);

                                                                // Overlay with Message
                                                                const overlay = document.createElement('div');
                                                                overlay.className = "absolute inset-0 z-20 flex flex-col items-center justify-center text-center p-12 bg-slate-900/60 backdrop-blur-md";
                                                                overlay.innerHTML = `
                                                                        <div class="relative">
                                                                            <div class="absolute -inset-4 bg-indigo-500/20 blur-2xl rounded-full animate-pulse"></div>
                                                                            <div class="relative w-24 h-24 bg-indigo-600 rounded-[2.5rem] flex items-center justify-center mx-auto mb-8 shadow-2xl shadow-indigo-500/40 border border-white/20">
                                                                                <span class="text-4xl">🔐</span>
                                                                            </div>
                                                                        </div>
                                                                        <h5 class="text-2xl font-black text-white uppercase tracking-[0.2em] mb-3">Keep Reading?</h5>
                                                                        <p class="text-indigo-100/70 text-[11px] font-bold uppercase tracking-widest mb-10 max-w-xs mx-auto leading-relaxed">
                                                                            This manuscript contains <span class="text-white font-black">${m_pdfDoc.numPages} pages</span> of research. 
                                                                            Sign in to unlock the full document.
                                                                        </p>
                                                                        <a href="{{ route('login') }}" class="inline-flex items-center gap-4 px-10 py-5 bg-white text-indigo-600 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] shadow-2xl hover:scale-105 transition-all active:scale-95 group">
                                                                            <span>Unlock Full Access</span>
                                                                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7-7 7M3 12h18"/></svg>
                                                                        </a>
                                                                    `;
                                                                teaserWrapper.appendChild(overlay);
                                                                container.appendChild(teaserWrapper);

                                                                // Render the teaser page (page 6 or last page)
                                                                const teaserPageNum = Math.min(m_pdfDoc.numPages, pagesToRender + 1);
                                                                m_pdfDoc.getPage(teaserPageNum).then(function (page) {
                                                                    const dpr = window.devicePixelRatio || 1;
                                                                    const containerWidth = Math.min(container.clientWidth - 60, 850);
                                                                    const unscaledViewport = page.getViewport({ scale: 1.0 });
                                                                    const scale = containerWidth / unscaledViewport.width;
                                                                    const viewport = page.getViewport({ scale: scale });
                                                                    const ctx = teaserCanvas.getContext('2d');
                                                                    
                                                                    teaserCanvas.height = viewport.height * dpr;
                                                                    teaserCanvas.width = viewport.width * dpr;
                                                                    teaserCanvas.style.width = viewport.width + 'px';
                                                                    teaserCanvas.style.height = viewport.height + 'px';
                                                                    
                                                                    ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
                                                                    
                                                                    page.render({ canvasContext: ctx, viewport: viewport });
                                                                });

                                                                const loading = document.getElementById('desktop-pdf-loading');
                                                                if (loading) {
                                                                    loading.style.opacity = '0';
                                                                    setTimeout(() => loading.classList.add('hidden'), 500);
                                                                }
                                                            } else if (i === pagesToRender) {
                                                                // Just hide loading if there's no teaser needed
                                                                const loading = document.getElementById('desktop-pdf-loading');
                                                                if (loading) {
                                                                    loading.style.opacity = '0';
                                                                    setTimeout(() => loading.classList.add('hidden'), 500);
                                                                }
                                                            }
                                                        });
                                                    });
                                                }
                                            } else {
                                                // MOBILE: Render one page at a time with navigation
                                                const totalPages = isGuest ? Math.min(m_pdfDoc.numPages, 6) : m_pdfDoc.numPages;
                                                document.getElementById('mobile-page-count').textContent = totalPages;

                                                if (isGuest) {
                                                    const mobileTeaserPageCount = document.getElementById('mobile-teaser-page-count');
                                                    if (mobileTeaserPageCount) mobileTeaserPageCount.textContent = m_pdfDoc.numPages;
                                                }

                                                renderMobilePage(m_pageNum);
                                            }
                                        }).catch(err => {
                                            console.error("PDF Engine Error:", err);
                                            const mobileLoading = document.getElementById('mobile-pdf-loading');
                                            const desktopLoading = document.getElementById('desktop-pdf-loading');
                                            if (mobileLoading) mobileLoading.innerHTML = '<p class="text-red-500 text-[10px] font-black uppercase">Preview Locked.</p>';
                                            if (desktopLoading) desktopLoading.innerHTML = '<p class="text-red-500 text-[10px] font-black uppercase">Preview Restricted.</p>';
                                        });
                                    }
                                </script>

                                <style>
                                    @media (min-width: 768px) {
                                        .mobile-only {
                                            display: none !important;
                                        }

                                        .desktop-only {
                                            display: block !important;
                                        }
                                    }

                                    @media (max-width: 767px) {
                                        .desktop-only {
                                            display: none !important;
                                        }

                                        .mobile-only {
                                            display: block !important;
                                        }
                                    }

                                    .animate-bounce-short {
                                        animation: bounce-short 1s ease-in-out infinite;
                                    }

                                    @keyframes bounce-short {

                                        0%,
                                        100% {
                                            transform: translateY(0);
                                        }

                                        50% {
                                            transform: translateY(-5px);
                                        }
                                    }
                                </style>

                                <div
                                    class="mt-6 flex flex-col sm:flex-row justify-between items-center gap-4 bg-gray-50 dark:bg-slate-800 p-4 rounded-2xl border border-gray-200 dark:border-white/5 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 bg-white dark:bg-slate-950 rounded-xl flex items-center justify-center shadow-sm dark:shadow-inner border border-gray-200 dark:border-white/5">
                                            <span class="text-xl">📄</span>
                                        </div>
                                        <div>
                                            <p class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest">
                                                {{ $manuscript->filename }}</p>
                                            <p class="text-[10px] text-gray-500 dark:text-slate-500 font-bold uppercase tracking-widest">
                                                {{ number_format($manuscript->size / 1048576, 2) }} MB</p>
                                        </div>
                                    </div>

                                    @auth
                                        <a href="{{ route('files.download', $manuscript->id) }}"
                                            class="w-full sm:w-auto px-8 py-3 bg-blue-600 dark:bg-gray-900 hover:bg-blue-700 dark:hover:bg-indigo-600 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-md dark:shadow-lg dark:hover:shadow-indigo-500/20 transition-all flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                            </svg>
                                            Download PDF
                                        </a>
                                    @endauth
                                </div>
                        @else
                            <div
                                class="p-12 text-center bg-gray-50 dark:bg-slate-950 rounded-3xl text-gray-500 dark:text-slate-500 border border-gray-200 dark:border-white/5 font-black uppercase tracking-widest text-xs">
                                No manuscript file detected.
                            </div>
                        @endif
                    </div>

                    {{-- Attachments: restricted to advisers, admins, and the project's own authors --}}
                    {{-- Attachments: restricted to advisers, admins, and the project's own authors --}}
                    @php
                        $isOwnerOrAdmin = auth()->check() && (
                            auth()->user()->isAdmin() ||
                            $project->authors->contains(auth()->user())
                        );
                    @endphp

                    @if($attachments->isNotEmpty())
                        <div class="bg-white dark:bg-slate-900 overflow-hidden shadow-sm dark:shadow-2xl sm:rounded-3xl p-8 border border-gray-200 dark:border-white/5 transition-colors">
                            <div class="flex items-center justify-between mb-8 border-b border-gray-200 dark:border-white/5 pb-6">
                                <h3 class="font-black text-xl text-gray-900 dark:text-white uppercase tracking-tight">Project Attachments</h3>
                                <span
                                    class="px-3 py-1 bg-gray-100 dark:bg-slate-950 text-[10px] font-black text-gray-500 dark:text-slate-400 uppercase rounded-full border border-gray-200 dark:border-white/5 shadow-sm dark:shadow-inner">{{ $attachments->count() }}
                                    Technical Files</span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($attachments as $file)
                                    @php
                                        $ext = strtolower(pathinfo($file->filename, PATHINFO_EXTENSION));
                                        $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                        $isVideo = in_array($ext, ['mp4', 'mov', 'webm', 'avi']);
                                        $isSafe = $isImage || $isVideo || $ext === 'pdf';
                                        
                                        // Access Logic
                                        $canView = $isOwnerOrAdmin || (auth()->check() && ($isImage || $isVideo));
                                        $canDownload = $isOwnerOrAdmin;
                                        $isLocked = !auth()->check() || (!$isOwnerOrAdmin && !$isSafe);

                                        $isPreviewable = $isImage || $isVideo;
                                        $icon = match (true) {
                                            in_array($ext, ['zip', 'rar', '7z']) => '📦',
                                            in_array($ext, ['mp4', 'mov', 'webm', 'avi']) => '🎬',
                                            in_array($ext, ['sql', 'db']) => '🗄️',
                                            in_array($ext, ['pdf']) => '📄',
                                            in_array($ext, ['doc', 'docx']) => '📝',
                                            in_array($ext, ['ppt', 'pptx']) => '📊',
                                            in_array($ext, ['xls', 'xlsx', 'csv']) => '📊',
                                            in_array($ext, ['jpg', 'jpeg', 'png', 'gif']) => '🖼️',
                                            in_array($ext, ['txt', 'md']) => '📃',
                                            in_array($ext, ['json', 'xml']) => '🔧',
                                            default => '📎'
                                        };
                                        $fileUrl = route('files.view', $file);
                                    @endphp
                                    <div
                                        class="flex flex-col border border-gray-200 dark:border-white/5 rounded-2xl {{ $isLocked ? 'bg-gray-100/50 dark:bg-slate-950/20 opacity-75' : 'bg-gray-50 dark:bg-slate-950/50 hover:bg-white dark:hover:bg-white/[0.02]' }} transition-all duration-300 overflow-hidden shadow-sm dark:shadow-lg group">
                                        <div class="flex items-center justify-between p-5">
                                            <div class="flex items-center gap-4 overflow-hidden">
                                                <div
                                                    class="w-12 h-12 rounded-xl {{ $isLocked ? 'bg-gray-200 dark:bg-slate-800' : 'bg-white dark:bg-slate-900' }} flex items-center justify-center shadow-sm dark:shadow-inner border border-gray-200 dark:border-white/5 flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
                                                    <span class="text-xl {{ $isLocked ? 'grayscale opacity-50' : '' }}">{{ $icon }}</span>
                                                </div>
                                                <div class="flex flex-col overflow-hidden">
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-sm font-black text-gray-900 dark:text-white truncate"
                                                            title="{{ $file->filename }}">{{ $file->filename }}</span>
                                                        @if($isLocked)
                                                            <span class="text-xs" title="Login required for full access">🔒</span>
                                                        @elseif($isImage)
                                                            <span
                                                                class="bg-emerald-50 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-[8px] font-black px-2 py-0.5 rounded-full uppercase tracking-widest border border-emerald-200 dark:border-emerald-500/20">Media</span>
                                                        @elseif($isVideo)
                                                            <span
                                                                class="bg-blue-50 dark:bg-indigo-500/20 text-blue-600 dark:text-indigo-400 text-[8px] font-black px-2 py-0.5 rounded-full uppercase tracking-widest border border-blue-200 dark:border-indigo-500/20">Stream</span>
                                                        @endif
                                                    </div>
                                                    <span class="text-[9px] uppercase font-black text-gray-500 dark:text-slate-500 tracking-widest mt-0.5">
                                                        {{ strtoupper($ext) }} • 
                                                        @if(!$isLocked)
                                                            {{ number_format($file->size / 1048576, 2) }} MB
                                                        @else
                                                            Restricted Access
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2 pr-2">
                                                @if(!$isLocked && $canView && $isPreviewable)
                                                    <button
                                                        onclick="{{ $isImage ? "openLightbox('$fileUrl', '$file->filename')" : "openVideoLightbox('$fileUrl', '$file->filename', '$ext')" }}"
                                                        class="p-2.5 text-blue-500 dark:text-indigo-400 hover:bg-blue-50 dark:hover:bg-indigo-500/20 rounded-xl transition-all"
                                                        title="Preview File">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                    </button>
                                                @endif
                                                @if($canDownload)
                                                    <a href="{{ route('files.download', $file) }}"
                                                        class="p-2.5 text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/10 rounded-xl transition-all"
                                                        title="Download File">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                        </svg>
                                                    </a>
                                                @endif
                                                @if($isLocked)
                                                    <span class="p-2.5 text-gray-400 dark:text-slate-600" title="Login or appropriate role required">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                        </svg>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Image & Video Lightbox Modal -->
    <div id="lightbox-modal"
        class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/90 backdrop-blur-sm p-4"
        onclick="if(event.target===this) closeLightbox()">

        <!-- Close button -->
        <button onclick="closeLightbox()"
            class="absolute top-4 right-4 w-10 h-10 flex items-center justify-center rounded-full bg-white/10 hover:bg-white/20 text-white transition-all z-10">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!-- Content (image or video swapped in by JS) -->
        <div class="flex flex-col items-center gap-4 max-w-5xl w-full max-h-[90vh]">

            <!-- Image mode -->
            <img id="lightbox-img" src="" alt=""
                class="max-h-[80vh] max-w-full object-contain rounded-xl shadow-2xl hidden">

            <!-- Video mode -->
            <video id="lightbox-video" controls controlsList="nodownload" class="max-h-[80vh] max-w-full rounded-xl shadow-2xl hidden"
                style="max-width:900px;">
                <source id="lightbox-video-src" src="" type="">
            </video>

            <!-- Caption + download -->
            <div class="flex items-center gap-4">
                <span id="lightbox-caption" class="text-white/70 text-sm font-semibold"></span>
                @if($isOwnerOrAdmin)
                    <a id="lightbox-download" href="#" download
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-white/10 hover:bg-white/20 text-white text-xs font-bold rounded-full transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- PDF Navigation + Lightbox Script -->
    <script>
        // ── Lightbox (Image + Video) ──────────────────────────────────────────────
        function openLightbox(src, filename) {
            const img = document.getElementById('lightbox-img');
            const video = document.getElementById('lightbox-video');
            img.src = src;
            img.alt = filename;
            img.classList.remove('hidden');
            video.classList.add('hidden');
            video.pause();
            _openLightboxShared(src, filename);
        }

        function openVideoLightbox(src, filename, ext) {
            const img = document.getElementById('lightbox-img');
            const video = document.getElementById('lightbox-video');
            const vsrc = document.getElementById('lightbox-video-src');
            img.classList.add('hidden');
            img.src = '';
            
            // Map common video extensions to correct mime types
            let mime = 'mp4';
            if(ext === 'webm') mime = 'webm';
            if(ext === 'ogg') mime = 'ogg';

            vsrc.src = src;
            vsrc.type = 'video/' + mime;
            video.load();  // reload source
            video.classList.remove('hidden');
            _openLightboxShared(src, filename);
        }

        function _openLightboxShared(src, filename) {
            document.getElementById('lightbox-caption').textContent = filename;
            const dlBtn = document.getElementById('lightbox-download');
            if (dlBtn) {
                dlBtn.href = src;
                dlBtn.download = filename;
            }
            const modal = document.getElementById('lightbox-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeLightbox() {
            const modal = document.getElementById('lightbox-modal');
            const video = document.getElementById('lightbox-video');
            const vsrc = document.getElementById('lightbox-video-src');
            video.pause();
            vsrc.src = '';      // clear the <source> element, NOT video.src
            video.load();       // fully unload buffered media
            document.getElementById('lightbox-img').src = '';
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }

        // Close on Escape key
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') closeLightbox();
        });

        // ── PDF Navigation ────────────────────────────────────────────────────────
        function jumpToPage(pageNumber, keyword) {
            // Check if we are in Mobile View (Canvas) or Desktop View (Iframe)
            if (window.innerWidth < 768) {
                // MOBILE NAVIGATION (Talks to the Canvas Engine)
                if (typeof m_pdfDoc !== 'undefined' && m_pdfDoc) {
                    m_pageNum = pageNumber;
                    renderMobilePage(pageNumber);

                    // Center viewer on screen for better focus
                    const wrapper = document.getElementById('mobile-pdf-wrapper');
                    if (wrapper) wrapper.scrollIntoView({ behavior: 'smooth', block: 'center' });

                    if (keyword) {
                        const cleanKeyword = keyword.replace(/['"]+/g, '');
                        navigator.clipboard.writeText(cleanKeyword).catch(() => { });
                        showToast(`Mobile: Page ${pageNumber}. Keyword copied!`, 'info');
                    }
                }
            } else {
                // DESKTOP NAVIGATION
                const iframe = document.getElementById('manuscript-viewer');
                const container = document.getElementById('pdf-container');

                if (iframe) {
                    if (keyword) {
                        const cleanKeyword = keyword.replace(/['"]+/g, '');
                        navigator.clipboard.writeText(cleanKeyword).catch(() => { });
                        showToast(`Navigated to Page ${pageNumber}. Press Ctrl+F and Paste to highlight.`, 'info');
                    }

                    let currentSrc = iframe.src.split('#')[0];
                    let fragment = '';

                    if (keyword) {
                        let cleanKeyword = keyword.replace(/['"]+/g, '');
                        fragment = 'search=' + encodeURIComponent(cleanKeyword) + '&page=' + pageNumber + '&view=FitH&pagemode=none&navpanes=0';
                    } else {
                        fragment = 'page=' + pageNumber + '&view=FitH&pagemode=none&navpanes=0';
                    }

                    const newSrc = currentSrc + '#' + fragment;
                    iframe.src = 'about:blank';
                    setTimeout(() => {
                        iframe.src = newSrc;
                        if (container) {
                            container.classList.add('ring-4', 'ring-indigo-500/50', 'scale-[1.005]');
                            setTimeout(() => container.classList.remove('ring-4', 'ring-indigo-500/50', 'scale-[1.005]'), 800);
                        }
                    }, 50);
                }
            }
        }

        function showToast(message, type = 'success') {
            const existing = document.getElementById('smart-toast');
            if (existing) existing.remove();

            const toast = document.createElement('div');
            toast.id = 'smart-toast';
            toast.className = `fixed bottom-8 left-1/2 -translate-x-1/2 z-[200] px-6 py-3 rounded-2xl shadow-2xl border flex items-center gap-3 transition-all duration-500 bg-gray-900 border-indigo-500/50 text-white`;

            toast.innerHTML = `
                <span class="text-lg">🔍</span>
                <p class="text-xs font-black uppercase tracking-widest leading-tight">${message}</p>
            `;

            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translate(-50%, 20px)';
                setTimeout(() => toast.remove(), 500);
            }, 8000);
        }
    </script>
</x-app-layout>