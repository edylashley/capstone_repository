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
                                    0% { transform: translateX(0%); }
                                    100% { transform: translateX(-50%); }
                                }
                                .animate-sidebar-marquee {
                                    display: inline-block;
                                    animation: sidebar-marquee 12s linear infinite;
                                }
                            </style>
                            <div>
                                <label class="block text-xs font-bold mb-1 text-gray-300">Search Records</label>
                                <div class="relative w-full bg-white rounded-lg overflow-hidden flex items-center border border-gray-300 focus-within:ring-1 focus-within:ring-indigo-500 focus-within:border-indigo-500">
                                    <div id="sidebar-marquee-placeholder" class="absolute inset-y-0 flex items-center overflow-hidden pointer-events-none transition-opacity duration-200" style="left: 0.50rem; right: 0.50rem; mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent); -webkit-mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);">
                                        <div class="whitespace-nowrap animate-sidebar-marquee text-sm text-gray-400">
                                            Title, abstract, author, adviser, keywords... &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Title, abstract, author, adviser, keywords...
                                        </div>
                                    </div>
                                    <input type="text" id="sidebar-search" name="keyword" value="{{ request('keyword') }}" class="block w-full py-2 px-3 text-sm text-gray-900 bg-transparent border-none outline-none focus:ring-0 relative z-20" placeholder="">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold mb-1 text-gray-300">Year</label>
                                <select name="year" class="w-full rounded-lg border-gray-300 text-sm">
                                    <option value="">All Years</option>
                                    @foreach($years as $y)
                                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold mb-1 text-gray-300">Program</label>
                                <select name="program" class="w-full rounded-lg border-gray-300 text-sm">
                                    <option value="">All Programs</option>
                                    <option value="BSIT" {{ request('program') == 'BSIT' || request('program') == 'BSInT' ? 'selected' : '' }}>BSIT</option>
                                    <option value="BSCS" {{ request('program') == 'BSCS' || request('program') == 'Com-Sci' ? 'selected' : '' }}>BSCS</option>
                                </select>
                            </div>

                            <div x-data="{ open: {{ request('specialization') ? 'true' : 'false' }} }" class="space-y-3 pt-2">
                                <div @click="open = !open" class="flex items-center justify-between cursor-pointer group border-b border-gray-700/50 pb-2">
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 group-hover:text-indigo-400 transition-colors cursor-pointer italic">
                                        Filter by Category
                                    </label>
                                    <svg class="w-3.5 h-3.5 text-gray-500 group-hover:text-indigo-400 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                                
                                <div x-show="open" 
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 -translate-y-2"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     class="space-y-1 text-gray-400 pl-3 border-l-2 border-indigo-500/30 ml-1 py-1"
                                     x-cloak>
                                    @php
                                        $specializations = \App\Models\Category::pluck('name');
                                    @endphp
                                    @foreach($specializations as $spec)
                                        <label class="flex items-center gap-2 text-sm cursor-pointer hover:text-indigo-400 transition-colors py-0.5">
                                            <input type="radio" name="specialization" value="{{ $spec }}" {{ request('specialization') == $spec ? 'checked' : '' }} onchange="this.form.submit()" class="w-3 h-3 text-indigo-600 focus:ring-indigo-500 bg-gray-700 border-gray-600">
                                            <span class="{{ request('specialization') == $spec ? 'text-indigo-400 font-bold' : '' }}">{{ $spec }}</span>
                                        </label>
                                    @endforeach
                                    
                                    @if(request('specialization'))
                                        <a href="{{ route('projects.index') }}" class="inline-flex items-center gap-1 text-[9px] text-red-400 mt-2 uppercase font-black hover:text-red-500 transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            Clear Category
                                        </a>
                                    @endif
                                </div>
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
                <div class="flex-1">
                    @if($projects->isEmpty())
                        <div class="bg-white dark:bg-gray-800 p-12 text-center rounded-xl shadow-sm border border-gray-100">
                            <div class="text-4xl mb-4">📭</div>
                            <h3 class="text-lg font-bold text-gray-400">No records found</h3>
                            <p class="text-gray-400">Try adjusting your search filters or browse by category.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($projects as $project)
                                <article class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition group">
                                    <div class="flex flex-col md:flex-row justify-between gap-4">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-2">
                                                <span class="bg-indigo-100 text-indigo-700 text-[10px] font-bold px-2 py-0.5 rounded uppercase">
                                                    {{ $project->specialization ?? 'General' }}
                                                </span>
                                                <span class="text-[10px] text-gray-400 font-bold uppercase">{{ $project->year }}</span>
                                                <span class="text-[10px] text-indigo-400/80 font-black uppercase tracking-widest border-l border-gray-700 pl-2 ml-1">{{ $project->program }}</span>
                                                @if(auth()->check() && auth()->user()->isAdmin())
                                                    @if($project->status === 'published')
                                                        <span class="bg-emerald-500/10 text-emerald-600 text-[9px] font-black px-2 py-0.5 rounded-full border border-emerald-500/20 uppercase tracking-tighter">Published Record</span>
                                                    @elseif($project->status === 'approved')
                                                        <span class="bg-indigo-500/10 text-indigo-500 text-[9px] font-black px-2 py-0.5 rounded-full border border-indigo-500/20 uppercase tracking-tighter">Approved Final</span>
                                                    @else
                                                        <span class="bg-green-500/10 text-green-500 text-[9px] font-black px-2 py-0.5 rounded-full border border-green-500/20 uppercase tracking-tighter">Pending Review</span>
                                                    @endif
                                                @endif
                                            </div>
                                            <h4 class="text-xl font-bold text-gray-800 dark:text-white group-hover:text-indigo-600 transition mb-2">
                                                <a href="{{ route('projects.show', $project) }}">{{ $project->title }}</a>
                                            </h4>
                                            <p class="text-sm text-gray-500 line-clamp-2 leading-relaxed mb-4">
                                                {{ $project->abstract }}
                                            </p>
                                            <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-[10px] uppercase font-black tracking-widest leading-none">
                                                <div class="flex items-center gap-1.5 overflow-hidden">
                                                    <span class="text-indigo-400/80 flex-shrink-0">Authors:</span>
                                                    <span class="text-gray-300 truncate">{{ $project->authors_list ?: $project->authors->pluck('name')->join(', ') }}</span>
                                                </div>
                                                <div class="flex items-center gap-1.5 overflow-hidden">
                                                    <span class="text-indigo-400/80 flex-shrink-0">Adviser:</span>
                                                    <span class="text-gray-300 truncate">{{ $project->adviser->name ?? $project->adviser_name ?? 'N/A' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="md:w-32 flex md:flex-col justify-center items-center gap-2 border-l md:pl-4 border-gray-100 font-black">
                                            <a href="{{ route('projects.show', $project) }}" class="text-indigo-600 hover:underline text-xs">View Full Details</a>
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