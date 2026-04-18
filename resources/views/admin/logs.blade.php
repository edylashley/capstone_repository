<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('System Activity Logs') }}
            </h2>
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition ease-in-out duration-150">
                Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-bold mb-4">System Timeline</h3>
                    
                    <div class="max-h-[600px] overflow-y-auto pr-2 custom-scrollbar space-y-4">
                        <style>
                            .custom-scrollbar::-webkit-scrollbar { width: 6px; }
                            .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
                            .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
                            .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
                        </style>
                        @foreach($logs as $log)
                            @php
                                $statusClasses = match(true) {
                                    str_contains($log->action, 'login') => 'border-green-500 bg-white shadow-green-500/5',
                                    str_contains($log->action, 'logout') => 'border-red-500 bg-white shadow-red-500/5',
                                    str_contains($log->action, 'approved') || str_contains($log->action, 'confirm') => 'border-emerald-500 bg-white shadow-emerald-500/5',
                                    str_contains($log->action, 'failed') || str_contains($log->action, 'error') || str_contains($log->action, 'blocked') => 'border-orange-500 bg-white shadow-orange-500/5',
                                    str_contains($log->action, 'create') || str_contains($log->action, 'upload') || str_contains($log->action, 'request') => 'border-blue-500 bg-white shadow-blue-500/5',
                                    default => 'border-slate-200 bg-white shadow-slate-500/5',
                                };

                                $badgeClasses = match(true) {
                                    str_contains($log->action, 'login') || str_contains($log->action, 'approved') || str_contains($log->action, 'confirm') => 'bg-emerald-600 text-white',
                                    str_contains($log->action, 'logout') || str_contains($log->action, 'failed') || str_contains($log->action, 'blocked') => 'bg-red-600 text-white',
                                    str_contains($log->action, 'create') || str_contains($log->action, 'upload') || str_contains($log->action, 'request') => 'bg-indigo-600 text-white',
                                    default => 'bg-gray-200 text-gray-800',
                                };
                            @endphp
                            <div class="flex items-start gap-4 p-4 border-l-4 {{ $statusClasses }} rounded-r-lg transition-all hover:shadow-sm">
                                <div class="flex-shrink-0 mt-1">
                                    <span class="p-2 bg-white rounded-full shadow-sm">
                                        @if(str_contains($log->action, 'user')) 👤 @elseif(str_contains($log->action, 'project')) 📂 @else ⚙️ @endif
                                    </span>
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-black text-sm text-black">
                                                {{ $log->user->name ?? 'System' }}
                                            </p>
                                            <p class="text-[14px] text-gray-900 font-bold leading-tight">
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
                                        <span class="text-xs text-gray-800 font-black whitespace-nowrap ml-4">{{ $log->created_at ? $log->created_at->diffForHumans() : 'N/A' }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 mt-2">
                                        <div style="color: #000 !important;" class="text-[10px] font-black bg-gray-200 px-2 py-0.5 rounded border border-gray-400 uppercase tracking-tighter">
                                            IP: {{ $log->ip }}
                                        </div>
                                        <div style="color: #000 !important;" class="text-[10px] font-black bg-gray-200 px-2 py-0.5 rounded border border-gray-400 uppercase tracking-tighter">
                                            ID: {{ $log->target_id }}
                                        </div>
                                    </div>

                                    @if($log->meta)
                                        <div class="mt-4 p-4 rounded-xl bg-gray-200 border border-gray-400 shadow-inner relative overflow-hidden">
                                            @if(isset($log->meta['changes']))
                                                <div class="space-y-4">
                                                    @foreach($log->meta['changes'] as $field => $change)
                                                        <div class="flex flex-col items-center justify-center p-2 bg-white/50 rounded-lg border border-gray-300 shadow-sm">
                                                            <span style="color: #000 !important;" class="text-[11px] font-black uppercase tracking-widest mb-2 block border-b border-gray-300 w-full text-center pb-1">{{ str_replace('_', ' ', $field) }}</span>
                                                            <div class="flex items-center gap-4">
                                                                @if(is_array($change))
                                                                    <div class="flex flex-col items-center">
                                                                        <span class="text-[8px] font-black text-red-600 uppercase mb-0.5">FROM</span>
                                                                        <span style="color: #000 !important;" class="text-sm bg-red-100 border border-red-300 px-3 py-1 rounded font-black line-through shadow-sm">{{ $change['from'] ?: 'None' }}</span>
                                                                    </div>
                                                                    <span style="color: #000 !important;" class="font-black text-2xl">→</span>
                                                                    <div class="flex flex-col items-center">
                                                                        <span class="text-[8px] font-black text-green-600 uppercase mb-0.5">TO</span>
                                                                        <span style="color: #000 !important;" class="text-sm bg-green-100 border border-green-300 px-3 py-1 rounded font-black shadow-sm">{{ $change['to'] ?: 'None' }}</span>
                                                                    </div>
                                                                @else
                                                                    <span style="color: #000 !important;" class="text-sm bg-blue-100 border border-blue-300 px-3 py-1 rounded font-black shadow-sm">{{ $change ?: 'None' }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div style="color: #000 !important;" class="text-[11px] font-mono font-bold leading-relaxed bg-white p-3 rounded-lg border border-gray-300 shadow-sm whitespace-pre-wrap break-all">
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
