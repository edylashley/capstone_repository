<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-white leading-tight">🛡️ Security Scan — Live Demo</h2>
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition ease-in-out duration-150">
                Back to Dashboard
            </a>
        </div>
    </x-slot>

    <style>
        @keyframes scan-pulse { 0%,100% { box-shadow:0 0 0 0 rgba(99,102,241,0.2); } 50% { box-shadow:0 0 20px 8px rgba(99,102,241,0.3); } }
        @keyframes scan-line { 0% { top:0; } 100% { top:100%; } }
        @keyframes fade-in-up { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }
        @keyframes progress-fill { from { width:0; } }
        @keyframes spin-slow { 0% { transform:rotate(0); } 100% { transform:rotate(360deg); } }
        @keyframes glow-red { 0%,100% { box-shadow:0 0 0 0 rgba(239,68,68,.2); } 50% { box-shadow:0 0 24px 8px rgba(239,68,68,.25); } }
        @keyframes glow-green { 0%,100% { box-shadow:0 0 0 0 rgba(34,197,94,.2); } 50% { box-shadow:0 0 24px 8px rgba(34,197,94,.25); } }
        @keyframes shield-bounce { 0%,100% { transform:scale(1); } 50% { transform:scale(1.08); } }
        .scan-pulse { animation: scan-pulse 2s ease-in-out infinite; }
        .fade-in-up { animation: fade-in-up .5s ease-out forwards; }
        .scan-line-anim { animation: scan-line 2s linear infinite; }
        .spin-slow { animation: spin-slow 3s linear infinite; }
        .glow-red { animation: glow-red 1.5s ease-in-out infinite; }
        .glow-green { animation: glow-green 1.5s ease-in-out infinite; }
        .shield-bounce { animation: shield-bounce 1s ease-in-out infinite; }
    </style>

    <div class="py-4">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- ═══ SECTION 1: Algorithm Overview ═══ --}}
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-600 via-indigo-700 to-purple-700 px-8 py-6">
                    <h3 class="text-xl font-black text-white tracking-tight flex items-center gap-3">
                        <svg class="w-7 h-7 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        How the Security Scan Algorithm Works
                    </h3>
                    <p class="text-indigo-200 text-sm mt-1">Every file uploaded to this system passes through a multi-layered security pipeline before it is accepted.</p>
                </div>
                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- Layer 1 --}}
                        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-2xl p-5 border border-yellow-200 group hover:shadow-lg transition-all duration-300">
                            <div class="flex items-center justify-between mb-3">
                                <div class="w-10 h-10 bg-yellow-200 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-5 h-5 text-yellow-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                </div>
                                <span class="bg-yellow-500 text-yellow-950 text-[10px] font-black px-2.5 py-1 rounded-full shadow-sm tracking-wider">LAYER 1</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 text-sm">Integrity Hash & Dup Check</h4>
                                <p class="text-xs text-gray-600 mt-1 leading-relaxed">SHA-256 hash of every file is stored. Detects re-submissions of identical files (plagiarism/duplicate protection).</p>
                            </div>
                        </div>

                        {{-- Layer 2 --}}
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-5 border border-blue-200 group hover:shadow-lg transition-all duration-300">
                            <div class="flex items-center justify-between mb-3">
                                <div class="w-10 h-10 bg-blue-200 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-5 h-5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <span class="bg-blue-600 text-white text-[10px] font-black px-2.5 py-1 rounded-full shadow-sm tracking-wider">LAYER 2</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 text-sm">File Type & Extension Check</h4>
                                <p class="text-xs text-gray-600 mt-1 leading-relaxed">Validates MIME type + extension. Blocks dangerous types like <code class="bg-white text-red-600 px-1 py-0.5 rounded border border-gray-100 text-[10px] font-mono">.exe</code>, <code class="bg-white text-red-600 px-1 py-0.5 rounded border border-gray-100 text-[10px] font-mono">.bat</code>. Detects disguised binaries.</p>
                            </div>
                        </div>

                        {{-- Layer 3 --}}
                        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl p-5 border border-purple-200 group hover:shadow-lg transition-all duration-300">
                            <div class="flex items-center justify-between mb-3">
                                <div class="w-10 h-10 bg-purple-200 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-5 h-5 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                                </div>
                                <span class="bg-purple-600 text-white text-[10px] font-black px-2.5 py-1 rounded-full shadow-sm tracking-wider">LAYER 3</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 text-sm">Magic Bytes & Signature Analysis</h4>
                                <p class="text-xs text-gray-600 mt-1 leading-relaxed">Reads actual binary header (magic bytes) to verify true file type. Catches renamed executables disguised as PDFs or ZIP files.</p>
                            </div>
                        </div>

                        {{-- Layer 4 --}}
                        <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-2xl p-5 border border-red-200 group hover:shadow-lg transition-all duration-300">
                            <div class="flex items-center justify-between mb-3">
                                <div class="w-10 h-10 bg-red-200 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-5 h-5 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                </div>
                                <span class="bg-red-600 text-white text-[10px] font-black px-2.5 py-1 rounded-full shadow-sm tracking-wider">LAYER 4</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 text-sm">Malicious Pattern Scanning</h4>
                                <p class="text-xs text-gray-600 mt-1 leading-relaxed">Scans content for known malware signatures: EICAR test, PHP webshells, JS injection, VBA macros, PowerShell cradles, and more.</p>
                            </div>
                        </div>

                        {{-- Layer 5 --}}
                        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-2xl p-5 border border-green-200 group hover:shadow-lg transition-all duration-300">
                            <div class="flex items-center justify-between mb-3">
                                <div class="w-10 h-10 bg-green-200 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-5 h-5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                </div>
                                <span class="bg-green-600 text-white text-[10px] font-black px-2.5 py-1 rounded-full shadow-sm tracking-wider">LAYER 5</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 text-sm">ClamAV Antivirus Engine</h4>
                                <p class="text-xs text-gray-600 mt-1 leading-relaxed">Optional deep scan via ClamAV — 8M+ virus signatures, heuristic analysis. Status: <span class="font-bold {{ config('repository.filescan_enabled') ? 'text-green-700' : 'text-gray-500' }}">{{ config('repository.filescan_enabled') ? 'ENABLED' : 'AVAILABLE (disabled)' }}</span></p>
                            </div>
                        </div>

                        {{-- Layer 6 --}}
                        <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-2xl p-5 border border-indigo-200 group hover:shadow-lg transition-all duration-300">
                            <div class="flex items-center justify-between mb-3">
                                <div class="w-10 h-10 bg-indigo-200 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-5 h-5 text-indigo-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                </div>
                                <span class="bg-indigo-600 text-white text-[10px] font-black px-2.5 py-1 rounded-full shadow-sm tracking-wider">LAYER 6</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 text-sm">PDF Structure Validation</h4>
                                <p class="text-xs text-gray-600 mt-1 leading-relaxed">PDF-specific: extracts text directly from bytes. Strictly scans payload for missing approval cover-sheet signatures via NLP validation.</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex items-start gap-3 p-4 bg-indigo-50 rounded-xl border border-indigo-100">
                        <svg class="w-5 h-5 text-indigo-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div class="text-xs text-indigo-700">
                            <strong>How it works in practice:</strong> When a student submits a project, every uploaded file (manuscript + attachments) goes through this entire pipeline. If <em>any</em> layer detects a threat, the entire submission is <strong>blocked</strong>, the uploaded files are <strong>deleted</strong>, and the incident is <strong>logged</strong> to the activity log with full details.
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══ SECTION 2: Test Files ═══ --}}
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="px-8 py-5 border-b border-gray-100">
                    <h3 class="font-black text-gray-800 text-lg flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                        Test Files — Download & Scan
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">Download these pre-made test files, then upload them below to see how the scanner detects each type.</p>
                </div>
                <div class="p-8">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        {{-- EICAR --}}
                        <div class="p-4 rounded-xl bg-red-50 border-2 border-red-200 hover:border-red-400 transition-all group">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-8 h-8 bg-red-500 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                </div>
                                <div>
                                    <p class="font-bold text-red-700 text-sm">EICAR Test Virus</p>
                                    <p class="text-[10px] text-red-400 font-semibold">SHOULD BE DETECTED</p>
                                </div>
                            </div>
                            <p class="text-xs text-red-600 mb-3">Industry-standard antivirus test file. Completely harmless but contains the EICAR detection signature.</p>
                            <a href="{{ route('admin.security-demo.test-file', 'eicar') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Download
                            </a>
                        </div>
                        {{-- PHP Shell --}}
                        <div class="p-4 rounded-xl bg-red-50 border-2 border-red-200 hover:border-red-400 transition-all group">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-8 h-8 bg-red-500 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                                </div>
                                <div>
                                    <p class="font-bold text-red-700 text-sm">PHP Webshell</p>
                                    <p class="text-[10px] text-red-400 font-semibold">SHOULD BE DETECTED</p>
                                </div>
                            </div>
                            <p class="text-xs text-red-600 mb-3">Simulated PHP webshell with eval(), system(), and passthru() patterns — common server-side attack.</p>
                            <a href="{{ route('admin.security-demo.test-file', 'php-shell') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Download
                            </a>
                        </div>
                        {{-- JS Injection --}}
                        <div class="p-4 rounded-xl bg-amber-50 border-2 border-amber-200 hover:border-amber-400 transition-all group">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-8 h-8 bg-amber-500 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </div>
                                <div>
                                    <p class="font-bold text-amber-700 text-sm">XSS / JS Injection</p>
                                    <p class="text-[10px] text-amber-500 font-semibold">SHOULD BE DETECTED</p>
                                </div>
                            </div>
                            <p class="text-xs text-amber-600 mb-3">HTML file with JavaScript eval() and document.write() injection — cross-site scripting (XSS) attack.</p>
                            <a href="{{ route('admin.security-demo.test-file', 'js-injection') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-lg transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Download
                            </a>
                        </div>
                        {{-- Clean PDF --}}
                        <div class="p-4 rounded-xl bg-green-50 border-2 border-green-200 hover:border-green-400 transition-all group">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div>
                                    <p class="font-bold text-green-700 text-sm">Clean PDF</p>
                                    <p class="text-[10px] text-green-500 font-semibold">SHOULD PASS</p>
                                </div>
                            </div>
                            <p class="text-xs text-green-600 mb-3">A minimal, clean PDF file with no malicious content — should pass all security checks successfully.</p>
                            <a href="{{ route('admin.security-demo.test-file', 'clean-pdf') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-bold rounded-lg transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Download
                            </a>
                        </div>
                        {{-- Clean Text --}}
                        <div class="p-4 rounded-xl bg-green-50 border-2 border-green-200 hover:border-green-400 transition-all group">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div>
                                    <p class="font-bold text-green-700 text-sm">Clean Text File</p>
                                    <p class="text-[10px] text-green-500 font-semibold">SHOULD PASS</p>
                                </div>
                            </div>
                            <p class="text-xs text-green-600 mb-3">A plain text file with harmless content — should pass all security checks without any alerts.</p>
                            <a href="{{ route('admin.security-demo.test-file', 'clean-text') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-bold rounded-lg transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Download
                            </a>
                        </div>
                        {{-- Upload Your Own --}}
                        <div class="p-4 rounded-xl bg-gray-50 border-2 border-dashed border-gray-300 hover:border-indigo-400 transition-all flex flex-col items-center justify-center text-center group">
                            <div class="w-8 h-8 bg-gray-200 group-hover:bg-indigo-100 rounded-lg flex items-center justify-center mb-2 transition-colors">
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-indigo-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            </div>
                            <p class="font-bold text-gray-500 text-sm">Or upload any file</p>
                            <p class="text-[10px] text-gray-400 mt-1">Use the scanner below</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══ SECTION 3: Live Scanner ═══ --}}
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="px-8 py-5 border-b border-gray-100">
                    <h3 class="font-black text-gray-800 text-lg flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Live Security Scanner
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">Upload any file below to run it through the full security pipeline in real-time.</p>
                </div>
                <div class="p-8">
                    {{-- Upload Area --}}
                    <input type="file" id="scan-file-input" class="hidden" accept="*/*">
                    <div id="drop-zone"
                         class="relative border-2 border-dashed border-gray-300 hover:border-indigo-400 rounded-2xl p-12 text-center transition-all duration-300 cursor-pointer group"
                         onclick="document.getElementById('scan-file-input').click()">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-16 h-16 bg-indigo-100 group-hover:bg-indigo-200 rounded-2xl flex items-center justify-center transition-colors">
                                <svg class="w-8 h-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-700">Drop a file here or click to browse</p>
                                <p class="text-xs text-gray-400 mt-1">Any file type up to 50MB — it will be scanned and immediately deleted</p>
                            </div>
                        </div>
                        {{-- Scanning overlay --}}
                        <div id="scan-overlay" class="hidden absolute inset-0 bg-white/95 backdrop-blur-sm rounded-2xl flex flex-col items-center justify-center gap-4">
                            <div class="relative w-20 h-20">
                                <svg class="spin-slow w-20 h-20 text-indigo-500" viewBox="0 0 50 50" fill="none">
                                    <circle cx="25" cy="25" r="21" stroke="#e0e7ff" stroke-width="3"></circle>
                                    <path d="M25 4 a21 21 0 0 1 21 21" stroke="currentColor" stroke-width="3" stroke-linecap="round"></path>
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-indigo-600 shield-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                </div>
                            </div>
                            <div class="text-center">
                                <p class="font-black text-gray-800 text-sm">Scanning File...</p>
                                <p id="scan-status-text" class="text-xs text-indigo-600 font-semibold mt-1">Analyzing file header</p>
                            </div>
                        </div>
                    </div>

                    {{-- Results Container --}}
                    <div id="scan-results" class="hidden mt-8 space-y-6">

                        {{-- Verdict Banner --}}
                        <div id="verdict-banner" class="rounded-2xl p-6 flex items-center gap-5 transition-all duration-500"></div>

                        {{-- File Info --}}
                        <div id="file-info-card" class="bg-gray-50 rounded-xl p-5 border border-gray-100">
                            <h4 class="text-xs font-black text-gray-500 uppercase tracking-wider mb-3">File Information</h4>
                            <div id="file-info-grid" class="grid grid-cols-2 md:grid-cols-4 gap-4"></div>
                        </div>

                        {{-- Step-by-Step Results --}}
                        <div>
                            <h4 class="text-xs font-black text-gray-500 uppercase tracking-wider mb-3">Scan Pipeline — Step-by-Step Results</h4>
                            <div id="steps-container" class="space-y-3"></div>
                        </div>

                        {{-- Scan Again --}}
                        <div class="text-center pt-4">
                            <button onclick="resetScanner()" class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-100 hover:shadow-indigo-200 transition-all text-sm uppercase tracking-wider">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                Scan Another File
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        const fileInput  = document.getElementById('scan-file-input');
        const dropZone   = document.getElementById('drop-zone');
        const overlay    = document.getElementById('scan-overlay');
        const statusText = document.getElementById('scan-status-text');
        const resultsEl  = document.getElementById('scan-results');

        // ── Status messages cycle ──
        const statusMessages = [
            'Analyzing file header...',
            'Reading magic bytes...',
            'Checking file extension...',
            'Scanning for malware signatures...',
            'Running pattern matching...',
            'Verifying integrity hash...',
            'Finalizing scan...',
        ];

        let statusInterval = null;

        // ── Drag & Drop ──
        ['dragenter','dragover'].forEach(ev => {
            dropZone.addEventListener(ev, e => { e.preventDefault(); dropZone.classList.add('border-indigo-500','bg-indigo-50'); });
        });
        ['dragleave','drop'].forEach(ev => {
            dropZone.addEventListener(ev, e => { e.preventDefault(); dropZone.classList.remove('border-indigo-500','bg-indigo-50'); });
        });
        dropZone.addEventListener('drop', e => {
            if (e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                startScan(e.dataTransfer.files[0]);
            }
        });

        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                const fileToScan = this.files[0];
                this.value = ''; // Reset to allow re-selecting the same file
                startScan(fileToScan);
            }
        });

        function startScan(file) {
            // Show overlay
            overlay.classList.remove('hidden');
            resultsEl.classList.add('hidden');

            // Status cycle
            let idx = 0;
            statusText.textContent = statusMessages[0];
            statusInterval = setInterval(() => {
                idx = (idx + 1) % statusMessages.length;
                statusText.textContent = statusMessages[idx];
            }, 1200);

            // Upload + scan via AJAX
            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', '{{ csrf_token() }}');

            fetch('{{ route("admin.security-demo.scan") }}', {
                method: 'POST',
                body: formData,
            })
            .then(res => res.json())
            .then(data => {
                clearInterval(statusInterval);
                overlay.classList.add('hidden');
                displayResults(data);
            })
            .catch(err => {
                clearInterval(statusInterval);
                overlay.classList.add('hidden');
                alert('Scan failed: ' + err.message);
            });
        }

        function displayResults(data) {
            resultsEl.classList.remove('hidden');

            // ── Verdict Banner ──
            const banner = document.getElementById('verdict-banner');
            if (data.overall_verdict === 'clean') {
                banner.className = 'rounded-2xl p-6 flex items-center gap-5 bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 glow-green fade-in-up';
                banner.innerHTML = `
                    <div class="w-16 h-16 bg-green-500 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-green-200">
                        <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <h3 class="text-2xl font-black text-green-700">FILE IS CLEAN</h3>
                        <p class="text-sm text-green-600 mt-1">No threats detected. This file passed all security checks.</p>
                    </div>`;
            } else if (data.overall_verdict === 'suspicious') {
                banner.className = 'rounded-2xl p-6 flex items-center gap-5 bg-gradient-to-r from-amber-50 to-yellow-50 border-2 border-amber-200 fade-in-up';
                banner.innerHTML = `
                    <div class="w-16 h-16 bg-amber-500 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-amber-200">
                        <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-2xl font-black text-amber-700">SUSPICIOUS FILE</h3>
                        <p class="text-sm text-amber-600 mt-1">Potentially dangerous content detected. This file would be flagged for review.</p>
                        <p class="text-xs text-amber-500 mt-2 font-semibold">Threats: ${data.threats_found.join(', ')}</p>
                    </div>`;
            } else {
                banner.className = 'rounded-2xl p-6 flex items-center gap-5 bg-gradient-to-r from-red-50 to-rose-50 border-2 border-red-200 glow-red fade-in-up';
                banner.innerHTML = `
                    <div class="w-16 h-16 bg-red-500 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-red-200">
                        <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    </div>
                    <div>
                        <h3 class="text-2xl font-black text-red-700">MALICIOUS FILE BLOCKED</h3>
                        <p class="text-sm text-red-600 mt-1">Threats detected! This file would be <strong>rejected and deleted</strong> by the system.</p>
                        <p class="text-xs text-red-500 mt-2 font-semibold">Threats: ${data.threats_found.join(', ')}</p>
                    </div>`;
            }

            // ── File Info ──
            const grid = document.getElementById('file-info-grid');
            const info = data.file_info;
            grid.innerHTML = `
                <div class="fade-in-up" style="animation-delay:.1s">
                    <p class="text-[10px] text-gray-400 font-bold uppercase">Filename</p>
                    <p class="text-sm text-gray-800 font-semibold truncate">${info.name}</p>
                </div>
                <div class="fade-in-up" style="animation-delay:.15s">
                    <p class="text-[10px] text-gray-400 font-bold uppercase">Size</p>
                    <p class="text-sm text-gray-800 font-semibold">${info.size_human}</p>
                </div>
                <div class="fade-in-up" style="animation-delay:.2s">
                    <p class="text-[10px] text-gray-400 font-bold uppercase">MIME Type</p>
                    <p class="text-sm text-gray-800 font-semibold">${info.mime_type}</p>
                </div>
                <div class="fade-in-up" style="animation-delay:.25s">
                    <p class="text-[10px] text-gray-400 font-bold uppercase">Extension</p>
                    <p class="text-sm text-gray-800 font-semibold">.${info.extension}</p>
                </div>
                <div class="col-span-full fade-in-up" style="animation-delay:.3s">
                    <p class="text-[10px] text-gray-400 font-bold uppercase">SHA-256 Hash</p>
                    <p class="text-xs text-gray-600 font-mono break-all">${info.sha256}</p>
                </div>
            `;

            // ── Steps ──
            const stepsContainer = document.getElementById('steps-container');
            stepsContainer.innerHTML = '';

            data.steps.forEach((step, i) => {
                const delay = (i * 0.15) + 0.3;
                let borderColor, bgColor, iconColor, iconSvg, statusBadge;

                if (step.status === 'pass') {
                    borderColor = 'border-green-200';
                    bgColor = 'bg-green-50';
                    iconColor = 'text-green-500';
                    iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>';
                    statusBadge = '<span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-black rounded-full uppercase">Pass</span>';
                } else if (step.status === 'fail') {
                    borderColor = 'border-red-200';
                    bgColor = 'bg-red-50';
                    iconColor = 'text-red-500';
                    iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>';
                    statusBadge = '<span class="px-2 py-0.5 bg-red-100 text-red-700 text-[10px] font-black rounded-full uppercase">Threat Detected</span>';
                } else if (step.status === 'warning') {
                    borderColor = 'border-amber-200';
                    bgColor = 'bg-amber-50';
                    iconColor = 'text-amber-500';
                    iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>';
                    statusBadge = '<span class="px-2 py-0.5 bg-amber-100 text-amber-700 text-[10px] font-black rounded-full uppercase">Warning</span>';
                } else {
                    borderColor = 'border-gray-200';
                    bgColor = 'bg-gray-50';
                    iconColor = 'text-gray-400';
                    iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>';
                    statusBadge = '<span class="px-2 py-0.5 bg-gray-100 text-gray-500 text-[10px] font-black rounded-full uppercase">Skipped</span>';
                }

                const detailsHtml = step.details.map(d => {
                    let cls = 'text-gray-600';
                    if (d.startsWith('⚠')) cls = 'text-red-600 font-semibold';
                    else if (d.startsWith('✓')) cls = 'text-green-600';
                    else if (d.startsWith('ℹ')) cls = 'text-blue-600';
                    return `<li class="text-xs ${cls}">${escapeHtml(d)}</li>`;
                }).join('');

                const stepHtml = `
                <div class="rounded-xl border-2 ${borderColor} ${bgColor} overflow-hidden fade-in-up" style="animation-delay:${delay}s">
                    <div class="px-5 py-4 flex items-center gap-4">
                        <div class="w-9 h-9 rounded-lg bg-white border ${borderColor} flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 ${iconColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24">${iconSvg}</svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-sm text-gray-800">Step ${i + 1}: ${escapeHtml(step.name)}</span>
                                ${statusBadge}
                            </div>
                            <p class="text-[11px] text-gray-500 mt-0.5 truncate">${escapeHtml(step.description)}</p>
                        </div>
                        <button onclick="this.closest('.rounded-xl').querySelector('.step-details').classList.toggle('hidden')"
                                class="text-xs text-gray-400 hover:text-indigo-600 font-bold uppercase tracking-wider flex-shrink-0 transition-colors">
                            Details ▾
                        </button>
                    </div>
                    <div class="step-details hidden border-t ${borderColor} px-5 py-4 space-y-3">
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase mb-1">Algorithm</p>
                            <p class="text-xs text-gray-600 leading-relaxed">${escapeHtml(step.algorithm)}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase mb-1">Results</p>
                            <ul class="space-y-1">${detailsHtml}</ul>
                        </div>
                    </div>
                </div>`;

                stepsContainer.insertAdjacentHTML('beforeend', stepHtml);
            });

            // Auto-expand failed/warning steps
            setTimeout(() => {
                stepsContainer.querySelectorAll('.rounded-xl').forEach(el => {
                    if (el.classList.contains('border-red-200') || el.classList.contains('border-amber-200')) {
                        el.querySelector('.step-details')?.classList.remove('hidden');
                    }
                });
            }, 600);
        }

        function resetScanner() {
            resultsEl.classList.add('hidden');
            fileInput.value = '';
            dropZone.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        function escapeHtml(str) {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }
    </script>
</x-app-layout>
