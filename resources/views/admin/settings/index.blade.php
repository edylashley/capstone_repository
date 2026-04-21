@push('styles')
<style>
    /* Force browser native date/time icons to be white */
    ::-webkit-calendar-picker-indicator {
        filter: invert(1);
        cursor: pointer;
    }
</style>
@endpush

<x-app-layout>
    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            {{-- Integrated Header --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-2">
                <div>
                    <h2 class="font-black text-4xl text-white uppercase tracking-tighter leading-none">System Settings</h2>
                    <p class="text-[10px] text-indigo-400 uppercase tracking-[0.4em] font-black mt-3 opacity-80">General Website Configuration</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-slate-900/50 text-slate-400 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-slate-800 hover:text-white transition-all shadow-inner border border-white/5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Dashboard
                    </a>
                </div>
            </div>
            
            @if(session('success'))
                <div class="bg-emerald-100 border-l-4 border-emerald-500 text-emerald-900 p-3 rounded shadow-sm flex items-center gap-4 py-4" role="alert">
                    <div class="flex-shrink-0 bg-emerald-500 text-white rounded-full p-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-black text-xs uppercase tracking-widest text-emerald-600 mb-0.5">Success</p>
                        <p class="text-sm font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900 dark:text-gray-100 border-b-4 border-indigo-500">
                    <h3 class="text-xl font-bold mb-6 text-indigo-600 dark:text-indigo-400">Website Configuration</h3>
                    
                    <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- General Settings -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <div>
                                <label for="repository_name" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">
                                    Repository / Program Name <span class="text-red-500">*</span>
                                </label>
                                <p class="text-xs text-gray-500 mb-2">The branding text shown in headers and reports.</p>
                                <input type="text" id="repository_name" name="repository_name" 
                                    value="{{ old('repository_name', $settings['repository_name']) }}" 
                                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('repository_name') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="academic_year" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">
                                    Current Academic Year <span class="text-xs font-normal text-gray-500 ml-1">(Optional)</span>
                                </label>
                                <p class="text-xs text-gray-500 mb-2">The latest valid year for new submissions.</p>
                                <input type="number" id="academic_year" name="academic_year" 
                                    value="{{ old('academic_year', $settings['academic_year']) }}" 
                                    placeholder="e.g., {{ date('Y') }}"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('academic_year') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Upload Limit Settings -->
                        <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-8">
                            <h4 class="text-md font-bold text-gray-800 dark:text-gray-200 mb-4 border-l-4 border-emerald-500 pl-3">Upload Configurations</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label for="max_upload_size" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">
                                        Max Manuscript Size (MB) <span class="text-red-500">*</span>
                                    </label>
                                    <p class="text-xs text-gray-500 mb-2">Limit for the main PDF manuscript.</p>
                                    <input type="number" id="max_upload_size" name="max_upload_size" 
                                        value="{{ old('max_upload_size', $settings['max_upload_size']) }}" 
                                        min="1"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('max_upload_size') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="max_attachment_size" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">
                                        Max Attachment Size (MB) <span class="text-red-500">*</span>
                                    </label>
                                    <p class="text-xs text-gray-500 mb-2">Limit for code/zip attachments.</p>
                                    <input type="number" id="max_attachment_size" name="max_attachment_size" 
                                        value="{{ old('max_attachment_size', $settings['max_attachment_size']) }}" 
                                        min="1"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('max_attachment_size') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="allowed_file_types" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">
                                        Allowed Attachment Types <span class="text-red-500">*</span>
                                    </label>
                                    <p class="text-xs text-gray-500 mb-2">Extensions (e.g., zip,rar,7z,sql).</p>
                                    <input type="text" id="allowed_file_types" name="allowed_file_types" 
                                        value="{{ old('allowed_file_types', $settings['allowed_file_types']) }}" 
                                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('allowed_file_types') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Submission Controls -->
                        <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-8">
                            <h4 class="text-md font-bold text-gray-800 dark:text-gray-200 mb-4 border-l-4 border-blue-500 pl-3">Submission Controls</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-blue-50/50 dark:bg-blue-900/10 p-6 rounded-xl border border-blue-100 dark:border-blue-800/50">
                                
                                <div class="col-span-1 md:col-span-2 flex items-center gap-6 p-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">
                                    <div class="flex-shrink-0">
                                        <input type="checkbox" name="submissions_open" id="submissions_open" value="1" 
                                            class="w-8 h-8 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 cursor-pointer transition-all"
                                            {{ old('submissions_open', $settings['submissions_open']) == '1' ? 'checked' : '' }} />
                                    </div>
                                    <div>
                                        <label for="submissions_open" class="font-bold text-lg text-gray-800 dark:text-gray-200 cursor-pointer block">Accepting New Submissions</label>
                                        <p class="text-xs text-gray-500 mt-1">When turned OFF, the "Submit Project" button will be completely hidden from all students.</p>
                                    </div>
                                </div>

                                <div class="col-span-1 md:col-span-2">
                                    <label for="submission_deadline_date" class="block text-sm font-bold text-white mb-1">
                                        Strict Submission Deadline <span class="text-xs font-normal text-white ml-2">(Optional)</span>
                                    </label>
                                    <p class="text-xs text-white mb-4">If a date and time are set, the system will automatically lock and reject submissions past this exact moment.</p>
                                    
                                    <div class="flex flex-col md:flex-row gap-4 mb-2">
                                        <div class="flex-1 w-full">
                                            <label class="block text-[10px] uppercase font-black text-gray-300 mb-1 tracking-widest">Select Date</label>
                                            <div class="relative">
                                                <input type="date" id="submission_deadline_date" name="submission_deadline_date" 
                                                    value="{{ old('submission_deadline_date', $settings['submission_deadline_date']) }}" 
                                                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 pl-10">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-white">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="flex-1 w-full">
                                            <label class="block text-[10px] uppercase font-black text-gray-300 mb-1 tracking-widest">Select Cut-off Time</label>
                                            <div class="relative">
                                                <input type="time" id="submission_deadline_time" name="submission_deadline_time" 
                                                    value="{{ old('submission_deadline_time', $settings['submission_deadline_time']) }}" 
                                                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 pl-10">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-white">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex justify-center">
                                        <button type="button" 
                                            onclick="document.getElementById('submission_deadline_date').value = ''; document.getElementById('submission_deadline_time').value = '';"
                                            class="text-[10px] font-bold text-gray-200 hover:text-red-600 transition-colors uppercase tracking-widest bg-transparent border-0 p-0 cursor-pointer">
                                            Clear
                                        </button>
                                    </div>
                                    
                                    @error('submission_deadline_date') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                                    @error('submission_deadline_time') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                                </div>

                            </div>
                        </div>

                        <!-- Activity Log Settings -->
                        <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-8">
                            <h4 class="text-md font-bold text-gray-800 dark:text-gray-200 mb-4 border-l-4 border-amber-500 pl-3">Activity Log Settings</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="log_retention_days" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">
                                        Log Retention Period (Days) <span class="text-red-500">*</span>
                                    </label>
                                    <p class="text-xs text-gray-500 mb-2">Number of days to keep activity logs. Logs older than this will be auto-pruned to save space.</p>
                                    <input type="number" id="log_retention_days" name="log_retention_days" 
                                        value="{{ old('log_retention_days', $settings['log_retention_days'] ?? '90') }}" 
                                        min="7" max="3650"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-amber-500 focus:ring-amber-500">
                                    @error('log_retention_days') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div class="flex items-end">
                                    <div class="p-4 bg-amber-50 dark:bg-amber-900/10 rounded-lg border border-amber-100 dark:border-amber-800/50 w-full">
                                        <p class="text-[10px] text-amber-600 dark:text-amber-400 font-bold uppercase tracking-widest mb-1">Cyber-Hardening Tip</p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">Higher retention (90+ days) is recommended for institutional audits, while lower retention (30 days) saves database storage.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Maintenance Settings -->
                         <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-8">
                            <h4 class="text-md font-bold text-gray-800 dark:text-gray-200 mb-4 border-l-4 border-red-500 pl-3">Danger Zone</h4>
                            
                            <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg border border-red-200 dark:border-red-800 flex items-start gap-3">
                                <div class="mt-1">
                                    <input type="checkbox" id="maintenance_mode" name="maintenance_mode" value="1" 
                                        {{ old('maintenance_mode', $settings['maintenance_mode']) == '1' ? 'checked' : '' }}
                                        class="h-6 w-6 mt-4 rounded border-gray-600 text-red-600 focus:ring-red-500">
                                </div>
                                <div class="ml-2 text-sm">
                                    <label for="maintenance_mode" class="font-medium text-red-800 dark:text-red-400 font-bold text-base">Enable Maintenance Mode</label>
                                    <p class="text-red-600 dark:text-red-300 mt-1">
                                        When checked, the system will prevent students and advisers from accessing the platform. Only admins will be able to log in. Use this when performing system upgrades.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg transition-all flex items-center gap-2 uppercase tracking-widest text-[10px]">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
