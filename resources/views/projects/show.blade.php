<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">{{ $project->title }}</h2>
            <a href="{{ url()->previous() }}" class="px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition ease-in-out duration-150">
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col gap-6 @if(optional(auth()->user())->isAdviser() && $project->status === 'pending' && $project->adviser_id === auth()->id()) lg:flex-row @endif">
                
                <!-- Metadata Side -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 @if(optional(auth()->user())->isAdviser() && $project->status === 'pending' && $project->adviser_id === auth()->id()) lg:w-1/3 @else w-full @endif">
                    <div class="mb-4 space-y-2">
                        <div class="flex justify-between items-start border-b border-gray-100 dark:border-gray-700 pb-4 mb-4">
                            <div>
                                <h3 class="text-xl font-bold uppercase tracking-tight text-gray-900 dark:text-gray-300 leading-none mb-1">Project Details</h3>
                                <p class="text-xs text-gray-500">ID: #{{ str_pad($project->id, 5, '0', STR_PAD_LEFT) }}</p>
                            </div>
                            @if(optional(auth()->user())->isAdviser() && $project->status === 'pending' && $project->adviser_id === auth()->id())
                                <span class="bg-indigo-600 text-white text-[10px] font-black uppercase px-2 py-1 rounded">Review Mode</span>
                            @endif
                        </div>

                        <p class="text-gray-700 dark:text-gray-300"><strong class="text-white">Year:</strong> {{ $project->year }}</p>
                        <p class="text-gray-700 dark:text-gray-300"><strong class="text-white">Adviser:</strong> {{ $project->adviser->name ?? $project->adviser_name ?? '-' }}</p>
                        <p class="text-gray-700 dark:text-gray-300"><strong class="text-white">Program:</strong> {{ $project->program ?? '-' }}</p>
                        <p class="text-gray-700 dark:text-gray-300"><strong class="text-white">Authors:</strong> {{ $project->authors_list ?: $project->authors->pluck('name')->join(', ') }}</p>
                        <p class="pt-2 border-t border-gray-100 dark:border-gray-700 mt-2 text-gray-700 dark:text-gray-300"><strong class="text-white">Abstract:</strong></p>
                        <p class="text-sm text-gray-600 dark:text-gray-300 italic leading-relaxed">{{ $project->abstract }}</p>
                        <p class="pt-2 border-t mt-2 text-gray-300"><strong class="text-gray-500">Categories:</strong></p>
                        <div class="text-sm mt-1 flex flex-wrap gap-2">
                            <!-- Show Specialization as Plain Text -->
                            @if($project->specialization)
                                <span class="text-gray-300 font-medium">
                                    {{ $project->specialization }}
                                </span>
                            @endif

                            <!-- Show Keywords as Hash Tags -->
                            @if(is_array($project->keywords))
                                @foreach($project->keywords as $kw)
                                    <span class="bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded-full border border-gray-200">#{{ $kw }}</span>
                                @endforeach
                            @elseif(!empty($project->keywords))
                                <span class="text-gray-400">{{ $project->keywords }}</span>
                            @endif

                            @if(empty($project->specialization) && empty($project->keywords))
                                <span class="text-gray-500 italic text-xs">Uncategorized</span>
                            @endif
                        </div>

                        @if($project->status === 'published' || (auth()->check() && (auth()->user()->isAdviser() || auth()->user()->isAdmin() || $project->authors->contains(auth()->user()))))
                            @if($project->status === 'published')
                                <div class="mt-4 p-5 border-2 border-emerald-500/30 bg-emerald-500/10 rounded-2xl shadow-sm">
                                    <span class="text-white/50 uppercase text-[10px] font-bold tracking-widest block mb-1">RECORD STATUS:</span> 
                                    <div class="flex items-center gap-3">
                                        <div class="relative flex h-3 w-3">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-500 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.4)]"></span>
                                        </div>
                                        <span class="text-emerald-400 font-black text-sm uppercase tracking-tight italic">Official Institutional Record</span>
                                    </div>
                                </div>
                            @elseif($project->status === 'approved')
                                <div class="mt-4 p-5 border-2 border-indigo-500/30 bg-indigo-500/10 rounded-2xl shadow-sm">
                                    <span class="text-white/50 uppercase text-[10px] font-bold tracking-widest block mb-1">RECORD STATUS:</span> 
                                    <div class="flex items-center gap-3">
                                        <span class="relative inline-flex rounded-full h-3 w-3 bg-indigo-500 shadow-[0_0_10px_rgba(99,102,241,0.4)]"></span>
                                        <span class="text-indigo-400 font-black text-sm uppercase tracking-tight italic">Confirmed Final Version</span>
                                    </div>
                                </div>
                            @elseif($project->status === 'rejected')
                                <div class="mt-4 p-5 border-2 border-red-500/30 bg-red-500/10 rounded-2xl shadow-sm">
                                    <span class="text-white/50 uppercase text-[10px] font-bold tracking-widest block mb-1">RECORD STATUS:</span> 
                                    <div class="flex items-center gap-3">
                                        <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500 shadow-[0_0_10px_rgba(239,68,68,0.4)]"></span>
                                        <span class="text-red-400 font-black text-sm uppercase tracking-tight italic">Returned</span>
                                    </div>
                                    @if($project->rejection_reason)
                                        <div class="mt-4 pt-4 border-t border-red-500/20">
                                            <p class="text-[10px] text-gray-300 uppercase font-bold tracking-widest mb-2">Adviser's Feedback:</p>
                                            <p class="text-sm text-gray-200 leading-relaxed whitespace-pre-wrap bg-red-500/10 rounded-lg p-3 border border-red-500/20">{{ $project->rejection_reason }}</p>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="mt-4 p-5 border-2 border-yellow-500/30 bg-yellow-500/10 rounded-2xl shadow-sm">
                                    <span class="text-white/50 uppercase text-[10px] font-bold tracking-widest block mb-1">RECORD STATUS:</span> 
                                    <div class="flex items-center gap-3">
                                        <span class="relative inline-flex rounded-full h-3 w-3 bg-yellow-500 shadow-[0_0_10px_rgba(234,179,8,0.4)]"></span>
                                        <span class="text-yellow-300 font-black text-sm uppercase tracking-tight italic">Archive Processing</span>
                                    </div>
                                </div>
                            @endif


                            <!-- ADVISER DECISION HUB ACTION -->
                            @if(optional(auth()->user())->isAdviser() && $project->status === 'pending' && $project->adviser_id === auth()->id())
                                <div class="mt-8 pt-6 border-t border-gray-700/50">
                                    <!-- Assistant's Report Card -->
                                    <div class="mb-6 p-4 rounded-xl {{ $project->manuscript_validated ? 'bg-emerald-500/10 border border-emerald-500/20' : 'bg-yellow-500/10 border border-yellow-500/20' }}">
                                        <div class="flex items-center gap-2 mb-3">
                                            <span class="text-lg">⚙️</span>
                                            <h4 class="font-bold text-[11px] tracking-widest uppercase {{ $project->manuscript_validated ? 'text-emerald-400' : 'text-yellow-400' }}">
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
                                                    <div class="mt-3 mb-1 text-[9px] font-bold text-gray-500 uppercase tracking-widest border-b border-gray-700/50 pb-1">
                                                        Keywords Found
                                                    </div>
                                                    @php $hasPrintedHeader = true; @endphp
                                                @endif
                                                <div class="flex items-start gap-2">
                                                    <span class="text-xs {{ str_contains($note, '✓') || !str_contains($note, 'Warning') ? 'text-emerald-500' : 'text-yellow-500' }}">●</span>
                                                    <p class="text-[11px] leading-tight text-gray-300">
                                                        @if(str_starts_with($note, 'Detected:'))
                                                            @php
                                                                // Extract keyword and page list: "Detected: approval (Pages 1, 2, 3)"
                                                                $formattedNote = preg_replace_callback('/Detected:\s+(.+?)\s+\(Pages\s+([\d,\s]+)\)/', function($matches) {
                                                                    $keyword = trim($matches[1]);
                                                                    $pageListStr = $matches[2]; // "1, 2, 3"
                                                                    $pages = explode(',', $pageListStr);
                                                                    
                                                                    $links = [];
                                                                    $jsKeyword = addslashes($keyword);
                                                                    foreach($pages as $p) {
                                                                        $p = trim($p);
                                                                        $links[] = '<a href="#" onclick="jumpToPage('.$p.', \''.$jsKeyword.'\'); return false;" class="text-indigo-400 hover:text-indigo-300 underline font-bold" title="Jump to Page '.$p.'">'.$p.'</a>';
                                                                    }
                                                                    
                                                                    return '<strong class="text-gray-400">'.$keyword.'</strong> found on pages: ' . implode(', ', $links);
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
                                                    <strong>Note:</strong> Signature page or min. page count not detected. Please manually verify the uploaded PDF before confirming.
                                                </p>
                                            </div>
                                        @endif
                                        
                                        <form method="POST" action="{{ route('projects.reverify-pdf', $project->id) }}" class="mt-4 border-t border-dashed border-gray-600/50 pt-2">
                                            @csrf
                                            <button type="submit" class="flex items-center gap-2 text-[10px] text-gray-400 hover:text-indigo-400 transition-colors">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                                Run System Scan Again
                                            </button>
                                        </form>
                                    </div>

                                    <h4 class="font-black text-gray-400 uppercase text-xs mb-4">Verification Actions</h4>
                                    <form method="POST" action="{{ route('faculty.projects.approve', $project) }}" onsubmit="return confirm('Confirm that this is the final, defended version of the project?');">
                                        @csrf
                                        <button type="submit" class="w-full bg-indigo-600 text-white font-black py-4 rounded-lg shadow-lg hover:shadow-xl hover:bg-indigo-700 transition-all border-b-4 border-indigo-800 active:translate-y-1 active:border-b-0 uppercase tracking-widest text-sm text-nowrap">
                                            Confirm Final Record
                                        </button>
                                    </form>
                                    <p class="text-[10px] text-gray-500 mt-2 text-center italic">Digital signature will be recorded upon confirmation.</p>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- PDF Layout (Side or Bottom) -->
                <div class="flex-1 space-y-6 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700 sm:rounded-lg ">
                    <div class="overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="font-bold text-lg mb-4 border-b pb-2">Manuscript Viewer</h3>
                        @php
                            $manuscript = $project->files->firstWhere('type', 'manuscript');
                            $attachments = $project->files->where('type', 'attachment');
                        @endphp

                        @if($manuscript)
                            @auth
                                <div class="mt-4 border-4 border-gray-100 rounded-xl overflow-hidden shadow-inner transition-all duration-300" id="pdf-container">
                                    <iframe id="manuscript-viewer" src="{{ route('files.view', $manuscript) }}" width="100%" height="800px" style="min-height: 800px;" class="w-full h-[800px] bg-gray-500"></iframe>
                                </div>

                                <div class="mt-4 flex justify-between items-center bg-gray-50 p-4 rounded-lg">
                                    <span class="text-xs text-gray-500 italic">Verify all signatures on the manuscript before confirmation.</span>
                                    
                                    <div class="flex items-center gap-4">
                                        @if(auth()->user()->isAdmin() || auth()->user()->isAdviser() || $project->authors->contains(auth()->user()))
                                            <span class="text-xs font-bold text-gray-500 uppercase tracking-widest bg-gray-200 px-2 py-1 rounded">
                                                {{ number_format($manuscript->size / 1048576, 2) }} MB
                                            </span>
                                        @endif

                                        <a href="{{ route('files.download', $manuscript->id) }}" class="px-4 py-2 bg-gray-800 text-white rounded-md text-sm font-bold shadow hover:bg-gray-900 transition flex items-center gap-2">
                                            Download PDF
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="mt-4 p-12 border-4 border-dashed border-gray-200 bg-gray-50 rounded-xl flex flex-col items-center justify-center text-center">
                                    <div class="w-20 h-20 bg-gray-200 text-gray-400 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                    </div>
                                    <h4 class="text-xl font-bold text-gray-900 mb-2">Manuscript is Secured</h4>
                                    <p class="text-sm text-gray-500 mb-6 max-w-sm">Full research manuscripts and technical attachments can only be accessed by registered students and faculty members.</p>
                                    <a href="{{ route('login') }}" class="px-6 py-3 bg-indigo-600 text-white font-bold rounded-lg shadow-md hover:bg-indigo-700 hover:shadow-lg transition-all duration-200">
                                        Login to View Full Project
                                    </a>
                                </div>
                            @endauth
                        @else
                            <div class="p-12 text-center bg-gray-50 rounded-lg text-gray-500">
                                No manuscript file found for this record.
                            </div>
                        @endif
                    </div>

                    {{-- Attachments: restricted to advisers, admins, and the project's own authors --}}
                    @php
                        $canSeeAttachments = auth()->check() && (
                            auth()->user()->isAdmin() ||
                            auth()->user()->isAdviser() ||
                            $project->authors->contains(auth()->user())
                        );
                    @endphp

                    @if($canSeeAttachments && $attachments->isNotEmpty())
                        <div class="dark:bg-gray-800  overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <div class="flex items-center justify-between mb-4 border-b pb-2">
                                <h3 class="font-bold text-lg">Project Attachments</h3>
                                <span class="px-2 py-0.5 bg-gray-100 text-[10px] font-black text-gray-800 uppercase rounded">{{ $attachments->count() }} Technical Files</span>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($attachments as $file)
                                    @php
                                        $ext = strtolower(pathinfo($file->filename, PATHINFO_EXTENSION));
                                        $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                        $isVideo = in_array($ext, ['mp4', 'mov', 'webm', 'avi']);
                                        $isPreviewable = $isImage || $isVideo;
                                        $icon = match(true) {
                                            in_array($ext, ['zip', 'rar', '7z'])           => '📦',
                                            in_array($ext, ['mp4', 'mov', 'webm', 'avi'])  => '🎬',
                                            in_array($ext, ['sql', 'db'])                  => '🗄️',
                                            in_array($ext, ['pdf'])                        => '📄',
                                            in_array($ext, ['doc', 'docx'])               => '📝',
                                            in_array($ext, ['ppt', 'pptx'])               => '📊',
                                            in_array($ext, ['xls', 'xlsx', 'csv'])        => '📊',
                                            in_array($ext, ['jpg', 'jpeg', 'png', 'gif']) => '🖼️',
                                            in_array($ext, ['txt', 'md'])                  => '📃',
                                            in_array($ext, ['json', 'xml'])               => '🔧',
                                            default                                        => '📎'
                                        };
                                        $fileUrl = route('files.view', $file);
                                    @endphp
                                    <div class="flex flex-col border border-gray-100 rounded-xl bg-gray-50/50 hover:bg-gray-50 transition overflow-hidden">
                                        <div class="flex items-center justify-between p-4">
                                            <div class="flex items-center gap-3 overflow-hidden">
                                                <div class="w-10 h-10 rounded-lg bg-white flex items-center justify-center shadow-sm border border-gray-100 flex-shrink-0">
                                                    <span class="text-xl">{{ $icon }}</span>
                                                </div>
                                                <div class="flex flex-col overflow-hidden">
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-sm font-bold text-gray-900 truncate" title="{{ $file->filename }}">{{ $file->filename }}</span>
                                                        @if($isImage)
                                                            <span class="bg-emerald-100 text-emerald-700 text-[8px] font-black px-1.5 py-0.5 rounded uppercase tracking-widest">Preview</span>
                                                        @elseif($isVideo)
                                                            <span class="bg-indigo-100 text-indigo-700 text-[8px] font-black px-1.5 py-0.5 rounded uppercase tracking-widest">Video Preview</span>
                                                        @endif
                                                    </div>
                                                    @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isAdviser() || $project->authors->contains(auth()->user())))
                                                        <span class="text-[10px] uppercase font-black text-gray-800 tracking-tighter">
                                                            {{ strtoupper($ext) }} • {{ number_format($file->size / 1048576, 2) }} MB
                                                        </span>
                                                    @else
                                                        <span class="text-[10px] uppercase font-black text-gray-400 tracking-tighter">
                                                            {{ strtoupper($ext) }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <a href="{{ route('files.download', $file->id) }}"
                                               class="p-2 hover:bg-indigo-50 text-indigo-600 rounded-lg transition-colors flex-shrink-0" title="Download File">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                            </a>
                                        </div>

                                        {{-- Inline image preview --}}
                                        @if($isImage)
                                            <div class="px-4 pb-4">
                                                <div class="rounded-lg overflow-hidden border border-gray-200 bg-gray-100 shadow-inner flex items-center justify-center relative group cursor-zoom-in"
                                                     onclick="openLightbox('{{ $fileUrl }}', '{{ addslashes($file->filename) }}')">
                                                    <img src="{{ $fileUrl }}"
                                                         alt="{{ $file->filename }}"
                                                         class="max-h-[300px] w-full object-contain transition-transform duration-200 group-hover:scale-[1.02]"
                                                         loading="lazy">
                                                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-all duration-200 flex items-center justify-center">
                                                        <span class="opacity-0 group-hover:opacity-100 transition-opacity bg-black/60 text-white text-xs font-bold px-3 py-1.5 rounded-full">
                                                            🔍 Click to enlarge
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                        {{-- Inline video preview --}}
                                        @elseif($isVideo)
                                            <div class="px-4 pb-4">
                                                <div class="rounded-lg overflow-hidden border border-gray-200 bg-black shadow-inner relative group cursor-pointer"
                                                     onclick="openVideoLightbox('{{ $fileUrl }}', '{{ addslashes($file->filename) }}', '{{ $ext === 'mov' ? 'quicktime' : $ext }}')">
                                                    <video class="w-full max-h-[300px] pointer-events-none">
                                                        <source src="{{ $fileUrl }}" type="video/{{ $ext === 'mov' ? 'quicktime' : $ext }}">
                                                    </video>
                                                    <div class="absolute inset-0 bg-black/40 group-hover:bg-black/60 transition-all flex items-center justify-center">
                                                        <span class="bg-white/10 hover:bg-white/20 border border-white/30 text-white text-xs font-bold px-4 py-2 rounded-full backdrop-blur-sm">
                                                            ⛶ Click to expand
                                                        </span>
                                                    </div>
                                                </div>
                                                <p class="text-[9px] text-gray-400 mt-2 text-center italic">Video Demo — click to open full player.</p>
                                            </div>
                                        @endif
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
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <!-- Content (image or video swapped in by JS) -->
        <div class="flex flex-col items-center gap-4 max-w-5xl w-full max-h-[90vh]">

            <!-- Image mode -->
            <img id="lightbox-img"
                 src="" alt=""
                 class="max-h-[80vh] max-w-full object-contain rounded-xl shadow-2xl hidden">

            <!-- Video mode -->
            <video id="lightbox-video"
                   controls
                   class="max-h-[80vh] max-w-full rounded-xl shadow-2xl hidden"
                   style="max-width:900px;">
                <source id="lightbox-video-src" src="" type="">
            </video>

            <!-- Caption + download -->
            <div class="flex items-center gap-4">
                <span id="lightbox-caption" class="text-white/70 text-sm font-semibold"></span>
                <a id="lightbox-download" href="#" download
                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-white/10 hover:bg-white/20 text-white text-xs font-bold rounded-full transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download
                </a>
            </div>
        </div>
    </div>

    <!-- PDF Navigation + Lightbox Script -->
    <script>
        // ── Lightbox (Image + Video) ──────────────────────────────────────────────
        function openLightbox(src, filename) {
            const img   = document.getElementById('lightbox-img');
            const video = document.getElementById('lightbox-video');
            img.src = src;
            img.alt = filename;
            img.classList.remove('hidden');
            video.classList.add('hidden');
            video.pause();
            _openLightboxShared(src, filename);
        }

        function openVideoLightbox(src, filename, mimeType) {
            const img   = document.getElementById('lightbox-img');
            const video = document.getElementById('lightbox-video');
            const vsrc  = document.getElementById('lightbox-video-src');
            img.classList.add('hidden');
            img.src = '';
            vsrc.src  = src;
            vsrc.type = 'video/' + mimeType;
            video.load();  // reload source
            video.classList.remove('hidden');
            _openLightboxShared(src, filename);
        }

        function _openLightboxShared(src, filename) {
            document.getElementById('lightbox-caption').textContent = filename;
            document.getElementById('lightbox-download').href = src;
            document.getElementById('lightbox-download').download = filename;
            const modal = document.getElementById('lightbox-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeLightbox() {
            const modal = document.getElementById('lightbox-modal');
            const video = document.getElementById('lightbox-video');
            const vsrc  = document.getElementById('lightbox-video-src');
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
            const iframe = document.getElementById('manuscript-viewer');
            const container = document.getElementById('pdf-container');
            
            if (iframe) {
                let currentSrc = iframe.src.split('#')[0];
                let newSrc = currentSrc + '#page=' + pageNumber + '&zoom=100';
                if (keyword) {
                    newSrc += '&search="' + encodeURIComponent(keyword) + '"';
                }
                iframe.src = 'about:blank';
                setTimeout(() => { iframe.src = newSrc; }, 50);

                if(container) {
                    container.classList.add('ring-4', 'ring-indigo-500', 'scale-[1.01]', 'shadow-2xl');
                    setTimeout(() => {
                        container.classList.remove('ring-4', 'ring-indigo-500', 'scale-[1.01]', 'shadow-2xl');
                    }, 800);
                }
            }
        }
    </script>
</x-app-layout>