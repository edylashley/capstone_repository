<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            {{-- Integrated Header --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-2">
                <div>
                    <h2 class="font-black text-4xl text-gray-900 dark:text-white uppercase tracking-tighter leading-none">System Activity</h2>
                    <p class="text-[10px] text-blue-600 dark:text-indigo-400 uppercase tracking-[0.4em] font-black mt-3 opacity-80">Audit Trail & Security Event Tracking</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gray-100 dark:bg-slate-900/50 text-gray-500 dark:text-slate-400 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-gray-200 dark:hover:bg-slate-800 hover:text-gray-900 dark:hover:text-white transition-all shadow-sm dark:shadow-inner border border-gray-200 dark:border-white/5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Dashboard
                    </a>
                </div>
            </div>
            
            <div class="bg-gray-100 dark:bg-slate-900 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-white/5 transition-colors">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h3 class="text-lg font-bold mb-4">System Timeline</h3>
                    
                    <div class="max-h-[600px] overflow-y-auto pr-2 custom-scrollbar space-y-4">
                        <style>
                            .custom-scrollbar::-webkit-scrollbar { width: 6px; }
                            .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
                            .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
                            .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
                            .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; }
                            .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #475569; }
                        </style>
                        @foreach($logs as $log)
                            @php
                                $statusClasses = match(true) {
                                    str_contains($log->action, 'login') => 'border-emerald-500 bg-white dark:bg-slate-800/50',
                                    str_contains($log->action, 'logout') => 'border-rose-500 bg-white dark:bg-slate-800/50',
                                    str_contains($log->action, 'approved') || str_contains($log->action, 'confirm') => 'border-emerald-500 bg-white dark:bg-slate-800/50',
                                    str_contains($log->action, 'failed') || str_contains($log->action, 'error') || str_contains($log->action, 'blocked') => 'border-amber-500 bg-white dark:bg-slate-800/50',
                                    str_contains($log->action, 'create') || str_contains($log->action, 'upload') || str_contains($log->action, 'request') => 'border-blue-500 bg-white dark:bg-slate-800/50',
                                    default => 'border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800/50',
                                };

                                $badgeClasses = match(true) {
                                    str_contains($log->action, 'login') || str_contains($log->action, 'approved') || str_contains($log->action, 'confirm') => 'bg-emerald-600 text-white',
                                    str_contains($log->action, 'logout') || str_contains($log->action, 'failed') || str_contains($log->action, 'blocked') => 'bg-rose-600 text-white',
                                    str_contains($log->action, 'create') || str_contains($log->action, 'upload') || str_contains($log->action, 'request') => 'bg-indigo-600 text-white',
                                    default => 'bg-gray-200 dark:bg-slate-700 text-gray-800 dark:text-gray-300',
                                };
                            @endphp
                            <div class="flex items-start gap-4 p-4 border-l-4 border-y border-r border-y-gray-200 border-r-gray-200 dark:border-y-white/5 dark:border-r-white/5 {{ $statusClasses }} rounded-r-lg transition-all hover:shadow-sm">
                                <div class="flex-shrink-0 mt-1">
                                    <span class="p-2 bg-white dark:bg-slate-900 border border-gray-200 dark:border-white/10 rounded-full shadow-sm">
                                        @if(str_contains($log->action, 'user')) 👤 @elseif(str_contains($log->action, 'project')) 📂 @else ⚙️ @endif
                                    </span>
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-black text-sm text-gray-900 dark:text-white">
                                                {{ $log->user->name ?? 'System' }}
                                            </p>
                                            <p class="text-[14px] text-gray-700 dark:text-gray-300 font-bold leading-tight">
                                                @php
                                                    $meta = $log->meta ?? [];
                                                    $description = match($log->action) {
                                                        'login' => 'Logged into the system',
                                                        'logout' => 'Logged out of the session',
                                                        'settings_updated' => 'Updated system configuration preferences',
                                                        'account_created' => 'Created a new user account for ' . ($meta['name'] ?? 'Unknown User'),
                                                        'account_approved' => 'Approved and activated user account for ' . ($meta['name'] ?? 'Unknown User'),
                                                        'account_deleted' => 'Permanently deleted the account belonging to ' . ($meta['deleted_name'] ?? 'Unknown User'),
                                                        'user_updated' => 'Updated profile details for ' . ($meta['name'] ?? 'Unknown User'),
                                                        'upload_project' => 'Submitted a new project: "' . ($meta['title'] ?? 'Unknown') . '"',
                                                        'project_edited' => 'Modified project details for "' . ($meta['title'] ?? 'Unknown') . '"',
                                                        'project_resubmitted' => 'Resubmitted project for review: "' . ($meta['title'] ?? 'Unknown') . '"',
                                                        'project_confirmed_final' => 'Adviser confirmed final version of "' . ($meta['project_title'] ?? 'Unknown') . '"',
                                                        'project_returned_for_revision' => 'Returned project for revision: "' . ($meta['project_title'] ?? 'Unknown') . '"',
                                                        'project_confirmation_cancelled' => 'Reverted project status from approved to pending for "' . ($meta['project_title'] ?? 'Unknown') . '"',
                                                        'publish_project' => 'Published project to the repository: "' . ($meta['project_title'] ?? $meta['title'] ?? 'Unknown') . '"',
                                                        'delete_project' => 'Permanently deleted project: "' . ($meta['title'] ?? 'Unknown') . '"',
                                                        'manuscript_validated' => 'System successfully verified the manuscript for "' . ($meta['title'] ?? 'Unknown') . '"',
                                                        'manuscript_revalidated' => 'Administrator manually re-verified the manuscript for project ID: ' . $log->target_id,
                                                        'manuscript_scan_failed' => 'Security Threat Detected: Manuscript for "' . ($meta['title'] ?? 'Unknown') . '" was blocked by the security scanner.',
                                                        'manuscript_verification_failed' => 'Verification Failed: Manuscript for "' . ($meta['title'] ?? 'Unknown') . '" failed keyword/signature validation.',
                                                        'submission_validation_failed' => 'Invalid Submission: A project upload was rejected due to form errors (e.g., file too large).',
                                                        'submission_rejected_duplicate' => 'Duplicate Rejected: Submission for "' . ($meta['title'] ?? 'Unknown') . '" was blocked due to identical content in the repository.',
                                                        'category_created' => 'Created a new project specialization: ' . ($meta['name'] ?? 'Unknown'),
                                                        'category_updated' => 'Updated the specialization category: ' . ($meta['name'] ?? 'Unknown'),
                                                        'category_deleted' => 'Removed the specialization category: ' . ($meta['name'] ?? 'Unknown'),
                                                        'submission_aborted' => 'Aborted an in-progress submission: "' . ($meta['title'] ?? 'Unknown') . '"',
                                                        'login_blocked_inactive' => 'Attempted login from an inactive or pending account',
                                                        'support_ticket_created' => 'Submitted a support ticket: "' . ($meta['subject'] ?? 'Unknown') . '"',
                                                        'support_ticket_status_changed' => 'Changed status of support ticket: "' . ($meta['subject'] ?? 'Unknown') . '"',
                                                        'support_ticket_deleted' => 'Deleted support ticket: "' . ($meta['subject'] ?? 'Unknown') . '"',
                                                        'profile_updated' => 'Updated their personal profile and contact information',
                                                        'account_self_deleted' => 'Permanently deleted their own account from the system',
                                                        'project_viewed' => 'Opened the detailed view for project: "' . ($meta['title'] ?? 'Unknown') . '"',
                                                        'file_viewed' => 'Viewed/Streamed file: "' . ($meta['filename'] ?? 'Unknown') . '"',
                                                        'file_downloaded' => 'Downloaded a local copy of: "' . ($meta['filename'] ?? 'Unknown') . '"',
                                                        default => str_replace('_', ' ', $log->action)
                                                    };
                                                @endphp
                                                {{ $description }}
                                            </p>
                                            <div class="mt-1 flex items-center gap-2">
                                                <span class="uppercase text-[9px] font-black px-1.5 py-0.5 rounded shadow-sm {{ $badgeClasses }}">
                                                    {{ str_replace('_', ' ', $log->action) }}
                                                </span>
                                            </div>
                                        </div>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 font-black whitespace-nowrap ml-4">{{ $log->created_at ? $log->created_at->diffForHumans() : 'N/A' }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 mt-2">
                                        <div class="text-[10px] text-gray-700 dark:text-gray-300 font-black bg-gray-100 dark:bg-slate-800 px-2 py-0.5 rounded border border-gray-300 dark:border-slate-600 uppercase tracking-tighter">
                                            IP: {{ $log->ip }}
                                        </div>
                                        <div class="text-[10px] text-gray-700 dark:text-gray-300 font-black bg-gray-100 dark:bg-slate-800 px-2 py-0.5 rounded border border-gray-300 dark:border-slate-600 uppercase tracking-tighter">
                                            ID: {{ $log->target_id }}
                                        </div>
                                    </div>

                                    @if($log->meta)
                                        <div class="mt-4 p-4 rounded-xl bg-gray-100 dark:bg-slate-950/50 border border-gray-300 dark:border-white/5 shadow-inner relative overflow-hidden">
                                            @if(isset($log->meta['changes']))
                                                <div class="space-y-4">
                                                    @foreach($log->meta['changes'] as $field => $change)
                                                        <div class="flex flex-col items-center justify-center p-2 bg-white dark:bg-slate-900 rounded-lg border border-gray-200 dark:border-slate-700 shadow-sm">
                                                            <span class="text-gray-800 dark:text-slate-200 text-[11px] font-black uppercase tracking-widest mb-2 block border-b border-gray-200 dark:border-slate-700 w-full text-center pb-1">{{ str_replace('_', ' ', $field) }}</span>
                                                            <div class="flex items-center gap-4">
                                                                @if(is_array($change))
                                                                    <div class="flex flex-col items-center">
                                                                        <span class="text-[8px] font-black text-red-600 dark:text-red-400 uppercase mb-0.5">FROM</span>
                                                                        <span class="text-gray-900 dark:text-red-300 text-sm bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 px-3 py-1 rounded font-black line-through shadow-sm">{{ $change['from'] ?: 'None' }}</span>
                                                                    </div>
                                                                    <span class="text-gray-400 dark:text-slate-500 font-black text-2xl">→</span>
                                                                    <div class="flex flex-col items-center">
                                                                        <span class="text-[8px] font-black text-green-600 dark:text-green-400 uppercase mb-0.5">TO</span>
                                                                        <span class="text-gray-900 dark:text-green-300 text-sm bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 px-3 py-1 rounded font-black shadow-sm">{{ $change['to'] ?: 'None' }}</span>
                                                                    </div>
                                                                @else
                                                                    <span class="text-gray-900 dark:text-blue-300 text-sm bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/20 px-3 py-1 rounded font-black shadow-sm">{{ $change ?: 'None' }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="text-gray-800 dark:text-slate-300 text-[11px] font-mono font-bold leading-relaxed bg-white dark:bg-slate-900 p-3 rounded-lg border border-gray-200 dark:border-slate-700 shadow-sm whitespace-pre-wrap break-all">
                                                    {{ json_encode($log->meta, JSON_PRETTY_PRINT) }}
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6 border-t pt-4">
                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
