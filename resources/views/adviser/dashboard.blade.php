<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-white leading-tight">
                    {{ __('Faculty Dashboard') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1 uppercase tracking-widest font-semibold">Institutional Mentorship Overview</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs font-bold text-gray-400 uppercase">System Status:</span>
                <span class="flex items-center gap-1.5 py-1 px-3 rounded-full text-[10px] font-black bg-green-100 text-green-700 uppercase tracking-tighter">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-600 animate-pulse"></span>
                    Operational
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Welcome Hero -->
            <div class="dark:bg-gray-800 rounded-3xl p-8 text-white shadow-2xl shadow-indigo-200 relative overflow-hidden">
                <div class="relative z-10 max-w-2xl">
                    <h1 class="text-4xl font-black mb-4 leading-tight">Good day, Adviser {{ auth()->user()->name }}! 👋</h1>
                    <p class="text-indigo-100 text-lg mb-8 leading-relaxed">
                        Welcome back to the {{ \App\Models\Setting::get('repository_name', 'CSIT Capstone Repository') }}. You are currently mentoring <span class="font-bold text-white border-b-2 border-white/30">{{ $stats['total_advising'] }}</span> student groups. There are <span class="font-bold text-white border-b-2 border-white/30">{{ $stats['pending_confirmation'] }}</span> projects awaiting your expertise in the verification queue.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('faculty.review') }}" class="inline-flex items-center px-6 py-3 bg-white text-indigo-700 font-black rounded-xl shadow-xl hover:bg-indigo-50 transition transform hover:-translate-y-1 group">
                            Open Confirmation Queue
                            <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                        </a>
                        <a href="{{ route('projects.index') }}" class="inline-flex items-center px-6 py-3 bg-indigo-500/30 text-white font-bold rounded-xl border border-white/20 hover:bg-indigo-500/50 transition">
                            Browse All Records
                        </a>
                    </div>
                </div>
                <!-- Abstract Background Shapes -->
                <div class="absolute -right-20 -top-20 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
                <div class="absolute right-40 -bottom-20 w-60 h-60 bg-purple-500/20 rounded-full blur-3xl"></div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Stat Card 1 -->
                <div class="dark:bg-gray-800 bg-white p-6 rounded-3xl shadow-sm border border-gray-100 group hover:shadow-xl hover:shadow-indigo-50 transition-all duration-300">
                    <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <div class="text-3xl font-black text-gray-300">{{ $stats['total_advising'] }}</div>
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Total Supervised Groups</div>
                </div>

                <!-- Stat Card 2 -->
                <div class="dark:bg-gray-800 bg-white p-6 rounded-3xl shadow-sm border border-gray-100 group hover:shadow-xl hover:shadow-amber-50 transition-all duration-300">
                    <div class="w-12 h-12 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-600 mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="text-3xl font-black text-gray-300">{{ $stats['pending_confirmation'] }}</div>
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Pending Confirmation</div>
                </div>

                <!-- Stat Card 3 -->
                <div class="dark:bg-gray-800 bg-white p-6 rounded-3xl shadow-sm border border-gray-100 group hover:shadow-xl hover:shadow-green-50 transition-all duration-300">
                    <div class="w-12 h-12 bg-green-50 rounded-2xl flex items-center justify-center text-green-600 mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="text-3xl font-black text-gray-300">{{ $stats['confirmed_projects'] }}</div>
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Finalized Archives</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Recent Submissions -->
                <div class="dark:bg-gray-800 bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-xl font-black text-gray-300 font-display italic">Recent Submissions</h3>
                        <a href="{{ route('faculty.review') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 uppercase tracking-widest border-b-2 border-indigo-100 pb-0.5">View All &rarr;</a>
                    </div>
                    
                    @if($recentProjects->isEmpty())
                        <div class="text-center py-10 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
                            <span class="text-4xl block mb-2 opacity-50 italic">📂</span>
                            <p class="text-gray-400 font-medium">No projects have been assigned to you yet.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($recentProjects as $project)
                                <div class="p-4 bg-gray-200 rounded-2xl border border-gray-100 flex items-center justify-between hover:bg-gray-700 hover:shadow-md transition cursor-pointer" onclick="window.location='{{ route('projects.show', $project) }}'">
                                    <div class="flex-1 min-w-0 pr-4">
                                        <h4 class="font-bold text-gray-900 truncate mb-1">{{ $project->title }}</h4>
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded font-black uppercase tracking-tighter">{{ $project->year }}</span>
                                            <span class="text-xs text-gray-500 font-medium truncate italic text-clamp-1">By: {{ $project->authors->pluck('name')->join(', ') }}</span>
                                        </div>
                                    </div>
                                    <div>
                                        @if($project->status === 'pending')
                                            <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-[10px] font-black uppercase tracking-widest">New</span>
                                        @elseif($project->status === 'rejected')
                                            <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-[10px] font-black uppercase tracking-widest">Returned</span>
                                        @else
                                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-[10px] font-black uppercase tracking-widest">Confirmed</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Adviser Quick Actions & Guide -->
                <div class="space-y-6">
                    <div class="dark:bg-gray-800 bg-white p-8 rounded-3xl shadow-sm border border-gray-100 h-full">
                        <h3 class="text-xl font-black text-gray-300 mb-6">Mentorship Dashboard Tips</h3>
                        <div class="space-y-6">
                            <div class="flex gap-4">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 flex-shrink-0">
                                    <span class="font-black">01</span>
                                </div>
                                <div>
                                    <h5 class="font-bold text-gray-300 leading-tight">Evaluate Submissions</h5>
                                    <p class="text-sm text-gray-500 mt-1 italic">Access the Confirmation Queue to review pending project records. Ensure the submitted manuscript represents the finalized, successfully defended capstone project.</p>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600 flex-shrink-0">
                                    <span class="font-black">02</span>
                                </div>
                                <div>
                                    <h5 class="font-bold text-gray-300 leading-tight">Verify Authorship</h5>
                                    <p class="text-sm text-gray-500 mt-1 italic">Prior to confirmation, verify that all contributing group members are accurately listed as authors in the project metadata.</p>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 flex-shrink-0">
                                    <span class="font-black">03</span>
                                </div>
                                <div>
                                    <h5 class="font-bold text-gray-300 leading-tight">Institutional Archiving</h5>
                                    <p class="text-sm text-gray-500 mt-1 italic">Upon your formal confirmation, the project record will be seamlessly forwarded to the Administrator for permanent institutional archiving.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
