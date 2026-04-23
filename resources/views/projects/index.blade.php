<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            {{-- Integrated Header --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-2">
                <div class="flex flex-col gap-4">
                    @guest
                    <a href="{{ route('home') }}" class="inline-flex w-max px-4 py-2 bg-slate-900/50 hover:bg-slate-800 border border-white/5 rounded-xl font-black text-[10px] text-slate-400 uppercase tracking-widest transition shadow-inner items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Back to Home
                    </a>
                    @endguest
                    <div>
                        <h2 class="font-black text-4xl text-white uppercase tracking-tighter leading-none">Research Library</h2>
                        <p class="text-[10px] text-indigo-400 uppercase tracking-[0.4em] font-black mt-3 opacity-80">Institutional Knowledge & Project Archive</p>
                    </div>
                </div>
                <div class="hidden md:flex items-center gap-3">
                    @guest
                    <a href="{{ route('login') }}" class="inline-flex w-max px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white border border-indigo-500/50 rounded-xl font-black text-[10px] uppercase tracking-widest transition shadow-lg shadow-indigo-900/20 items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                        Sign In
                    </a>
                    @endguest
                    <div class="bg-slate-900/50 px-4 py-2 rounded-2xl border border-white/5 shadow-inner">
                        <span class="text-[10px] font-black text-indigo-400 uppercase tracking-widest italic">Institutional Access Only</span>
                    </div>
                </div>
            </div>
            
            <div class="flex flex-col lg:flex-row gap-8 items-start">
                <!-- Sidebar: Refinement Tools -->
                <aside class="w-full lg:w-1/4 shrink-0 space-y-6 lg:sticky lg:top-8 lg:self-start lg:z-10">
                    <div class="bg-slate-900 p-6 rounded-xl shadow-sm border border-white/5">
                        <h3 class="font-black text-xs uppercase tracking-widest text-slate-500 mb-4 italic">Library Catalog</h3>
                        
                        <!-- Search Form -->
                        <form method="GET" action="{{ route('projects.index') }}" class="space-y-4">
                            <!-- Marquee CSS -->
                            <style>
                                @keyframes sidebar-marquee {
                                    0% { transform: translateX(0); }
                                    100% { transform: translateX(-50%); }
                                }
                                .animate-sidebar-marquee {
                                    display: flex;
                                    width: max-content;
                                    animation: sidebar-marquee 30s linear infinite;
                                }
                            </style>
                            <div>
                                <label class="block text-xs font-bold mb-1 text-slate-400">Search Records</label>
                                <div class="relative w-full bg-slate-800 rounded-lg overflow-hidden flex items-center border border-white/10 focus-within:ring-1 focus-within:ring-indigo-500 focus-within:border-indigo-500">
                                    <div id="sidebar-marquee-placeholder" class="absolute inset-y-0 flex items-center overflow-hidden pointer-events-none transition-opacity duration-200" style="left: 0.50rem; right: 0.50rem; mask-image: linear-gradient(to right, transparent, black 5%, black 95%, transparent); -webkit-mask-image: linear-gradient(to right, transparent, black 5%, black 95%, transparent);">
                                        <div class="animate-sidebar-marquee text-sm text-gray-400">
                                            <div class="flex shrink-0">
                                                <span class="pr-12">Title, abstract, author, adviser, keywords...</span>
                                                <span class="pr-12">Title, abstract, author, adviser, keywords...</span>
                                                <span class="pr-12">Title, abstract, author, adviser, keywords...</span>
                                            </div>
                                            <div class="flex shrink-0">
                                                <span class="pr-12">Title, abstract, author, adviser, keywords...</span>
                                                <span class="pr-12">Title, abstract, author, adviser, keywords...</span>
                                                <span class="pr-12">Title, abstract, author, adviser, keywords...</span>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="text" id="sidebar-search" name="keyword" value="{{ request('keyword') }}" class="block w-full py-2 pl-3 pr-8 text-sm text-white bg-transparent border-none outline-none focus:ring-0 relative z-20" placeholder="">
                                    <button type="button" id="sidebar-clear-btn" class="absolute text-gray-400 hover:text-gray-600 hidden z-30 focus:outline-none" style="right: 10px; top: 50%; transform: translateY(-50%);" title="Clear search">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold mb-1 text-slate-400">Year</label>
                                <select name="year" class="w-full rounded-lg border-white/10 bg-slate-800 text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">All Years</option>
                                    @foreach($years as $y)
                                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold mb-1 text-slate-400">Program</label>
                                <select name="program" class="w-full rounded-lg border-white/10 bg-slate-800 text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">All Programs</option>
                                    <option value="BSInT" {{ request('program') == 'BSInT' ? 'selected' : '' }}>BSInT</option>
                                    <option value="BSCS" {{ request('program') == 'BSCS' || request('program') == 'Com-Sci' ? 'selected' : '' }}>BSCS</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold mb-1 text-slate-400">Category</label>
                                <select id="category-select" name="specialization" class="w-full rounded-lg border-white/10 bg-slate-800 text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Select Category</option>
                                    @foreach(\App\Models\Category::where('name', '!=', 'Others')->get() as $cat)
                                        <option value="{{ $cat->name }}" {{ request('specialization') == $cat->name ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                    <option value="Others" {{ request('specialization') == 'Others' || request('category_text') ? 'selected' : '' }}>Others</option>
                                </select>
                                <div id="category-text-container" class="mt-2 {{ request('specialization') == 'Others' || request('category_text') ? '' : 'hidden' }}">
                                    <input type="text" name="category_text" value="{{ request('category_text') }}" 
                                           class="w-full rounded-lg border-white/10 bg-slate-800 text-white text-[10px] uppercase font-black tracking-widest focus:ring-indigo-500 focus:border-indigo-500 placeholder:text-slate-600 px-3 py-2" 
                                           placeholder="Type custom category...">
                                </div>

                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        const select = document.getElementById('category-select');
                                        const container = document.getElementById('category-text-container');
                                        
                                        select.addEventListener('change', function() {
                                            if (this.value === 'Others') {
                                                container.classList.remove('hidden');
                                                container.querySelector('input').focus();
                                            } else {
                                                container.classList.add('hidden');
                                            }
                                        });
                                    });
                                </script>
                            </div>

                            <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-2 rounded-lg hover:bg-indigo-700 transition shadow-sm">
                                Apply Filters
                            </button>
                        </form>
                    </div>

                    <div class="bg-slate-900/50 p-6 rounded-xl border border-white/5 italic">
                        <p class="text-xs text-slate-400">"The true sign of intelligence is not knowledge but imagination." – Albert Einstein</p>
                    </div>
                </aside>

                <!-- Main Content: Project Gallery -->
                <div class="flex-1 w-full min-w-0">
                    @if($projects->isEmpty())
                        <div class="bg-slate-900 p-16 md:p-24 text-center rounded-3xl shadow-2xl border border-white/5 relative overflow-hidden group">
                            <div class="absolute inset-0 bg-indigo-500/[0.02] group-hover:bg-indigo-500/[0.04] transition-colors"></div>
                            <div class="relative z-10 flex flex-col items-center justify-center">
                                <div class="relative mb-8">
                                    <div class="absolute inset-0 bg-indigo-500/10 blur-3xl rounded-full"></div>
                                    <svg class="w-24 h-24 text-slate-700 relative z-10 opacity-30 group-hover:opacity-50 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-black text-white uppercase tracking-[0.2em] mb-3">No Records Found</h3>
                                <p class="text-slate-500 text-xs font-bold uppercase tracking-widest max-w-sm mx-auto leading-relaxed">
                                    Your search did not return any matches. Try adjusting your filters or browsing by category.
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="space-y-4 md:space-y-6">
                            @foreach($projects as $project)
                                <article class="bg-slate-900 p-4 md:p-6 rounded-xl shadow-sm border border-white/5 hover:shadow-md transition-all group overflow-hidden">
                                    <div class="flex flex-col md:flex-row justify-between gap-4 md:gap-6">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex flex-wrap items-center gap-2 mb-3">
                                                @foreach($project->categories as $category)
                                                    <span class="bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-[9px] font-black px-2 py-0.5 rounded uppercase tracking-tighter border border-indigo-100 dark:border-indigo-800">
                                                        {{ $category->name }}
                                                    </span>
                                                @endforeach
                                                @if($project->custom_category)
                                                    <span class="bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-[9px] font-black px-2 py-0.5 rounded uppercase tracking-tighter border border-indigo-100 dark:border-indigo-800">
                                                        {{ $project->custom_category }}
                                                    </span>
                                                @endif
                                                @if($project->categories->isEmpty() && !$project->custom_category)
                                                    <span class="bg-gray-50 dark:bg-gray-700 text-gray-400 text-[9px] font-black px-2 py-0.5 rounded uppercase tracking-tighter border border-gray-100 dark:border-gray-600">
                                                        General
                                                    </span>
                                                @endif
                                                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">{{ $project->year }}</span>
                                                <span class="text-[10px] text-indigo-400/80 font-black uppercase tracking-widest border-l border-gray-200 dark:border-gray-700 pl-2">{{ $project->program }}</span>
                                                
                                                @if(auth()->check() && auth()->user()->isAdmin())
                                                    <span class="bg-emerald-500/10 text-emerald-600 text-[9px] font-black px-2 py-0.5 rounded-full border border-emerald-500/20 uppercase tracking-tighter ml-auto sm:ml-0">
                                                        {{ ucfirst($project->status) }}
                                                    </span>
                                                @endif
                                            </div>

                                            <h4 class="text-lg md:text-xl font-black text-gray-800 dark:text-white mb-2 leading-tight">
                                                {{ $project->title }}
                                            </h4>

                                            <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 md:line-clamp-3 leading-relaxed mb-4">
                                                {{ $project->abstract }}
                                            </p>

                                            <div class="flex flex-col sm:flex-row sm:items-center gap-y-2 gap-x-6 text-[10px] uppercase font-black tracking-widest leading-none">
                                                <div class="flex items-center gap-2 overflow-hidden">
                                                    <span class="text-indigo-400/80 shrink-0">Authors:</span>
                                                    <span class="text-gray-400 truncate">{{ $project->authors_list ?: $project->authors->pluck('name')->join(', ') }}</span>
                                                </div>
                                                <div class="flex items-center gap-2 overflow-hidden">
                                                    <span class="text-indigo-400/80 shrink-0">Adviser:</span>
                                                    <span class="text-gray-400 truncate">{{ $project->adviser->name ?? $project->adviser_name ?? 'N/A' }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="shrink-0 flex md:flex-col justify-center items-center gap-3 pt-4 md:pt-0 border-t md:border-t-0 md:border-l border-white/5 md:pl-6">
                                            <a href="{{ route('projects.show', $project) }}" class="w-full md:w-auto text-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg hover:shadow-indigo-500/30 transition-all">
                                                View Details
                                            </a>
                                        </div>

                                    </div>
                                </article>
                            @endforeach
                        </div>

                        <div class="mt-8">
                            {{ $projects->links() }}
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Smooth CSS Marquee Placeholder behavior for Sidebar Config
            const sidebarSearchInput = document.getElementById('sidebar-search');
            const sidebarMarqueePlaceholder = document.getElementById('sidebar-marquee-placeholder');
            const sidebarClearBtn = document.getElementById('sidebar-clear-btn');
            
            if (sidebarSearchInput && sidebarMarqueePlaceholder) {
                const toggleSidebarMarquee = () => {
                    if (sidebarSearchInput.value.length > 0 || document.activeElement === sidebarSearchInput) {
                        sidebarMarqueePlaceholder.style.opacity = '0';
                    } else {
                        sidebarMarqueePlaceholder.style.opacity = '1';
                    }
                    if (sidebarClearBtn) {
                        if (sidebarSearchInput.value.length > 0) {
                            sidebarClearBtn.classList.remove('hidden');
                        } else {
                            sidebarClearBtn.classList.add('hidden');
                        }
                    }
                };

                sidebarSearchInput.addEventListener('focus', toggleSidebarMarquee);
                sidebarSearchInput.addEventListener('blur', toggleSidebarMarquee);
                sidebarSearchInput.addEventListener('input', toggleSidebarMarquee);
                
                if (sidebarClearBtn) {
                    sidebarClearBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        sidebarSearchInput.value = '';
                        toggleSidebarMarquee();
                        sidebarSearchInput.focus();
                    });
                }
                
                // Set initial state
                toggleSidebarMarquee();
            }
        });
    </script>
</x-app-layout>