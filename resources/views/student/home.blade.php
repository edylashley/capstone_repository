<x-app-layout>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Integrated Header --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-2">
                <div>
                    <h2 class="font-black text-4xl text-white uppercase tracking-tighter leading-none">Student Library
                    </h2>
                    <p class="text-[10px] text-indigo-400 uppercase tracking-[0.4em] font-black mt-3 opacity-80">
                        Institutional Repository & Knowledge Base</p>
                </div>
            </div>

            <!-- System Guide -->
            <div class="bg-indigo-900/40 overflow-hidden shadow-sm sm:rounded-2xl border border-indigo-500/30">
                <div class="p-6 md:p-10">
                    <div class="mb-10">
                        <h3 class="text-3xl font-black text-white leading-tight mb-4">Welcome to your Capstone
                            Repository! 👋</h3>
                        <p class="text-indigo-100 text-lg leading-relaxed">
                            You are part of the institutional memory. Whether you're here to research or to upload your
                            <strong>work</strong>, use this guide to navigate the system effectively.
                        </p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div
                            class="bg-white/5 p-4 rounded-xl border border-white/10 hover:bg-white/10 transition-colors">
                            <div class="flex items-center gap-3 mb-2">
                                <svg class="w-5 h-5 text-indigo-300" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <span class="text-sm font-black text-white uppercase tracking-wider">Discovery</span>
                            </div>
                            <p class="text-indigo-100 text-xs leading-relaxed">Explore the institutional library to
                                research previous works and manuscripts for your own inspiration.</p>
                        </div>
                        <div
                            class="bg-white/5 p-4 rounded-xl border border-white/10 hover:bg-white/10 transition-colors">
                            <div class="flex items-center gap-3 mb-2">
                                <svg class="w-5 h-5 text-emerald-300" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <span class="text-sm font-black text-white uppercase tracking-wider">Submission</span>
                            </div>
                            <p class="text-indigo-100 text-xs leading-relaxed">Upload your final defended manuscript in
                                PDF format and all required <strong>attachment files</strong> via the secure submission
                                portal.</p>
                        </div>
                        <div
                            class="bg-white/5 p-4 rounded-xl border border-white/10 hover:bg-white/10 transition-colors">
                            <div class="flex items-center gap-3 mb-2">
                                <svg class="w-5 h-5 text-amber-300" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-sm font-black text-white uppercase tracking-wider">Verification</span>
                            </div>
                            <p class="text-indigo-100 text-xs leading-relaxed">Track your progress as the Administrator
                                reviews and confirms your submission for permanent institutional archiving.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- My Pending/Active Projects -->
            <div class="bg-slate-900 overflow-hidden shadow-sm sm:rounded-xl border border-white/5">
                <div class="p-4 md:p-8">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                        <div>
                            <h3 class="text-xl font-black text-white">My Submissions</h3>
                            <p class="text-[10px] text-slate-500 uppercase tracking-widest font-bold mt-1">Track the
                                status of your research manuscript</p>
                        </div>
                        @php
                            $deadlineStr = \App\Models\Setting::get('submission_deadline');
                            $isPastDeadline = $deadlineStr && \Carbon\Carbon::now()->greaterThan(\Carbon\Carbon::parse($deadlineStr));
                            $submissionsOpen = \App\Models\Setting::get('submissions_open', '1') == '1';
                            $hasSubmitted = auth()->user()->authoredProjects()->exists();
                        @endphp

                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                            @if($submissionsOpen && !$isPastDeadline)
                                @if($deadlineStr)
                                    <span
                                        class="text-[10px] font-black text-red-400 uppercase tracking-widest bg-red-950/50 px-3 py-2 rounded-xl border border-red-500/20 animate-pulse text-center">
                                        Terminating: {{ \Carbon\Carbon::parse($deadlineStr)->format('M d, Y h:i A') }}
                                    </span>
                                @endif
                                <a href="{{ route('projects.create') }}"
                                    class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-lg hover:shadow-indigo-500/30">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    Submit New Project
                                </a>
                            @else
                                <div
                                    class="bg-red-500/10 border border-red-500/20 rounded-xl px-4 py-2 flex items-center justify-center">
                                    <span class="text-[10px] font-black text-red-400 uppercase tracking-widest">
                                        @if(!$submissionsOpen)
                                            ⚠️ Closed
                                        @else
                                            ⚠️ Deadline Passed
                                        @endif
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($myProjects->isEmpty())
                        <div class="text-center py-12">
                            <div
                                class="w-16 h-16 bg-gray-50 dark:bg-gray-700/30 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                                📭</div>
                            <p class="text-gray-500 italic text-sm">You have no pending submissions.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto -mx-4 sm:mx-0">
                            <div class="inline-block min-w-full align-middle">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead>
                                        <tr
                                            class="text-left text-[10px] font-black uppercase tracking-widest text-gray-400">
                                            <th class="px-6 py-4">Project Title</th>
                                            <th class="px-6 py-4">Adviser</th>
                                            <th class="px-6 py-4 text-center">Status</th>
                                            <th class="px-6 py-4 text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-white/5">
                                        @foreach($myProjects as $project)
                                            <tr class="group hover:bg-white/[0.02] transition-colors">
                                                <td class="px-6 py-4">
                                                    <div class="max-w-xs md:max-w-md">
                                                        <div
                                                            class="text-sm font-bold text-white mb-1 leading-snug truncate md:whitespace-normal">
                                                            {{ $project->title }}
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            <span
                                                                class="text-[9px] font-black uppercase tracking-tighter text-indigo-400 px-1.5 py-0.5 bg-indigo-50 dark:bg-indigo-900/30 rounded border border-indigo-100 dark:border-indigo-800">{{ $project->program }}</span>
                                                            <span
                                                                class="text-[9px] font-bold text-gray-400 uppercase italic">{{ $project->year }}</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td
                                                    class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 italic whitespace-nowrap">
                                                    {{ $project->adviser_name ?? 'N/A' }}
                                                </td>
                                                <td class="px-6 py-4 text-center">
                                                    @php
                                                        $statusClasses = match ($project->status) {
                                                            'published' => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
                                                            'approved' => 'bg-indigo-500/20 text-indigo-400 border-indigo-500/30',
                                                            'rejected' => 'bg-red-500/20 text-red-400 border-red-500/30',
                                                            default => 'bg-amber-500/20 text-amber-400 border-amber-500/30',
                                                        };
                                                    @endphp
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest border {{ $statusClasses }} shadow-sm">
                                                        @if($project->status === 'pending')
                                                            Review Pending
                                                        @elseif($project->status === 'approved')
                                                            Published
                                                        @else
                                                            {{ $project->status }}
                                                        @endif
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-center">
                                                    <div class="inline-flex flex-col items-start gap-2 text-left">
                                                        <a href="{{ route('projects.show', $project) }}"
                                                            class="group flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-slate-300 hover:text-indigo-400 transition-all">
                                                            <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-indigo-500 transition-colors"
                                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2.5"
                                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                            </svg>
                                                            View Details
                                                        </a>
                                                        @if($project->status === 'pending' || $project->status === 'rejected')
                                                            <a href="{{ route('projects.edit', $project) }}"
                                                                class="group flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-slate-300 hover:text-emerald-400 transition-all">
                                                                <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-emerald-500 transition-colors"
                                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2.5"
                                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                                </svg>
                                                                {{ $project->status === 'rejected' ? 'Resubmit' : 'Edit' }}
                                                            </a>
                                                            <form action="{{ route('projects.cancel', $project) }}" method="POST"
                                                                onsubmit="return confirm('Are you sure you want to cancel this submission? This will delete the project and all uploaded files.');"
                                                                class="inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="group flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-slate-300 hover:text-red-400 transition-all bg-transparent border-0 p-0 cursor-pointer text-left">
                                                                    <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-red-500 transition-colors"
                                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2.5"
                                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                    </svg>
                                                                    {{ $project->status === 'rejected' ? 'Delete' : 'Cancel' }}
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            @if($project->status === 'rejected' && $project->rejection_reason)
                                                <tr class="bg-red-500/5">
                                                    <td colspan="4" class="px-6 py-4">
                                                        <div class="flex items-start gap-3">
                                                            <div class="flex-shrink-0 w-1.5 h-1.5 rounded-full bg-red-400 mt-1.5">
                                                            </div>
                                                            <div>
                                                                <p
                                                                    class="text-[9px] font-black uppercase tracking-widest text-red-500 mb-1">
                                                                    Feedback</p>
                                                                <p
                                                                    class="text-xs text-gray-600 dark:text-red-200/80 leading-relaxed">
                                                                    {{ $project->rejection_reason }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Research CTA -->
            <div
                class="bg-slate-900 overflow-hidden shadow-sm sm:rounded-lg border-2 border-dashed border-indigo-500/30">
                <div class="p-8 text-center">
                    <div
                        class="inline-flex items-center justify-center w-20 h-20 bg-indigo-500/10 text-indigo-400 rounded-2xl mb-6 border border-indigo-500/20 shadow-xl">
                        <span class="text-3xl">🔍</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-200 mb-2">Ready to start your research?</h3>
                    <p class="text-gray-500 max-w-sm mx-auto mb-6">Explore thousands of verified capstone projects,
                        manuscripts, and categories in our dedicated institutional library.</p>
                    <a href="{{ route('projects.index') }}"
                        class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-bold rounded-lg shadow-md hover:bg-indigo-700 transition transform hover:-translate-y-0.5">
                        Go to Browse Repository &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>