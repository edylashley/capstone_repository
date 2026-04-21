<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            {{-- Integrated Header --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-2">
                <div>
                    <h2 class="font-black text-4xl text-white uppercase tracking-tighter leading-none">Faculty Dashboard</h2>
                    <p class="text-[10px] text-indigo-400 uppercase tracking-[0.4em] font-black mt-3 opacity-80">Institutional Mentorship & Project Oversight</p>
                </div>
            </div>
            
            <!-- Welcome Hero -->
            <div class="bg-slate-900 rounded-3xl p-8 text-white shadow-2xl relative overflow-hidden border border-white/5">
                <div class="relative z-10">
                    <h1 class="text-4xl font-black mb-4 leading-tight">Good day, Adviser {{ auth()->user()->name }}! 👋</h1>
                    <p class="text-indigo-100 text-lg mb-8 leading-relaxed">
                        Welcome back to the {{ \App\Models\Setting::get('repository_name', 'CSIT Capstone Repository') }}. You are currently mentoring <span class="font-bold text-white border-b-2 border-white/30">{{ $stats['total_advising'] }}</span> student groups. There are <span class="font-bold text-white border-b-2 border-white/30">{{ $stats['pending_confirmation'] }}</span> projects awaiting your expertise in the verification queue.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 mt-8">
                        <a href="{{ route('faculty.review') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 bg-white text-indigo-700 font-black rounded-2xl shadow-xl hover:shadow-indigo-500/20 transition-all transform hover:-translate-y-1 active:scale-95 group">
                            <span class="text-sm md:text-base">Open Confirmation Queue</span>
                            <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                        </a>
                        <a href="{{ route('projects.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 bg-white/10 text-white font-bold rounded-2xl border border-white/20 hover:bg-white/20 transition-all active:scale-95 text-sm md:text-base">
                            Browse All Records
                        </a>
                    </div>
                </div>
                <!-- Abstract Background Shapes -->
                <div class="absolute -right-20 -top-20 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
                <div class="absolute right-40 -bottom-20 w-60 h-60 bg-purple-500/20 rounded-full blur-3xl"></div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
                <!-- Stat Card 1 -->
                <div class="bg-slate-900 p-5 rounded-2xl shadow-sm border border-white/5 group hover:shadow-lg transition-all duration-300">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-indigo-900/20 rounded-xl flex items-center justify-center text-indigo-400 group-hover:scale-105 transition-transform shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <div>
                            <div class="text-2xl font-black text-white leading-none">{{ $stats['total_advising'] }}</div>
                            <div class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-1">Supervised Groups</div>
                        </div>
                    </div>
                </div>

                <!-- Stat Card 2 -->
                <div class="bg-slate-900 p-5 rounded-2xl shadow-sm border border-white/5 group hover:shadow-lg transition-all duration-300">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-amber-900/20 rounded-xl flex items-center justify-center text-amber-500 group-hover:scale-105 transition-transform shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <div class="text-2xl font-black text-white leading-none">{{ $stats['pending_confirmation'] }}</div>
                            <div class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-1">Pending Confirmation</div>
                        </div>
                    </div>
                </div>

                <!-- Stat Card 3 -->
                <div class="bg-slate-900 p-5 rounded-2xl shadow-sm border border-white/5 group hover:shadow-lg transition-all duration-300">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-emerald-900/20 rounded-xl flex items-center justify-center text-emerald-500 group-hover:scale-105 transition-transform shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <div class="text-2xl font-black text-white leading-none">{{ $stats['confirmed_projects'] }}</div>
                            <div class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-1">Finalized Archives</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Recent Submissions -->
                <div class="bg-slate-900 p-8 rounded-3xl shadow-sm border border-white/5">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-xl font-black text-white font-display italic">Recent Submissions</h3>
                        <a href="{{ route('faculty.review') }}" class="text-xs font-bold text-indigo-400 hover:text-indigo-300 uppercase tracking-widest border-b-2 border-indigo-500/20 pb-0.5">View All &rarr;</a>
                    </div>
                    
                    @if($recentProjects->isEmpty())
                        <div class="text-center py-10 bg-slate-950 rounded-2xl border-2 border-dashed border-white/5">
                            <span class="text-4xl block mb-2 opacity-50 italic">📂</span>
                            <p class="text-slate-500 font-medium">No projects have been assigned to you yet.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($recentProjects as $project)
                                <div class="p-4 bg-slate-950 rounded-2xl border border-white/5 flex items-center justify-between hover:bg-slate-800 hover:shadow-md transition cursor-pointer group" onclick="window.location='{{ route('projects.show', $project) }}'">
                                    <div class="flex-1 min-w-0 pr-4">
                                        <h4 class="font-bold text-white group-hover:text-indigo-400 transition-colors truncate mb-1">{{ $project->title }}</h4>
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] bg-indigo-900/30 text-indigo-400 px-2 py-0.5 rounded font-black uppercase tracking-tighter border border-indigo-500/20">{{ $project->year }}</span>
                                            <span class="text-xs text-slate-500 font-medium truncate italic text-clamp-1">By: {{ $project->authors_list ?: $project->authors->pluck('name')->join(', ') }}</span>
                                        </div>
                                    </div>
                                    <div>
                                        @if($project->status === 'pending')
                                            <span class="px-3 py-1 bg-amber-900/30 text-amber-500 border border-amber-500/20 rounded-full text-[10px] font-black uppercase tracking-widest">New</span>
                                        @elseif($project->status === 'rejected')
                                            <span class="px-3 py-1 bg-red-900/30 text-red-500 border border-red-500/20 rounded-full text-[10px] font-black uppercase tracking-widest">Returned</span>
                                        @else
                                            <span class="px-3 py-1 bg-emerald-900/30 text-emerald-500 border border-emerald-500/20 rounded-full text-[10px] font-black uppercase tracking-widest">Confirmed</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Adviser Quick Actions & Guide -->
                <div class="space-y-6">
                    <div class="bg-slate-900 p-8 rounded-3xl shadow-sm border border-white/5 h-full">
                        <h3 class="text-xl font-black text-white mb-6 font-display italic">Mentorship Dashboard Tips</h3>
                        <div class="space-y-6">
                            <div class="flex gap-4">
                                <div class="w-10 h-10 rounded-xl bg-blue-900/20 flex items-center justify-center text-blue-400 flex-shrink-0 border border-blue-500/20">
                                    <span class="font-black">01</span>
                                </div>
                                <div>
                                    <h5 class="font-bold text-white leading-tight">Evaluate Submissions</h5>
                                    <p class="text-sm text-slate-500 mt-1 italic">Access the Confirmation Queue to review pending project records. Ensure the submitted manuscript represents the finalized, successfully defended capstone project.</p>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <div class="w-10 h-10 rounded-xl bg-purple-900/20 flex items-center justify-center text-purple-400 flex-shrink-0 border border-purple-500/20">
                                    <span class="font-black">02</span>
                                </div>
                                <div>
                                    <h5 class="font-bold text-white leading-tight">Verify Authorship</h5>
                                    <p class="text-sm text-slate-500 mt-1 italic">Prior to confirmation, verify that all contributing group members are accurately listed as authors in the project metadata.</p>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <div class="w-10 h-10 rounded-xl bg-indigo-900/20 flex items-center justify-center text-indigo-400 flex-shrink-0 border border-indigo-500/20">
                                    <span class="font-black">03</span>
                                </div>
                                <div>
                                    <h5 class="font-bold text-white leading-tight">Institutional Archiving</h5>
                                    <p class="text-sm text-slate-500 mt-1 italic">Upon your formal confirmation, the project record will be seamlessly forwarded to the Administrator for permanent institutional archiving.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
