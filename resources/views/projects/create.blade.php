<x-app-layout>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-8 px-4 sm:px-0">
                <h2 class="font-black text-3xl text-gray-900 dark:text-white uppercase tracking-tighter leading-none">Submit Capstone Project</h2>
                <a href="{{ route('student.home') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-slate-800 text-gray-900 dark:text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-gray-200 dark:hover:bg-slate-700 transition-all shadow-sm dark:shadow-lg border border-gray-200 dark:border-white/5">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Dashboard
                </a>
            </div>
            <div class="bg-white dark:bg-slate-900 overflow-hidden shadow-sm dark:shadow-sm sm:rounded-2xl p-8 border border-gray-200 dark:border-white/5 transition-colors duration-300">
                <form id="project-form" method="POST" action="{{ route('projects.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-6">
                        <label class="block font-bold text-xs uppercase tracking-widest text-gray-500 dark:text-slate-400 mb-2">Project Title</label>
                        <input type="text" name="title" value="{{ old('title') }}" 
                               class="mt-1 block w-full bg-gray-50 dark:bg-slate-950 border-gray-200 dark:border-white/10 rounded-xl text-gray-900 dark:text-white focus:border-blue-500 dark:focus:border-indigo-500 focus:ring-blue-500 dark:focus:ring-indigo-500 placeholder-gray-400 dark:placeholder-slate-600 transition-colors" 
                               placeholder="Enter your research title" required autocomplete="off">
                        @error('title') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block font-bold text-xs uppercase tracking-widest text-gray-500 dark:text-slate-400 mb-2">Abstract</label>
                        <textarea name="abstract" rows="4" 
                                  class="mt-1 block w-full bg-gray-50 dark:bg-slate-950 border-gray-200 dark:border-white/10 rounded-xl text-gray-900 dark:text-white focus:border-blue-500 dark:focus:border-indigo-500 focus:ring-blue-500 dark:focus:ring-indigo-500 placeholder-gray-400 dark:placeholder-slate-600 transition-colors" 
                                  placeholder="Briefly describe your work..." required>{{ old('abstract') }}</textarea>
                        @error('abstract') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div>
                            <label class="block font-bold text-xs uppercase tracking-widest text-gray-500 dark:text-slate-400 mb-2">Year</label>
                            <input type="number" name="year" value="{{ old('year', date('Y')) }}" 
                                   class="mt-1 block w-full bg-gray-50 dark:bg-slate-950 border-gray-200 dark:border-white/10 rounded-xl text-gray-900 dark:text-white focus:border-blue-500 dark:focus:border-indigo-500 focus:ring-blue-500 dark:focus:ring-indigo-500 transition-colors" required>
                            @error('year') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block font-bold text-xs uppercase tracking-widest text-gray-500 dark:text-slate-400 mb-2">Adviser</label>
                            <input type="text" name="adviser_name" value="{{ old('adviser_name') }}" 
                                   class="mt-1 block w-full bg-gray-50 dark:bg-slate-950 border-gray-200 dark:border-white/10 rounded-xl text-gray-900 dark:text-white focus:border-blue-500 dark:focus:border-indigo-500 focus:ring-blue-500 dark:focus:ring-indigo-500 placeholder-gray-400 dark:placeholder-slate-600 transition-colors" 
                                   placeholder="Full name of your adviser" required autocomplete="off">
                            @error('adviser_name') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block font-bold text-xs uppercase tracking-widest text-gray-500 dark:text-slate-400 mb-2">Program</label>
                            <select name="program" class="mt-1 block w-full bg-gray-50 dark:bg-slate-950 border-gray-200 dark:border-white/10 rounded-xl text-gray-900 dark:text-white focus:border-blue-500 dark:focus:border-indigo-500 focus:ring-blue-500 dark:focus:ring-indigo-500 transition-colors" required>
                                <option value="" disabled {{ (old('program') ?? auth()->user()->program) ? '' : 'selected' }} class="bg-white dark:bg-slate-900">Choose Program</option>
                                @foreach($programs as $prog)
                                    <option value="{{ $prog->abbreviation }}" {{ (old('program') ?? auth()->user()->program) == $prog->abbreviation ? 'selected' : '' }} class="bg-white dark:bg-slate-900">
                                        {{ $prog->abbreviation }}
                                    </option>
                                @endforeach
                            </select>
                            @error('program') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mb-6">
                        <div id="authors-container" class="space-y-3">
                            {{-- First author: pre-filled with submitter, but fully editable --}}
                            <div class="flex items-center gap-2 author-row">
                                <input type="text" name="authors[]" value="{{ old('authors.0', auth()->user()->name) }}" placeholder="Full name of group member" required
                                       class="flex-1 block w-full bg-gray-50 dark:bg-slate-950 border-gray-200 dark:border-white/10 rounded-xl text-gray-900 dark:text-white focus:border-blue-500 dark:focus:border-indigo-500 focus:ring-blue-500 dark:focus:ring-indigo-500 text-sm placeholder-gray-400 dark:placeholder-slate-600 transition-colors" autocomplete="off">
                            </div>

                            {{-- Pre-fill old values if validation failed --}}
                            @if(old('authors'))
                                @foreach(old('authors') as $index => $authorName)
                                    @if($index > 0)
                                    <div class="flex items-center gap-2 author-row">
                                        <input type="text" name="authors[]" value="{{ $authorName }}" placeholder="Full name of group member"
                                               class="flex-1 block w-full bg-gray-50 dark:bg-slate-950 border-gray-200 dark:border-white/10 rounded-xl text-gray-900 dark:text-white focus:border-blue-500 dark:focus:border-indigo-500 focus:ring-blue-500 dark:focus:ring-indigo-500 text-sm placeholder-gray-400 dark:placeholder-slate-600 transition-colors" autocomplete="off">
                                        <button type="button" onclick="this.closest('.author-row').remove()"
                                                class="w-10 h-10 flex items-center justify-center rounded-xl bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-500 hover:bg-red-100 dark:hover:bg-red-900/40 transition-all flex-shrink-0 border border-red-200 dark:border-red-500/20"
                                                title="Remove">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                    @endif
                                @endforeach
                            @else
                                {{-- Default: 2 empty slots for group members --}}
                                <div class="flex items-center gap-2 author-row">
                                    <input type="text" name="authors[]" value="" placeholder="Full name of group member"
                                           class="flex-1 block w-full bg-gray-50 dark:bg-slate-950 border-gray-200 dark:border-white/10 rounded-xl text-gray-900 dark:text-white focus:border-blue-500 dark:focus:border-indigo-500 focus:ring-blue-500 dark:focus:ring-indigo-500 text-sm placeholder-gray-400 dark:placeholder-slate-600 transition-colors" autocomplete="off">
                                    <button type="button" onclick="this.closest('.author-row').remove()"
                                            class="w-10 h-10 flex items-center justify-center rounded-xl bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-500 hover:bg-red-100 dark:hover:bg-red-900/40 transition-all flex-shrink-0 border border-red-200 dark:border-red-500/20"
                                            title="Remove">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                                <div class="flex items-center gap-2 author-row">
                                    <input type="text" name="authors[]" value="" placeholder="Full name of group member"
                                           class="flex-1 block w-full bg-gray-50 dark:bg-slate-950 border-gray-200 dark:border-white/10 rounded-xl text-gray-900 dark:text-white focus:border-blue-500 dark:focus:border-indigo-500 focus:ring-blue-500 dark:focus:ring-indigo-500 text-sm placeholder-gray-400 dark:placeholder-slate-600 transition-colors" autocomplete="off">
                                    <button type="button" onclick="this.closest('.author-row').remove()"
                                            class="w-10 h-10 flex items-center justify-center rounded-xl bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-500 hover:bg-red-100 dark:hover:bg-red-900/40 transition-all flex-shrink-0 border border-red-200 dark:border-red-500/20"
                                            title="Remove">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            @endif
                        </div>

                        {{-- Add Author button --}}
                        <button type="button" id="add-author-btn"
                                class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-50 dark:bg-indigo-900/20 hover:bg-blue-100 dark:hover:bg-indigo-900/40 text-blue-600 dark:text-indigo-400 text-[10px] font-black uppercase tracking-widest transition-all border border-blue-200 dark:border-indigo-500/20">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add Author
                        </button>

                        <p class="mt-3 text-[10px] text-gray-500 dark:text-slate-500 italic">Type the full names of your research group members. Your name is already included.</p>
                        @error('authors') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                        @error('authors.*') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-10">
                        <label class="block font-bold text-xs uppercase tracking-widest text-gray-500 dark:text-slate-400 mb-4 text-center sm:text-left">Project Categories <span class="text-blue-500 dark:text-indigo-400 normal-case font-medium">(Select all that apply)</span></label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach($categories as $category)
                                <label class="relative flex items-center p-4 rounded-2xl border border-gray-200 dark:border-white/5 hover:border-blue-500/50 dark:hover:border-indigo-500/50 hover:bg-gray-50 dark:hover:bg-white/[0.02] cursor-pointer transition-all group bg-gray-50 dark:bg-slate-950/50">
                                    <input type="checkbox" name="categories[]" value="{{ $category->id }}" 
                                        {{ is_array(old('categories')) && in_array($category->id, old('categories')) ? 'checked' : '' }}
                                        class="w-5 h-5 rounded-lg border-gray-300 dark:border-white/10 bg-white dark:bg-slate-900 text-blue-500 dark:text-indigo-500 shadow-sm focus:ring-blue-500 dark:focus:ring-indigo-500 transition-all cursor-pointer">
                                    <span class="ml-3 text-sm font-bold text-gray-600 dark:text-slate-300 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">
                                        {{ $category->name }}
                                    </span>
                                </label>
                            @endforeach

                            {{-- "Others" Option --}}
                            <label class="relative flex items-center p-4 rounded-2xl border border-gray-200 dark:border-white/5 hover:border-blue-500/50 dark:hover:border-indigo-500/50 hover:bg-gray-50 dark:hover:bg-white/[0.02] cursor-pointer transition-all group bg-gray-50 dark:bg-slate-950/50">
                                <input type="checkbox" id="other_category_trigger" name="other_category_trigger" 
                                    {{ old('other_category') ? 'checked' : '' }}
                                    class="w-5 h-5 rounded-lg border-gray-300 dark:border-white/10 bg-white dark:bg-slate-900 text-blue-500 dark:text-indigo-500 shadow-sm focus:ring-blue-500 dark:focus:ring-indigo-500 transition-all cursor-pointer">
                                <span class="ml-3 text-sm font-bold text-gray-600 dark:text-slate-300 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">
                                    Others
                                </span>
                            </label>
                        </div>

                        <div id="other_category_container" class="{{ old('other_category') ? '' : 'hidden' }} mt-4">
                            <input type="text" name="other_category" value="{{ old('other_category') }}" 
                                placeholder="Specify other category name"
                                class="w-full sm:w-1/2 bg-gray-50 dark:bg-slate-950 border-gray-200 dark:border-white/10 rounded-xl text-gray-900 dark:text-white focus:border-blue-500 dark:focus:border-indigo-500 focus:ring-blue-500 dark:focus:ring-indigo-500 text-sm px-4 py-3 font-bold placeholder-gray-400 dark:placeholder-slate-500 placeholder:font-normal">
                            <p class="text-[10px] text-blue-500 dark:text-indigo-400 mt-2 italic ml-1">This will be saved as a custom category for your project.</p>
                        </div>
                        
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const trigger = document.getElementById('other_category_trigger');
                                const container = document.getElementById('other_category_container');
                                
                                trigger.addEventListener('change', function() {
                                    if (this.checked) {
                                        container.classList.remove('hidden');
                                    } else {
                                        container.classList.add('hidden');
                                    }
                                });
                            });
                        </script>
                        @error('categories') <p class="text-red-600 text-sm mt-3 font-semibold">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="p-6 bg-gray-50 dark:bg-slate-950/50 rounded-2xl border-2 border-dashed border-gray-300 dark:border-white/5 group hover:border-blue-500/50 transition-colors">
                            <label class="block font-bold text-xs uppercase tracking-widest text-blue-500 dark:text-blue-400 mb-3">Main Manuscript (PDF)</label>
                            <input type="file" accept="application/pdf" name="manuscript" required class="block w-full text-sm text-gray-500 dark:text-slate-500 file:mr-4 file:py-2.5 file:px-6 file:rounded-full file:border-0 file:text-xs file:font-black file:bg-blue-600 file:text-white hover:file:bg-blue-700 transition-all cursor-pointer">
                            <p class="text-[10px] text-gray-500 dark:text-slate-500 mt-3 font-semibold uppercase tracking-tight">PDF only &mdash; max {{ \App\Models\Setting::get('max_upload_size', '10') }} MB</p>
                            @error('manuscript') <div class="text-red-500 text-xs mt-2 font-bold whitespace-pre-wrap">{{ $message }}</div> @enderror
                        </div>

                        <div class="p-6 bg-gray-50 dark:bg-slate-950/50 rounded-2xl border-2 border-dashed border-gray-300 dark:border-white/5 group hover:border-indigo-500/50 transition-colors">
                            @php
                                $allowedExts = \App\Models\Setting::get('allowed_file_types', 'pdf,zip,doc,docx,ppt,pptx,xls,xlsx,mp4,avi,mov,sql,txt,csv,json,xml,jpg,jpeg,png,gif,md,rar,7z');
                                $acceptString = '.' . str_replace(',', ',.', str_replace(' ', '', $allowedExts));
                            @endphp

                            <label class="block font-bold text-xs uppercase tracking-widest text-blue-600 dark:text-indigo-400 mb-4">
                                Attachments
                                <span class="normal-case font-normal text-gray-500 dark:text-slate-500">({{ strtoupper(str_replace(',', ', ', $allowedExts)) }})</span>
                            </label>

                            {{-- Hidden real input — synced via JS DataTransfer --}}
                            <input type="file" id="attachments-real" name="attachments[]" multiple class="hidden"
                                   accept="{{ $acceptString }}">

                            {{-- Hidden picker (opened programmatically) --}}
                            <input type="file" id="attachment-picker" multiple class="hidden"
                                   accept="{{ $acceptString }}">

                            {{-- File queue list --}}
                            <div id="attachment-queue" class="space-y-2 mb-4 empty:hidden"></div>

                            {{-- Empty state --}}
                            <p id="attachment-empty" class="text-xs text-gray-500 dark:text-slate-600 italic mb-4">No files added yet.</p>

                            {{-- Add File button --}}
                            <button type="button" id="add-attachment-btn"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 dark:bg-indigo-600 hover:bg-blue-700 dark:hover:bg-indigo-700 text-white text-[10px] font-black uppercase tracking-widest transition-all shadow-md dark:shadow-lg dark:shadow-indigo-900/20">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/>
                                </svg>
                                Add File
                            </button>
                            <p class="text-[10px] text-gray-500 dark:text-slate-500 mt-4 font-semibold uppercase tracking-tight">Max {{ \App\Models\Setting::get('max_attachment_size', '50') }} MB per file.</p>

                            @error('attachments') <div class="text-red-500 text-xs mt-3 font-bold">{{ $message }}</div> @enderror
                            @foreach($errors->get('attachments.*') as $attachError)
                                @foreach($attachError as $msg)
                                    <div class="text-red-500 text-xs mt-1 font-bold">{{ $msg }}</div>
                                @endforeach
                            @endforeach
                        </div>
                    </div>




                    <div class="mb-10 p-6 bg-gray-50 dark:bg-slate-950 rounded-2xl border border-gray-200 dark:border-white/5 transition-colors">
                        <label class="inline-flex items-center cursor-pointer group">
                            <input type="checkbox" name="acknowledge_policy" required class="w-5 h-5 rounded border-gray-300 dark:border-white/10 bg-white dark:bg-slate-900 text-blue-500 dark:text-indigo-500 shadow-sm focus:ring-blue-500 dark:focus:ring-indigo-500 transition-all">
                            <span class="ml-4 text-sm text-gray-600 dark:text-slate-400 leading-relaxed group-hover:text-gray-900 dark:group-hover:text-white transition-colors font-medium">
                                I acknowledge that this submission is governed by university policies. I certify that all authors listed above have contributed to this project and that this submission is final.
                            </span>
                        </label>
                        @error('acknowledge_policy') <p class="text-red-500 text-xs mt-3 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <!-- Add Author JS -->
                    <script>
                        document.getElementById('add-author-btn').addEventListener('click', function() {
                            const container = document.getElementById('authors-container');
                            const row = document.createElement('div');
                            row.className = 'flex items-center gap-2 author-row';
                            row.innerHTML = `
                                <input type="text" name="authors[]" value="" placeholder="Full name of group member"
                                       class="flex-1 block w-full bg-gray-50 dark:bg-slate-950 border-gray-200 dark:border-white/10 rounded-xl text-gray-900 dark:text-white focus:border-blue-500 dark:focus:border-indigo-500 focus:ring-blue-500 dark:focus:ring-indigo-500 text-sm placeholder-gray-400 dark:placeholder-slate-600 transition-colors" autocomplete="off">
                                <button type="button" onclick="this.closest('.author-row').remove()"
                                        class="w-10 h-10 flex items-center justify-center rounded-xl bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-500 hover:bg-red-100 dark:hover:bg-red-900/40 transition-all flex-shrink-0 border border-red-200 dark:border-red-500/20"
                                        title="Remove">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>`;
                            container.appendChild(row);
                            row.querySelector('input').focus();
                        });
                    </script>
                    <!-- Submission Action -->
                    <div class="mt-12 pt-10 border-t border-gray-200 dark:border-white/5 mb-10 flex flex-col items-center">
                        <button id="submit-btn" type="submit"
                                class="inline-flex items-center justify-center gap-3 px-20 py-4 bg-blue-600 dark:bg-indigo-600 hover:bg-blue-700 dark:hover:bg-indigo-700 text-white font-bold rounded-full shadow-lg dark:shadow-2xl dark:shadow-indigo-900/20 transition-all transform hover:-translate-y-1 active:scale-95 text-sm uppercase tracking-widest whitespace-nowrap">
                            <span id="btn-text">Final Submission</span>
                            <svg id="btn-spinner" class="hidden animate-spin w-5 h-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                        </button>
                        <p class="text-center text-[10px] text-gray-500 dark:text-slate-500 mt-6 uppercase font-black tracking-[0.2em]">Your project will be reviewed by an admin</p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Spinner Keyframes -->
    <style>
        @keyframes overlay-spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        @keyframes overlay-spin-reverse {
            0% { transform: rotate(360deg); }
            100% { transform: rotate(0deg); }
        }
        @keyframes overlay-pulse-glow {
            0%, 100% { box-shadow: 0 0 0 0 rgba(99,102,241,0.25); }
            50% { box-shadow: 0 0 12px 4px rgba(99,102,241,0.35); }
        }
        @keyframes overlay-dot-pulse {
            0%, 80%, 100% { opacity: .3; transform: scale(.85); }
            40% { opacity: 1; transform: scale(1); }
        }
        .spinner-outer {
            animation: overlay-spin 1.8s linear infinite;
            will-change: transform;
            backface-visibility: hidden;
        }
        .spinner-inner {
            animation: overlay-spin-reverse 1.2s linear infinite;
            will-change: transform;
            backface-visibility: hidden;
        }
        .step-spinner-active {
            animation: overlay-spin .9s linear infinite;
            will-change: transform;
            backface-visibility: hidden;
        }
        .step-waiting-dot {
            animation: overlay-dot-pulse 1.6s ease-in-out infinite;
            will-change: opacity, transform;
            backface-visibility: hidden;
        }
        .step-active-glow {
            animation: overlay-pulse-glow 2s ease-in-out infinite;
            will-change: box-shadow;
            backface-visibility: hidden;
        }
    </style>

    <!-- Full-screen Loading Overlay -->
    <div id="loading-overlay"
         class="fixed inset-0 z-50 flex-col items-center justify-center bg-slate-950/90 backdrop-blur-md hidden"
         aria-live="polite">
        <div class="bg-slate-900 rounded-[2.5rem] shadow-2xl p-12 flex flex-col items-center gap-8 max-w-sm w-full mx-4 border border-white/10">

            <!-- Dual-ring Spinner (High-Performance CSS) -->
            <div class="relative w-32 h-32 flex items-center justify-center">
                <!-- Outer ring -->
                <div class="spinner-outer absolute inset-0 rounded-full border-[3px] border-white/5 border-t-indigo-500 border-r-indigo-500/40"></div>
                <!-- Inner ring -->
                <div class="spinner-inner absolute inset-4 rounded-full border-[3px] border-white/5 border-b-indigo-400 border-l-indigo-400/40"></div>
                
                <!-- Centre icon -->
                <div class="relative z-10">
                    <svg class="w-12 h-12 text-indigo-400 drop-shadow-[0_0_15px_rgba(99,102,241,0.5)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
            </div>

            <!-- Status Text -->
            <div class="text-center">
                <h3 class="text-2xl font-black text-white mb-2">Processing</h3>
                <p id="loading-status" class="text-sm text-indigo-400 font-bold tracking-widest uppercase">Uploading manuscript...</p>
            </div>

            <!-- Step indicators -->
            <div class="w-full space-y-3">
                <div id="step-upload" class="flex items-center gap-4 px-5 py-3 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 step-active-glow">
                    <div id="step-upload-spin" class="step-spinner-active w-5 h-5 rounded-full border-2 border-white/5 border-t-indigo-400 flex-shrink-0"></div>
                    <span class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.2em]">Uploading Files</span>
                </div>
                <div id="step-scan" class="flex items-center gap-4 px-5 py-3 rounded-2xl bg-white/[0.02] border border-white/5 transition-all duration-500">
                    <div id="step-scan-spin" class="w-4 h-4 rounded-full border-2 border-white/10 flex-shrink-0 step-waiting-dot"></div>
                    <span class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Security Scan</span>
                </div>
                <div id="step-validate" class="flex items-center gap-4 px-5 py-3 rounded-2xl bg-white/[0.02] border border-white/5 transition-all duration-500">
                    <div id="step-validate-spin" class="w-4 h-4 rounded-full border-2 border-white/10 flex-shrink-0 step-waiting-dot" style="animation-delay:.4s"></div>
                    <span class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">PDF Validation</span>
                </div>
            </div>

            <!-- Cancel button -->
            <button id="cancel-submission-btn" type="button"
                    onclick="cancelSubmission()"
                    class="text-[10px] text-slate-500 hover:text-red-400 font-black uppercase tracking-widest transition-colors duration-200 flex items-center gap-2 -mt-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Abort Upload
            </button>
            <p class="text-[9px] text-slate-600 text-center uppercase tracking-tighter leading-tight">Please stay on this page.<br>Uploading and scanning your files.</p>
        </div>
    </div>

    <script>
        // ── Attachment File Queue ────────────────────────────────────────────────
        const attachmentFiles = []; // master file list
        const picker    = document.getElementById('attachment-picker');
        const realInput = document.getElementById('attachments-real');
        const queueEl   = document.getElementById('attachment-queue');
        const emptyEl   = document.getElementById('attachment-empty');

        document.getElementById('add-attachment-btn').addEventListener('click', () => picker.click());

        picker.addEventListener('change', function () {
            for (const file of this.files) {
                // Skip duplicates by name+size
                const isDupe = attachmentFiles.some(f => f.name === file.name && f.size === file.size);
                if (!isDupe) attachmentFiles.push(file);
            }
            syncQueue();
            this.value = ''; // reset picker so same file can be re-added if removed
        });

        function syncQueue() {
            // Rebuild visual list
            queueEl.innerHTML = '';
            emptyEl.style.display = attachmentFiles.length ? 'none' : '';

            attachmentFiles.forEach((file, i) => {
                const ext = file.name.split('.').pop().toUpperCase();
                const size = file.size < 1048576
                    ? (file.size / 1024).toFixed(1) + ' KB'
                    : (file.size / 1048576).toFixed(1) + ' MB';

                const row = document.createElement('div');
                row.className = 'flex items-center gap-2 px-3 py-2 bg-slate-900 rounded-xl border border-white/5 shadow-sm text-xs';
                row.innerHTML = `
                    <span class="px-1.5 py-0.5 bg-indigo-900/30 text-indigo-400 rounded font-black text-[9px] flex-shrink-0 border border-indigo-500/20">${ext}</span>
                    <span class="flex-1 truncate text-slate-300 font-semibold">${file.name}</span>
                    <span class="text-slate-500 flex-shrink-0">${size}</span>
                    <button type="button" data-index="${i}"
                            class="remove-attachment ml-1 w-5 h-5 flex items-center justify-center rounded-full bg-red-900/20 hover:bg-red-900/40 text-red-500 flex-shrink-0 transition-all border border-red-500/20"
                            title="Remove">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>`;
                queueEl.appendChild(row);
            });

            // Sync files to hidden real input via DataTransfer
            const dt = new DataTransfer();
            attachmentFiles.forEach(f => dt.items.add(f));
            realInput.files = dt.files;
        }

        // Remove button handler (delegated)
        queueEl.addEventListener('click', function (e) {
            const btn = e.target.closest('.remove-attachment');
            if (!btn) return;
            attachmentFiles.splice(+btn.dataset.index, 1);
            syncQueue();
        });

        // Track timeouts so we can clear them on cancel
        let scanTimeoutId = null;
        let valTimeoutId = null;

        document.getElementById('project-form').addEventListener('submit', function () {
            // Show overlay
            const overlay = document.getElementById('loading-overlay');
            overlay.classList.remove('hidden');
            overlay.classList.add('flex');

            // Disable & transform the button
            const btn = document.getElementById('submit-btn');
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            document.getElementById('btn-text').textContent = 'Processing…';
            document.getElementById('btn-spinner').classList.remove('hidden');

            // Animate steps sequentially
            const status = document.getElementById('loading-status');

            scanTimeoutId = setTimeout(() => {
                // Step 1 done → Step 2 active
                const uploadStep = document.getElementById('step-upload');
                uploadStep.classList.replace('bg-indigo-500/10', 'bg-green-500/10');
                uploadStep.classList.replace('border-indigo-500/20', 'border-green-500/20');
                uploadStep.classList.remove('step-active-glow');
                // Replace spinner with a simple CSS checkmark
                document.getElementById('step-upload-spin').outerHTML = '<div class="w-5 h-5 flex items-center justify-center text-green-500 flex-shrink-0"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg></div>';

                const scanStep = document.getElementById('step-scan');
                scanStep.classList.replace('bg-white/[0.02]', 'bg-indigo-500/10');
                scanStep.classList.replace('border-white/5', 'border-indigo-500/20');
                scanStep.classList.add('step-active-glow');
                scanStep.querySelector('span').classList.replace('text-slate-500', 'text-indigo-400');
                // Use a CSS-only spinner
                document.getElementById('step-scan-spin').outerHTML = '<div id="step-scan-spin" class="step-spinner-active w-5 h-5 rounded-full border-2 border-white/5 border-t-indigo-400 flex-shrink-0"></div>';

                status.textContent = 'Running security scan…';
            }, 2500);

            valTimeoutId = setTimeout(() => {
                // Step 2 done → Step 3 active
                const scanStep = document.getElementById('step-scan');
                scanStep.classList.replace('bg-indigo-500/10', 'bg-green-500/10');
                scanStep.classList.replace('border-indigo-500/20', 'border-green-500/20');
                scanStep.classList.remove('step-active-glow');
                // Replace spinner with a simple CSS checkmark
                document.getElementById('step-scan-spin').outerHTML = '<div class="w-5 h-5 flex items-center justify-center text-green-500 flex-shrink-0"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg></div>';

                const valStep = document.getElementById('step-validate');
                valStep.classList.replace('bg-white/[0.02]', 'bg-indigo-500/10');
                valStep.classList.replace('border-white/5', 'border-indigo-500/20');
                valStep.classList.add('step-active-glow');
                valStep.querySelector('span').classList.replace('text-slate-500', 'text-indigo-400');
                // Use a CSS-only spinner
                document.getElementById('step-validate-spin').outerHTML = '<div id="step-validate-spin" class="step-spinner-active w-5 h-5 rounded-full border-2 border-white/5 border-t-indigo-400 flex-shrink-0"></div>';

                status.textContent = 'Validating PDF content…';
            }, 5000);
        });

        // ── Cancel Submission Handler ────────────────────────────────────────────
        function cancelSubmission() {
            // 1. Immediately clear the UI animation timeouts!
            if (scanTimeoutId) clearTimeout(scanTimeoutId);
            if (valTimeoutId) clearTimeout(valTimeoutId);

            // 2. Abort the in-flight POST request immediately
            window.stop();

            // 2. Fire-and-forget cleanup (don't wait for response)
            navigator.sendBeacon(
                '{{ route("projects.abort-submission") }}',
                new Blob([JSON.stringify({ _token: '{{ csrf_token() }}' })], { type: 'application/json' })
            );

            // 3. Close the modal immediately
            const overlay = document.getElementById('loading-overlay');
            overlay.classList.add('hidden');
            overlay.classList.remove('flex');

            // 4. Re-enable the submit button so they can try again
            const btn = document.getElementById('submit-btn');
            btn.disabled = false;
            btn.classList.remove('opacity-75', 'cursor-not-allowed');
            document.getElementById('btn-text').textContent = 'Submit';
            document.getElementById('btn-spinner').classList.add('hidden');

            // 5. Reset the step indicators for next attempt
            resetStepIndicators();
        }

        function resetStepIndicators() {
            // Reset upload step
            const uploadStep = document.getElementById('step-upload');
            uploadStep.className = 'flex items-center gap-4 px-5 py-3 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 step-active-glow';
            uploadStep.innerHTML = '<div id="step-upload-spin" class="step-spinner-active w-5 h-5 rounded-full border-2 border-white/5 border-t-indigo-400 flex-shrink-0"></div><span class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.2em]">Uploading Files</span>';

            // Reset scan step
            const scanStep = document.getElementById('step-scan');
            scanStep.className = 'flex items-center gap-4 px-5 py-3 rounded-2xl bg-white/[0.02] border border-white/5 transition-all duration-500';
            scanStep.innerHTML = '<div id="step-scan-spin" class="w-4 h-4 rounded-full border-2 border-white/10 flex-shrink-0 step-waiting-dot"></div><span class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Security Scan</span>';

            // Reset validate step
            const valStep = document.getElementById('step-validate');
            valStep.className = 'flex items-center gap-4 px-5 py-3 rounded-2xl bg-white/[0.02] border border-white/5 transition-all duration-500';
            valStep.innerHTML = '<div id="step-validate-spin" class="w-4 h-4 rounded-full border-2 border-white/10 flex-shrink-0 step-waiting-dot" style="animation-delay:.4s"></div><span class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">PDF Validation</span>';

            // Reset status text
            document.getElementById('loading-status').textContent = 'Uploading manuscript...';
            document.getElementById('loading-status').className = 'text-sm text-indigo-400 font-bold tracking-widest uppercase';
        }
    </script>
</x-app-layout>
