<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-white leading-tight">
            {{ __('Final Version Confirmation') }}
        </h2>
    </x-slot>

    <div class="py-4 md:py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            <!-- Faculty Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-slate-900 p-6 rounded-xl shadow-sm border-l-4 border-indigo-500 transition-transform hover:scale-[1.02] border border-white/5">
                    <div class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-1">Pending Confirmation</div>
                    <div class="text-3xl font-black text-white">{{ $projects->where('status', 'pending')->count() }}</div>
                </div>
                <div class="bg-slate-900 p-6 rounded-xl shadow-sm border-l-4 border-emerald-500 transition-transform hover:scale-[1.02] border border-white/5">
                    <div class="text-[10px] font-black text-emerald-400 uppercase tracking-widest mb-1">Historical Archives</div>
                    <div class="text-3xl font-black text-white">{{ $projects->whereIn('status', ['approved', 'published'])->count() }}</div>
                </div>
                <div class="bg-slate-900 p-6 rounded-xl shadow-sm border-l-4 border-blue-500 transition-transform hover:scale-[1.02] sm:col-span-2 lg:col-span-1 border border-white/5">
                    <div class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-1">Assigned Advisees</div>
                    <div class="text-3xl font-black text-white">{{ $projects->count() }}</div>
                </div>
            </div>

            <div class="bg-slate-900 overflow-hidden shadow-sm rounded-2xl border border-white/5">
                <div class="p-4 md:p-8">
                    <div class="mb-8">
                        <h3 class="text-xl font-black text-white leading-tight">Final Record Confirmation Queue</h3>
                        <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold mt-1">Review and verify the final defended versions of research projects</p>
                    </div>

                    @if($projects->isEmpty())
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-gray-50 dark:bg-gray-700/30 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">⏳</div>
                            <p class="text-gray-500 italic">No pending final records waiting for confirmation.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto -mx-4 sm:mx-0">
                            <div class="inline-block min-w-full align-middle">
                                <table class="min-w-full divide-y divide-white/5">
                                    <thead>
                                        <tr class="text-left text-[10px] font-black uppercase tracking-widest text-slate-500">
                                            <th class="px-6 py-4">Project Title</th>
                                            <th class="px-6 py-4">Authors</th>
                                            <th class="px-6 py-4 text-center">Status</th>
                                            <th class="px-6 py-4 text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-white/5">
                                        @foreach($projects as $project)
                                            <tr class="group hover:bg-white/[0.02] transition-colors">
                                                <td class="px-6 py-4">
                                                    <div class="max-w-xs md:max-w-md">
                                                        <div class="text-sm font-bold text-white mb-1 leading-snug truncate md:whitespace-normal">{{ $project->title }}</div>
                                                        <div class="flex items-center gap-2">
                                                            <span class="text-[9px] font-black uppercase tracking-tighter text-indigo-400 px-1.5 py-0.5 bg-indigo-50 dark:bg-indigo-900/30 rounded border border-indigo-100 dark:border-indigo-800">{{ $project->program }}</span>
                                                            <span class="text-[9px] font-bold text-gray-400 uppercase italic">{{ $project->year }}</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-xs text-gray-500 dark:text-gray-400 italic">
                                                    <div class="max-w-[150px] truncate md:whitespace-normal md:max-w-xs">
                                                        {{ $project->authors_list ?: $project->authors->pluck('name')->join(', ') }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-center">
                                                    @php
                                                        $statusClasses = match($project->status) {
                                                            'published' => 'bg-emerald-500 text-white border-emerald-600',
                                                            'approved' => 'bg-indigo-600 text-white border-indigo-700',
                                                            'rejected' => 'bg-red-100 text-red-600 border-red-200',
                                                            default => 'bg-amber-100 text-amber-600 border-amber-200',
                                                        };
                                                    @endphp
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest border {{ $statusClasses }} shadow-sm">
                                                        {{ $project->status }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-center">
                                                    <div class="inline-flex flex-col items-start gap-2 text-left">
                                                        <a href="{{ route('projects.show', $project) }}" class="group flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-slate-300 hover:text-indigo-400 transition-all">
                                                            <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                            View Details
                                                        </a>

                                                        @if($project->status === 'pending')
                                                            <form method="POST" action="{{ route('faculty.projects.approve', $project) }}" onsubmit="return confirm('Confirm that this is the final, defended version of the project?');" class="inline">
                                                                @csrf
                                                                <button type="submit" class="group flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-slate-300 hover:text-emerald-400 transition-all bg-transparent border-0 p-0 cursor-pointer text-left">
                                                                    <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                                    Confirm
                                                                </button>
                                                            </form>
                                                            
                                                            <button type="button"
                                                                    onclick="openReturnModal({{ $project->id }}, '{{ addslashes($project->title) }}')"
                                                                    class="group flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-slate-300 hover:text-amber-400 transition-all bg-transparent border-0 p-0 cursor-pointer text-left">
                                                                <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-amber-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                                                Return
                                                            </button>
                                                            
                                                            <form method="POST" action="{{ route('faculty.projects.reject-advisory', $project) }}" onsubmit="return confirm('Confirm that you are NOT the adviser for this project? This will notify the student and allow them to pick the correct adviser.');" class="inline">
                                                                @csrf
                                                                <button type="submit" class="group flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-slate-300 hover:text-red-400 transition-all bg-transparent border-0 p-0 cursor-pointer text-left">
                                                                    <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-red-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                                    Not My Project
                                                                </button>
                                                            </form>
                                                        @elseif($project->status === 'approved')
                                                            <form method="POST" action="{{ route('faculty.projects.cancel', $project) }}" onsubmit="return confirm('Please confirm: You want to REVOKE the approval for this project? It will return to Pending status.');" class="inline">
                                                                @csrf
                                                                <button type="submit" class="group flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-slate-300 hover:text-red-400 transition-all bg-transparent border-0 p-0 cursor-pointer text-left">
                                                                    <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-red-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                                    Revoke
                                                                </button>
                                                            </form>
                                                        @elseif($project->status === 'rejected' && $project->rejection_reason)
                                                            <button type="button"
                                                                    onclick="openViewFeedbackModal('{{ addslashes($project->title) }}', `{{ addslashes($project->rejection_reason) }}`)"
                                                                    class="group flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-slate-300 hover:text-white transition-all bg-transparent border-0 p-0 cursor-pointer text-left">
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
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Return Project Modal -->
    <div id="return-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4" onclick="if(event.target===this) closeReturnModal()">
        <div class="bg-slate-900 rounded-2xl shadow-2xl max-w-md w-full mx-4 overflow-hidden border border-white/10" onclick="event.stopPropagation()">
            <div class="bg-amber-950/20 border-b border-white/5 px-6 py-4">
                <h3 class="text-lg font-bold text-amber-500">Return Project for Revisions</h3>
                <p id="return-modal-title" class="text-sm text-amber-600/80 mt-1 font-medium"></p>
            </div>
            <form id="return-form" method="POST" action="">
                @csrf
                <div class="p-6">
                    <label for="rejection_reason" class="block text-sm font-bold text-slate-300 mb-2">
                        Feedback for Student <span class="text-red-500">*</span>
                    </label>
                    <p class="text-xs text-slate-500 mb-3">
                        Please explain what needs to be corrected. This feedback will be visible to the student and included in the email notification.
                    </p>
                    <textarea
                        id="rejection_reason"
                        name="rejection_reason"
                        rows="5"
                        required
                        maxlength="2000"
                        class="w-full rounded-xl border-white/10 bg-slate-950 text-white shadow-sm focus:border-amber-500 focus:ring-amber-500 text-sm"
                        placeholder="e.g., The manuscript is missing the approval/signature page. Please add the signed approval sheet and resubmit."></textarea>
                    <p class="text-[10px] text-slate-500 mt-1 text-right"><span id="char-count">0</span>/2000 characters</p>
                    @error('rejection_reason')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="bg-slate-950/50 px-6 py-4 flex justify-end gap-3 border-t border-white/5">
                    <button type="button" onclick="closeReturnModal()" class="px-4 py-2 bg-slate-800 border border-white/5 rounded-lg text-sm font-bold text-slate-300 hover:bg-slate-700 transition">
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

