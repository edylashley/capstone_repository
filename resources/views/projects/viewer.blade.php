@extends('layouts.app')

@section('content')
<div class="fixed inset-0 bg-gray-900 z-[100] flex flex-col overflow-hidden">
    <!-- Header -->
    <div class="bg-gray-800/95 backdrop-blur-md border-b border-white/10 p-3 md:p-4 flex items-center justify-between shadow-2xl relative z-[300]">
        <div class="flex items-center gap-3 overflow-hidden flex-1">
            <button onclick="window.close(); if(!window.closed) window.history.back();" class="flex-shrink-0 p-2 text-white hover:bg-white/10 rounded-full transition active:scale-90">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <div class="overflow-hidden hidden sm:block">
                <h1 class="text-white font-black uppercase tracking-tighter text-xs md:text-sm leading-tight truncate max-w-[150px] md:max-w-md">{{ $project->title }}</h1>
                <p class="text-indigo-400 text-[8px] md:text-[9px] font-black uppercase tracking-[0.2em] mt-0.5">HD Manuscript Engine</p>
            </div>
        </div>
        
        <!-- Desktop Central Navigation -->
        <div class="hidden md:flex items-center gap-6 bg-gray-900/50 px-6 py-2 rounded-2xl border border-white/5 shadow-inner">
            <div class="flex items-center gap-4">
                <button onclick="jumpToPrev()" class="p-2 text-white hover:bg-indigo-600 rounded-xl transition active:scale-90 shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
                </button>
                
                <div class="flex flex-col items-center min-w-[80px]">
                    <span class="text-[8px] font-black text-gray-500 uppercase tracking-widest mb-0.5">Page</span>
                    <span class="text-lg font-black text-white tracking-tighter leading-none"><span id="current-page">1</span> <span class="text-gray-700 mx-1">/</span> <span id="total-pages">-</span></span>
                </div>

                <button onclick="jumpToNext()" class="p-2 text-white hover:bg-indigo-600 rounded-xl transition active:scale-90 shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
            <div class="h-8 w-[1px] bg-white/10"></div>
            <div class="flex flex-col">
                <span class="text-[8px] font-black text-gray-500 uppercase tracking-widest mb-0.5">Status</span>
                <span class="text-[10px] font-bold text-indigo-400 uppercase tracking-widest">{{ strtoupper($project->status) }}</span>
            </div>
        </div>

        <div class="flex items-center gap-1.5 md:gap-3 flex-shrink-0 flex-1 justify-end">
            <!-- Mobile Header Nav -->
            <div class="flex md:hidden items-center bg-gray-700 rounded-lg p-0.5 shadow-inner mr-1">
                <button onclick="jumpToPrev()" class="p-1.5 text-white hover:bg-gray-600 rounded-md transition">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <div class="h-4 w-[1px] bg-white/10 mx-1"></div>
                <button onclick="jumpToNext()" class="p-1.5 text-white hover:bg-gray-600 rounded-md transition">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>

            <button onclick="zoomOut()" class="p-1.5 md:p-2 text-white bg-gray-700 rounded-lg hover:bg-gray-600 transition shadow-lg">
                <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"/></svg>
            </button>
            <span id="zoom-percent" class="text-white text-[10px] font-black w-8 md:w-12 text-center uppercase tracking-tighter">100%</span>
            <button onclick="zoomIn()" class="p-1.5 md:p-2 text-white bg-gray-700 rounded-lg hover:bg-gray-600 transition shadow-lg">
                <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
            </button>
        </div>
    </div>

    <!-- Reader Area -->
    <div class="flex-1 relative overflow-y-auto bg-gray-950 p-4 pb-32 md:p-10 flex justify-center" id="viewer-scroll-container">
        <div id="canvas-container" class="max-w-full inline-block relative">
            <div id="pages-wrapper" class="flex flex-col gap-10">
                <!-- Pages injected here -->
            </div>
        </div>

        <!-- Loading Overlay -->
        <div id="viewer-loading" class="absolute inset-0 flex flex-col items-center justify-center bg-gray-900 z-50">
            <div class="w-12 h-12 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin mb-6 shadow-[0_0_20px_rgba(99,102,241,0.3)]"></div>
            <p class="text-[10px] font-black text-white uppercase tracking-[0.4em] animate-pulse text-center px-8 leading-loose">Allocating Resources<br>Rendering HD Pages</p>
        </div>
    </div>

    <!-- Bottom Navigation (Mobile Only) -->
    <div id="viewer-nav-bar" class="md:hidden fixed bottom-10 left-[5%] w-[90%] bg-black border border-white/20 px-6 py-4 rounded-full shadow-[0_40px_80px_-15px_rgba(0,0,0,1)] flex items-center justify-between z-[9999] whitespace-nowrap">
        <div class="flex items-center gap-4 w-full justify-between">
            <button onclick="jumpToPrev()" class="w-12 h-12 flex items-center justify-center bg-gray-800 text-white rounded-full active:scale-90 shadow-xl border border-white/10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
            </button>
            
            <div class="flex flex-col items-center px-4">
                <span class="text-[9px] font-black text-gray-500 uppercase tracking-[0.2em] mb-1">Page</span>
                <span class="text-xl font-black text-white tracking-tighter leading-none"><span id="current-page-mob">1</span> <span class="text-gray-700 mx-1">/</span> <span id="total-pages-mob">-</span></span>
            </div>

            <button onclick="jumpToNext()" class="w-12 h-12 flex items-center justify-center bg-gray-800 text-white rounded-full active:scale-90 shadow-xl border border-white/10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
    </div>
</div>

<style>
    /* THE "NUCLEAR" RESET: Purge all site UI except viewer */
    body > *:not(div:has(#viewer-nav-bar)):not(#viewer-nav-bar):not(main):not(#app-content) {
        display: none !important;
        opacity: 0 !important;
        pointer-events: none !important;
    }

    nav, header, footer, .sidebar, [role="navigation"], 
    [class*="fab"], [id*="fab"], [class*="help"], [id*="help"], 
    [class*="support"], [id*="support"], .z-50, .fixed:not(.inset-0):not(.bg-gray-800\/95):not(#viewer-nav-bar) { 
        display: none !important; 
    }

    /* Desktop view specifically hides bottom bar */
    @media (min-width: 768px) {
        #viewer-nav-bar { display: none !important; }
    }
    
    #app-content, main { padding: 0 !important; margin: 0 !important; width: 100vw !important; max-width: 100vw !important; background: #030712 !important; }
    
    body { background: #030712 !important; overflow: hidden !important; margin: 0; padding: 0; position: fixed; width: 100%; height: 100%; }
    
    canvas {
        max-width: 100%;
        height: auto !important;
        box-shadow: 0 50px 100px -20px rgba(0, 0, 0, 1);
        border: 1px solid rgba(255,255,255,0.05);
        background: white;
    }
</style>

<script src="{{ asset('assets/vendor/pdfjs/pdf.min.js') }}"></script>
<script>
    const pdfjsLib = window['pdfjs-dist/build/pdf'];
    pdfjsLib.GlobalWorkerOptions.workerSrc = "{{ asset('assets/vendor/pdfjs/pdf.worker.min.js') }}";

    let pdfDoc = null;
    let pageNum = 1;
    let scale = 1.5;
    let loadingPages = new Set();

    @php $manuscript = $project->files->firstWhere('type', 'manuscript'); @endphp
    let url = "{{ route('files.view', ['file' => $manuscript->id]) }}";
    
    // Protocol-Aware Fix: Force the PDF URL to match the current page's protocol
    if (window.location.protocol === 'https:' && url.startsWith('http:')) {
        url = url.replace('http:', 'https:');
    }

    async function initViewer() {
        try {
            // CRITICAL: withCredentials ensures the session cookie is sent!
            pdfjsLib.GlobalWorkerOptions.workerSrc = "{{ asset('assets/vendor/pdfjs/pdf.worker.min.js') }}";

            const loadingTask = pdfjsLib.getDocument({
                url: url,
                withCredentials: true
            });
            
            pdfDoc = await loadingTask.promise;
            
            // Update all total page displays
            const total = pdfDoc.numPages;
            if(document.getElementById('total-pages')) document.getElementById('total-pages').textContent = total;
            if(document.getElementById('total-pages-mob')) document.getElementById('total-pages-mob').textContent = total;

            document.getElementById('viewer-loading').style.opacity = '0';
            setTimeout(() => document.getElementById('viewer-loading').remove(), 500);

            await renderPage(1);
            setupIntersectionObserver();
        } catch (error) {
            console.error('Error loading PDF:', error);
            document.getElementById('viewer-loading').innerHTML = `
                <div class="text-center p-8">
                    <span class="text-4xl mb-4 block">⚠️</span>
                    <p class="text-white font-black uppercase tracking-widest text-xs mb-2">Access Denied or Connection Lost</p>
                    <p class="text-gray-500 text-[10px] uppercase">Please ensure you are logged in and refresh.</p>
                    <button onclick="location.reload()" class="mt-6 px-6 py-2 bg-indigo-600 text-white rounded-full text-[10px] font-black uppercase">Reload Engine</button>
                </div>
            `;
        }
    }

    async function renderPage(num) {
        // If already rendered, just return
        if (document.getElementById(`page-wrapper-${num}`)) return true;
        
        // If currently loading, wait for it
        if (loadingPages.has(num)) {
            return new Promise(resolve => {
                const check = setInterval(() => {
                    if (!loadingPages.has(num)) {
                        clearInterval(check);
                        resolve(true);
                    }
                }, 100);
            });
        }

        loadingPages.add(num);

        const container = document.getElementById('pages-wrapper');
        const pageWrapper = document.createElement('div');
        pageWrapper.id = `page-wrapper-${num}`;
        pageWrapper.className = 'page-container';
        pageWrapper.dataset.page = num;
        
        const canvas = document.createElement('canvas');
        canvas.id = `page-canvas-${num}`;
        pageWrapper.appendChild(canvas);
        container.appendChild(pageWrapper);

        try {
            const page = await pdfDoc.getPage(num);
            const viewport = page.getViewport({ scale: scale });
            
            const dpr = window.devicePixelRatio || 1;
            canvas.height = viewport.height * dpr;
            canvas.width = viewport.width * dpr;
            canvas.style.width = viewport.width + 'px';
            canvas.style.height = viewport.height + 'px';

            const renderContext = {
                canvasContext: canvas.getContext('2d'),
                viewport: viewport,
                transform: [dpr, 0, 0, dpr, 0, 0]
            };

            await page.render(renderContext).promise;
            loadingPages.delete(num);
            observer.observe(pageWrapper);
            return true;
        } catch (e) {
            loadingPages.delete(num);
            return false;
        }
    }

    let observer;
    function setupIntersectionObserver() {
        observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const pageNum = parseInt(entry.target.dataset.page);
                    
                    // Update all page displays
                    if(document.getElementById('current-page')) document.getElementById('current-page').textContent = pageNum;
                    if(document.getElementById('current-page-mob')) document.getElementById('current-page-mob').textContent = pageNum;
                    
                    // Progressive pre-loading
                    if (pageNum < pdfDoc.numPages) renderPage(pageNum + 1);
                    if (pageNum + 1 < pdfDoc.numPages) renderPage(pageNum + 2);
                }
            });
        }, { threshold: 0.4 });

        observer.observe(document.getElementById('page-wrapper-1'));
    }

    async function jumpToNext() {
        // Get current page from either display
        const display = document.getElementById('current-page') || document.getElementById('current-page-mob');
        const current = parseInt(display.textContent);
        if (current < pdfDoc.numPages) {
            const next = current + 1;
            const success = await renderPage(next);
            if (success) {
                const el = document.getElementById(`page-wrapper-${next}`);
                el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    }

    function jumpToPrev() {
        const display = document.getElementById('current-page') || document.getElementById('current-page-mob');
        const current = parseInt(display.textContent);
        if (current > 1) {
            const prev = current - 1;
            const el = document.getElementById(`page-wrapper-${prev}`);
            if (el) {
                el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    }

    function zoomIn() {
        scale += 0.25;
        refreshAll();
    }

    function zoomOut() {
        if (scale > 0.5) {
            scale -= 0.25;
            refreshAll();
        }
    }

    async function refreshAll() {
        document.getElementById('zoom-percent').textContent = Math.round(scale / 1.5 * 100) + '%';
        const display = document.getElementById('current-page') || document.getElementById('current-page-mob');
        const current = parseInt(display.textContent);
        document.getElementById('pages-wrapper').innerHTML = '';
        loadingPages.clear();
        await renderPage(current);
        const el = document.getElementById(`page-wrapper-${current}`);
        if(el) observer.observe(el);
    }

    initViewer();
</script>
@endsection
