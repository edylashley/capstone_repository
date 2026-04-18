<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-white leading-tight">
            {{ __('Final Version Confirmation') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Faculty Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border-l-4 border-indigo-500">
                    <div class="text-xs font-bold text-indigo-600 uppercase tracking-wider">Pending Confirmation</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $projects->where('status', 'pending')->count() }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border-l-4 border-green-500">
                    <div class="text-xs font-bold text-green-600 uppercase tracking-wider">My Historical Archives</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $projects->whereIn('status', ['approved', 'published'])->count() }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border-l-4 border-blue-500">
                    <div class="text-xs font-bold text-blue-600 uppercase tracking-wider">Assigned Advisees</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $projects->count() }}</div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-bold mb-4">Final Record Confirmation Queue</h3>

                    @if($projects->isEmpty())
                        <p class="text-gray-500 italic">No pending final records waiting for confirmation.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Project Title</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Authors</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Submitted</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Record Status</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($projects as $project)
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
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 italic">
                                            <div class="whitespace-normal break-words max-w-xs">
                                                {{ $project->authors->pluck('name')->join(', ') }}
                                            </div>
                                        </td>
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
                                        <td class="px-6 py-4 text-center">
                                            <div class="inline-flex flex-col items-start gap-2 text-left">
                                                <a href="{{ route('projects.show', $project) }}" class="group flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-slate-300 hover:text-indigo-400 transition-all">
                                                    <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    View Details
                                                </a>

                                                @if($project->status === 'pending')
                                                    <form method="POST" action="{{ route('faculty.projects.approve', $project) }}" onsubmit="return confirm('Confirm that this is the final, defended version of the project?');">
                                                        @csrf
                                                        <button type="submit" class="group flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-slate-300 hover:text-emerald-400 transition-all bg-transparent border-0 p-0 cursor-pointer">
                                                            <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                            Confirm
                                                        </button>
                                                    </form>
                                                    
                                                    <button type="button"
                                                            onclick="openReturnModal({{ $project->id }}, '{{ addslashes($project->title) }}')"
                                                            class="group flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-slate-300 hover:text-amber-400 transition-all bg-transparent border-0 p-0 cursor-pointer">
                                                        <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-amber-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                                        Return
                                                    </button>
                                                    
                                                    <form method="POST" action="{{ route('faculty.projects.reject-advisory', $project) }}" onsubmit="return confirm('Confirm that you are NOT the adviser for this project? This will notify the student and allow them to pick the correct adviser.');">
                                                        @csrf
                                                        <button type="submit" class="group flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-slate-300 hover:text-red-400 transition-all bg-transparent border-0 p-0 cursor-pointer">
                                                            <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-red-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                            Not My Project
                                                        </button>
                                                    </form>
                                                @elseif($project->status === 'approved')
                                                    <form method="POST" action="{{ route('faculty.projects.cancel', $project) }}" onsubmit="return confirm('Please confirm: You want to REVOKE the approval for this project? It will return to Pending status.');">
                                                        @csrf
                                                        <button type="submit" class="group flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-slate-300 hover:text-red-400 transition-all bg-transparent border-0 p-0 cursor-pointer">
                                                            <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-red-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                            Revoke
                                                        </button>
                                                    </form>
                                                @elseif($project->status === 'rejected' && $project->rejection_reason)
                                                    <button type="button"
                                                            onclick="openViewFeedbackModal('{{ addslashes($project->title) }}', `{{ addslashes($project->rejection_reason) }}`)"
                                                            class="group flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-slate-300 hover:text-white transition-all bg-transparent border-0 p-0 cursor-pointer">
                                                        <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                                                        Feedback
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Return Project Modal -->
    <div id="return-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4" onclick="if(event.target===this) closeReturnModal()">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 overflow-hidden" onclick="event.stopPropagation()">
            <div class="bg-amber-50 border-b border-amber-200 px-6 py-4">
                <h3 class="text-lg font-bold text-amber-800">Return Project for Revisions</h3>
                <p id="return-modal-title" class="text-sm text-amber-600 mt-1 font-medium"></p>
            </div>
            <form id="return-form" method="POST" action="">
                @csrf
                <div class="p-6">
                    <label for="rejection_reason" class="block text-sm font-bold text-gray-700 mb-2">
                        Feedback for Student <span class="text-red-500">*</span>
                    </label>
                    <p class="text-xs text-gray-500 mb-3">
                        Please explain what needs to be corrected. This feedback will be visible to the student and included in the email notification.
                    </p>
                    <textarea
                        id="rejection_reason"
                        name="rejection_reason"
                        rows="5"
                        required
                        maxlength="2000"
                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-sm"
                        placeholder="e.g., The manuscript is missing the approval/signature page. Please add the signed approval sheet and resubmit."></textarea>
                    <p class="text-[10px] text-gray-400 mt-1 text-right"><span id="char-count">0</span>/2000 characters</p>
                    @error('rejection_reason')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t">
                    <button type="button" onclick="closeReturnModal()" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-bold text-gray-700 hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-sm font-bold shadow-sm transition">
                        Return to Student
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Feedback Modal (for already returned projects) -->
    <div id="view-feedback-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4" onclick="if(event.target===this) closeViewFeedbackModal()">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 overflow-hidden" onclick="event.stopPropagation()">
            <div class="bg-red-50 border-b border-red-200 px-6 py-4">
                <h3 class="text-lg font-bold text-red-800">Return Feedback Sent</h3>
                <p id="view-feedback-title" class="text-sm text-red-600 mt-1 font-medium"></p>
            </div>
            <div class="p-6">
                <p class="text-xs text-gray-500 mb-2 uppercase font-bold tracking-wider">Your feedback to the student:</p>
                <div id="view-feedback-content" class="text-sm text-gray-700 bg-gray-50 rounded-xl p-4 border border-gray-200 whitespace-pre-wrap"></div>
            </div>
            <div class="bg-gray-50 px-6 py-4 flex justify-end border-t">
                <button type="button" onclick="closeViewFeedbackModal()" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-bold text-gray-700 hover:bg-gray-50 transition">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
        // Return Modal
        function openReturnModal(projectId, projectTitle) {
            const modal = document.getElementById('return-modal');
            const form = document.getElementById('return-form');
            const titleEl = document.getElementById('return-modal-title');
            
            form.action = `/faculty/projects/${projectId}/reject`;
            titleEl.textContent = `"${projectTitle}"`;
            document.getElementById('rejection_reason').value = '';
            document.getElementById('char-count').textContent = '0';
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
            
            setTimeout(() => document.getElementById('rejection_reason').focus(), 100);
        }

        function closeReturnModal() {
            const modal = document.getElementById('return-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }

        // Character counter
        document.getElementById('rejection_reason').addEventListener('input', function() {
            document.getElementById('char-count').textContent = this.value.length;
        });

        // View Feedback Modal
        function openViewFeedbackModal(projectTitle, feedback) {
            const modal = document.getElementById('view-feedback-modal');
            document.getElementById('view-feedback-title').textContent = `"${projectTitle}"`;
            document.getElementById('view-feedback-content').textContent = feedback;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeViewFeedbackModal() {
            const modal = document.getElementById('view-feedback-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }

        // Close modals on Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeReturnModal();
                closeViewFeedbackModal();
            }
        });

        // Auto-open modal if there's a validation error for rejection_reason
        @if($errors->has('rejection_reason'))
            document.addEventListener('DOMContentLoaded', function() {
                // Find the first pending project to re-open the modal
                const firstPendingBtn = document.querySelector('[onclick^="openReturnModal"]');
                if (firstPendingBtn) firstPendingBtn.click();
            });
        @endif
    </script>
</x-app-layout>

