<x-app-layout>
    
    <x-slot name="header">
        <h2 class="font-bold text-2xl   text-white leading-tight">
            {{ __('Student Library') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">


            <!-- Getting Started Guide -->
            <div class="bg-indigo-600 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-5">
                    <h3 class="text-2xl font-bold mb-2 text-white">Welcome to your Capstone Repository!</h3>
                    <p class="text-indigo-100 mb-4">You are part of the institutional memory. Whether you're here to research or to archive your legacy, follow these steps:</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-white/10 p-3 rounded-lg flex items-start gap-3">
                            <span class="bg-white text-indigo-600 rounded-full w-6 h-6 flex items-center justify-center font-bold flex-shrink-0">1</span>
                            <p class="text-white text-sm"><strong>Search</strong> for inspiration in the Library below using keywords or years.</p>
                        </div>
                        <div class="bg-white/10 p-3 rounded-lg flex items-start gap-3">
                            <span class="bg-white text-indigo-600 rounded-full w-6 h-6 flex items-center justify-center font-bold flex-shrink-0">2</span>
                            <p class="text-white text-sm"><strong>Upload</strong> your final defended manuscript in PDF format via the Submit button.</p>
                        </div>
                        <div class="bg-white/10 p-3 rounded-lg flex items-start gap-3">
                            <span class="bg-white text-indigo-600 rounded-full w-6 h-6 flex items-center justify-center font-bold flex-shrink-0">3</span>
                            <p class="text-white text-sm"><strong>Wait</strong> for your Faculty Adviser to confirm the digital record for the archive.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- My Pending/Active Projects -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-4">
                        <h3 class="text-lg font-bold">My Submissions</h3>
                        @php
                            $deadlineStr = \App\Models\Setting::get('submission_deadline');
                            $isPastDeadline = $deadlineStr && \Carbon\Carbon::now()->greaterThan(\Carbon\Carbon::parse($deadlineStr));
                            $submissionsOpen = \App\Models\Setting::get('submissions_open', '1') == '1';
                            $hasSubmitted = auth()->user()->authoredProjects()->exists();
                        @endphp

                        @if($submissionsOpen && !$isPastDeadline && !$hasSubmitted)
                            <div class="flex flex-col sm:flex-row items-end sm:items-center gap-3">
                                @if($deadlineStr)
                                    <span class="text-sm font-black text-red-600 uppercase tracking-widest bg-red-50 dark:bg-red-900/40 px-4 py-2 rounded-lg border-2 border-red-200 dark:border-red-800 animate-pulse">
                                        Deadline: {{ \Carbon\Carbon::parse($deadlineStr)->format('M d, Y h:i A') }}
                                    </span>
                                @endif
                                <a href="{{ route('projects.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded shadow-sm hover:shadow-indigo-500/30 transition-all text-center">
                                    Submit New Project
                                </a>
                            </div>
                        @elseif($hasSubmitted)
                            <div class="bg-indigo-50 dark:bg-indigo-900/40 border border-indigo-200 dark:border-indigo-800 rounded-lg px-6 py-3">
                                <span class="text-sm font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-wide">
                                    ✓ You have submitted your required project.
                                </span>
                            </div>
                        @else
                            <div class="bg-red-50 dark:bg-red-900/40 border border-red-200 dark:border-red-800 rounded-lg px-6 py-3">
                                <span class="text-xs sm:text-sm font-black text-red-600 dark:text-red-400 uppercase tracking-wide">
                                    @if(!$submissionsOpen)
                                        ⚠️ Submissions are currently closed.
                                    @else
                                        ⚠️ The submission deadline has passed ({{ \Carbon\Carbon::parse($deadlineStr)->format('M d, Y h:i A') }}).
                                    @endif
                                </span>
                            </div>
                        @endif
                    </div>
                    
                    @if($myProjects->isEmpty())
                        <p class="text-gray-500 italic">You have no pending submissions.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Project Title</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Adviser</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Submitted</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Record Status</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($myProjects as $project)
                                    <tr>
                                        <td class="px-6 py-4">
                                            <div class="whitespace-normal break-words max-w-xs md:max-w-md">
                                                <a href="{{ route('projects.show', $project) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-white font-bold leading-tight">
                                                    {{ $project->title }}
                                                </a>
                                                <div class="text-[10px] text-gray-500 font-bold mt-1 uppercase tracking-widest flex items-center gap-2">
                                                    <span>{{ $project->year }}</span>
                                                    <span class="w-1 h-1 bg-gray-400 rounded-full"></span>
                                                    <span>{{ $project->program }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 italic whitespace-nowrap">{{ $project->adviser->name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $project->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($project->status === 'published')
                                                <span class="px-3 py-1 inline-flex text-[10px] leading-5 font-black uppercase tracking-widest rounded-full bg-green-500 text-gray-200 border border-emerald-200 shadow-sm">
                                                    Official Record
                                                </span>
                                            @elseif($project->status === 'approved')
                                                <span class="px-3 py-1 inline-flex text-[10px] leading-5 font-black uppercase tracking-widest rounded-full bg-indigo-700 text-gray-200 border border-indigo-200 shadow-sm">
                                                    Confirmed Final
                                                </span>
                                            @elseif($project->status === 'rejected')
                                                <span class="px-3 py-1 inline-flex text-[10px] leading-5 font-black uppercase tracking-widest rounded-full bg-red-100 text-red-800 border border-red-200 shadow-sm">
                                                    Returned
                                                </span>
                                            @else
                                                <span class="px-3 py-1 inline-flex text-[10px] leading-5 font-black uppercase tracking-widest rounded-full bg-emerald-100 text-gray-700 border border-green-200 shadow-sm">
                                                    Pending
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center text-sm font-medium">
                                            <div class="flex flex-col items-center gap-2">
                                                <div class="flex flex-row items-center gap-2">
                                                    <a href="{{ route('projects.show', $project) }}" class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-indigo-500 hover:text-indigo-700 hover:underline transition whitespace-nowrap">
                                                        View Details
                                                    </a>
                                                    @if($project->status === 'pending' || $project->status === 'rejected')
                                                        <span class="text-gray-400 text-xs">|</span>
                                                        <a href="{{ route('projects.edit', $project) }}" class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-green-500 hover:text-green-600 hover:underline transition whitespace-nowrap">
                                                            {{ $project->status === 'rejected' ? 'Edit & Resubmit' : 'Edit' }}
                                                        </a>
                                                    @endif
                                                </div>
                                                @if($project->status === 'pending' || $project->status === 'rejected')
                                                    <form class="flex" action="{{ route('projects.cancel', $project) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this submission? This will delete the project and all uploaded files.');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-red-400 hover:text-red-300 hover:underline transition bg-transparent border-0 p-0 cursor-pointer whitespace-nowrap">
                                                            {{ $project->status === 'rejected' ? 'Delete' : 'Cancel' }}
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    {{-- Inline feedback row for returned projects --}}
                                    @if($project->status === 'rejected' && $project->rejection_reason)
                                    <tr class="bg-red-50/50">
                                        <td colspan="5" class="px-6 py-3">
                                            <div class="flex items-start gap-3">
                                                <svg class="w-4 h-4 text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                </svg>
                                                <div>
                                                    <p class="text-[10px] text-red-500 uppercase font-bold tracking-wider mb-1">Adviser's Feedback</p>
                                                    <p class="text-sm text-red-700 leading-relaxed">{{ $project->rejection_reason }}</p>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Research CTA -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-2 border-dashed border-indigo-200">
                <div class="p-8 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-100 text-indigo-600 rounded-full mb-4">
                        <span class="text-2xl">🔍</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-200 mb-2">Ready to start your research?</h3>
                    <p class="text-gray-500 max-w-sm mx-auto mb-6">Explore thousands of verified capstone projects, manuscripts, and categories in our dedicated institutional library.</p>
                    <a href="{{ route('projects.index') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-bold rounded-lg shadow-md hover:bg-indigo-700 transition transform hover:-translate-y-0.5">
                        Go to Browse Repository &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
