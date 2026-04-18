<x-app-layout>
    <x-slot name="header">
        <div class="flex items-start sm:items-center justify-between">
            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                @guest
                <a href="{{ route('home') }}" class="inline-flex w-max px-4 py-2 bg-gray-700/50 hover:bg-gray-600 border border-gray-500 rounded-md font-semibold text-xs text-white uppercase tracking-widest transition ease-in-out duration-150 items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to Home
                </a>
                @endguest
                <h2 class="font-bold text-2xl text-white leading-tight">
                    {{ __('Research Library') }}
                </h2>
            </div>
            <div class="hidden md:flex items-center gap-2">
                <span class="px-3 py-1 bg-white/20 text-white text-[10px] font-black uppercase rounded-full border border-white/30 italic">Program Restricted</span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-8 items-start">
                <!-- Sidebar: Refinement Tools -->
                <aside class="w-full lg:w-1/4 shrink-0 space-y-6 lg:sticky lg:top-8 lg:self-start lg:z-10">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100">
                        <h3 class="font-black text-xs uppercase tracking-widest text-gray-400 mb-4 italic">Library Catalog</h3>
                        
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
                                <label class="block text-xs font-bold mb-1 text-gray-500 dark:text-gray-300">Search Records</label>
                                <div class="relative w-full bg-white rounded-lg overflow-hidden flex items-center border border-gray-300 focus-within:ring-1 focus-within:ring-indigo-500 focus-within:border-indigo-500">
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
                                    <input type="text" id="sidebar-search" name="keyword" value="{{ request('keyword') }}" class="block w-full py-2 px-3 text-sm text-gray-900 bg-transparent border-none outline-none focus:ring-0 relative z-20" placeholder="">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold mb-1 text-gray-500 dark:text-gray-300">Year</label>
                                <select name="year" class="w-full rounded-lg border-gray-300 text-sm">
                                    <option value="">All Years</option>
                                    @foreach($years as $y)
                                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold mb-1 text-gray-500 dark:text-gray-300">Program</label>
                                <select name="program" class="w-full rounded-lg border-gray-300 text-sm">
                                    <option value="">All Programs</option>
                                    <option value="BSInT" {{ request('program') == 'BSInT' ? 'selected' : '' }}>BSInT</option>
                                    <option value="BSCS" {{ request('program') == 'BSCS' || request('program') == 'Com-Sci' ? 'selected' : '' }}>BSCS</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold mb-1 text-gray-500 dark:text-gray-300">Category</label>
                                <select name="specialization" class="w-full rounded-lg border-gray-300 text-sm" onchange="this.form.submit()">
                                    <option value="">Select Category</option>
                                    @foreach(\App\Models\Category::pluck('name') as $spec)
                                        <option value="{{ $spec }}" {{ request('specialization') == $spec ? 'selected' : '' }}>
                                            {{ $spec }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-2 rounded-lg hover:bg-indigo-700 transition shadow-sm">
                                Apply Filters
                            </button>
                        </form>
                    </div>

                    <div class="bg-gray-200 p-6 rounded-xl border border-gray-100 italic">
                        <p class="text-xs text-gray-700">"The true sign of intelligence is not knowledge but imagination." – Albert Einstein</p>
                    </div>
                </aside>

                <!-- Main Content: Project Gallery -->
                <div class="flex-1 w-full">
                    @if($projects->isEmpty())
                        <div class="bg-white dark:bg-gray-800 p-8 md:p-12 text-center rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                            <div class="text-4xl mb-4">📭</div>
                            <h3 class="text-lg font-bold text-gray-400">No records found</h3>
                            <p class="text-sm text-gray-400">Try adjusting your search filters or browse by category.</p>
                        </div>
                    @else
                        <div class="space-y-4 md:space-y-6">
                            @foreach($projects as $project)
                                <article class="bg-white dark:bg-gray-800 p-4 md:p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all group overflow-hidden">
                                    <div class="flex flex-col md:flex-row justify-between gap-4 md:gap-6">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex flex-wrap items-center gap-2 mb-3">
                                                @foreach($project->categories as $category)
                                                    <span class="bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-[9px] font-black px-2 py-0.5 rounded uppercase tracking-tighter border border-indigo-100 dark:border-indigo-800">
                                                        {{ $category->name }}
                                                    </span>
                                                @endforeach
                                                @if($project->categories->isEmpty())
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

                                            <h4 class="text-lg md:text-xl font-black text-gray-800 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors mb-2 leading-tight">
                                                <a href="{{ route('projects.show', $project) }}" class="block">{{ $project->title }}</a>
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

                                        <div class="shrink-0 flex md:flex-col justify-center items-center gap-3 pt-4 md:pt-0 border-t md:border-t-0 md:border-l border-gray-100 dark:border-gray-700 md:pl-6">
                                            <a href="{{ route('projects.show', $project) }}" class="w-full md:w-auto text-center px-4 py-2 md:py-1.5 bg-gray-50 dark:bg-gray-700/50 hover:bg-indigo-600 hover:text-white dark:text-gray-300 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all">
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
            
            if (sidebarSearchInput && sidebarMarqueePlaceholder) {
                const toggleSidebarMarquee = () => {
                    if (sidebarSearchInput.value.length > 0 || document.activeElement === sidebarSearchInput) {
                        sidebarMarqueePlaceholder.style.opacity = '0';
                    } else {
                        sidebarMarqueePlaceholder.style.opacity = '1';
                    }
                };

                sidebarSearchInput.addEventListener('focus', toggleSidebarMarquee);
                sidebarSearchInput.addEventListener('blur', toggleSidebarMarquee);
                sidebarSearchInput.addEventListener('input', toggleSidebarMarquee);
                
                // Set initial state
                toggleSidebarMarquee();
            }
        });
    </script>
</x-app-layout>