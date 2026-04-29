<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-white leading-tight">Edit Project Submission</h2>
            <a href="{{ route('student.home') }}" class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-800 dark:bg-gray-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-gray-700 dark:hover:bg-gray-600 transition-all shadow-sm hover:shadow-md border border-gray-700 dark:border-gray-600">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-3">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Show rejection feedback if project was returned --}}
            @if($project->status === 'returned' && $project->rejection_reason)
                <div class="mb-8 bg-red-900/10 border-2 border-red-500/20 rounded-3xl p-8 shadow-2xl">
                    <div class="flex items-start gap-6">
                        <div class="flex-shrink-0 w-14 h-14 bg-red-500/20 rounded-2xl flex items-center justify-center border border-red-500/30">
                            <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-black text-red-400 uppercase tracking-[0.2em] mb-2">Administrator Feedback</h3>
                            <p class="text-slate-300 text-sm font-medium mb-4 leading-relaxed">The Administrator returned this project with the following requirements:</p>
                            <div class="bg-slate-950/80 rounded-2xl p-5 border border-red-500/20 text-slate-200 text-sm leading-relaxed whitespace-pre-wrap font-medium shadow-inner italic">"{{ $project->rejection_reason }}"</div>
                            <p class="text-xs text-red-400/60 mt-4 font-bold uppercase tracking-widest">Resubmission Required</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-slate-900 overflow-hidden shadow-2xl sm:rounded-3xl p-8 border border-white/5">
                <form id="edit-form" method="POST" action="{{ route('projects.update', $project) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-6">
                        <label class="block font-bold text-xs uppercase tracking-widest text-slate-400 mb-2">Project Title</label>
                        <input type="text" name="title" value="{{ old('title', $project->title) }}" 
                               class="mt-1 block w-full bg-slate-950 border-white/10 rounded-xl text-white focus:border-indigo-500 focus:ring-indigo-500 placeholder-slate-600" 
                               required autocomplete="off">
                        @error('title') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block font-bold text-xs uppercase tracking-widest text-slate-400 mb-2">Abstract</label>
                        <textarea name="abstract" rows="8" 
                                  class="mt-1 block w-full bg-slate-950 border-white/10 rounded-xl text-white focus:border-indigo-500 focus:ring-indigo-500 placeholder-slate-600 leading-relaxed" 
                                  required>{{ old('abstract', $project->abstract) }}</textarea>
                        @error('abstract') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div>
                            <label class="block font-bold text-xs uppercase tracking-widest text-slate-400 mb-2">Year</label>
                            <input type="number" name="year" value="{{ old('year', $project->year) }}" 
                                   class="mt-1 block w-full bg-slate-950 border-white/10 rounded-xl text-white focus:border-indigo-500 focus:ring-indigo-500" required>
                            @error('year') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block font-bold text-xs uppercase tracking-widest text-slate-400 mb-2">Adviser</label>
                            <input type="text" name="adviser_name" value="{{ old('adviser_name', $project->adviser_name ?? $project->adviser->name ?? '') }}" 
                                   class="mt-1 block w-full bg-slate-950 border-white/10 rounded-xl text-white focus:border-indigo-500 focus:ring-indigo-500 placeholder-slate-600" 
                                   placeholder="Full name of your adviser" required autocomplete="off">
                            @error('adviser_name') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block font-bold text-xs uppercase tracking-widest text-slate-400 mb-2">Program</label>
                            <select name="program" class="mt-1 block w-full bg-slate-950 border-white/10 rounded-xl text-white focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="" disabled {{ (old('program', $project->program)) ? '' : 'selected' }} class="bg-slate-900">Choose Program</option>
                                @foreach($programs as $prog)
                                    <option value="{{ $prog->abbreviation }}" {{ old('program', $project->program) == $prog->abbreviation ? 'selected' : '' }} class="bg-slate-900">
                                        {{ $prog->abbreviation }}
                                    </option>
                                @endforeach
                            </select>
                            @error('program') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mb-10">
                        <label class="block font-bold text-xs uppercase tracking-widest text-slate-400 mb-4 text-center sm:text-left">Project Categories <span class="text-indigo-400 normal-case font-medium">(Select all that apply)</span></label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach($categories as $category)
                                <label class="relative flex items-center p-4 rounded-2xl border border-white/5 hover:border-indigo-500/50 hover:bg-white/[0.02] cursor-pointer transition-all group bg-slate-950/50">
                                    <input type="checkbox" name="categories[]" value="{{ $category->id }}" 
                                        {{ (is_array(old('categories')) && in_array($category->id, old('categories'))) || (!old('categories') && $project->categories->contains($category->id)) ? 'checked' : '' }}
                                        class="w-5 h-5 rounded-lg border-white/10 bg-slate-900 text-indigo-500 shadow-sm focus:ring-indigo-500 transition-all cursor-pointer">
                                    <span class="ml-3 text-sm font-bold text-slate-300 group-hover:text-white transition-colors">
                                        {{ $category->name }}
                                    </span>
                                </label>
                            @endforeach

                            {{-- "Others" Option --}}
                            <label class="relative flex items-center p-4 rounded-2xl border border-white/5 hover:border-indigo-500/50 hover:bg-white/[0.02] cursor-pointer transition-all group bg-slate-950/50">
                                <input type="checkbox" id="other_category_trigger" name="other_category_trigger" 
                                    {{ old('other_category') ? 'checked' : '' }}
                                    class="w-5 h-5 rounded-lg border-white/10 bg-slate-900 text-indigo-500 shadow-sm focus:ring-indigo-500 transition-all cursor-pointer">
                                <span class="ml-3 text-sm font-bold text-slate-300 group-hover:text-white transition-colors">
                                    Others
                                </span>
                            </label>
                        </div>

                        <div id="other_category_container" class="{{ old('other_category') ? '' : 'hidden' }} mt-4">
                            <input type="text" name="other_category" value="{{ old('other_category') }}" 
                                placeholder="Specify other category name"
                                class="w-full sm:w-1/2 bg-slate-950 border-white/10 rounded-xl text-white focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-3 font-bold placeholder:font-normal">
                            <p class="text-[10px] text-indigo-400 mt-2 italic ml-1">This will be added as a new specialization for your project.</p>
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

                    <div class="mb-8">
                        <label class="block font-bold text-xs uppercase tracking-widest text-slate-400 mb-2">Keywords</label>
                        <input type="text" name="keywords" 
                               value="{{ old('keywords', is_array($project->keywords) ? implode(', ', $project->keywords) : $project->keywords) }}" 
                               class="mt-1 block w-full bg-slate-950 border-white/10 rounded-xl text-white focus:border-indigo-500 focus:ring-indigo-500 placeholder-slate-600"
                               placeholder="e.g., machine learning, web app, IoT" autocomplete="off">
                        <p class="text-[10px] text-slate-500 mt-2 italic">Separate keywords with commas.</p>
                        @error('keywords') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                    </div>

                    {{-- Authors (Editable) --}}
                    <div class="mb-10">
                        <label class="block font-bold text-xs uppercase tracking-widest text-slate-400 mb-4">Authors & Collaborators</label>

                        <div id="authors-container" class="space-y-3">
                            @php
                                $authors = old('authors');
                                if (!$authors) {
                                    $authors = array_map('trim', explode(',', $project->authors_list));
                                }
                                $primaryAuthor = auth()->user()->name;
                            @endphp

                            @foreach($authors as $index => $authorName)
                                <div class="flex items-center gap-2 {{ $authorName === $primaryAuthor ? '' : 'author-row' }}">
                                    <input type="text" name="authors[]" value="{{ $authorName }}" 
                                           {{ $authorName === $primaryAuthor ? 'readonly' : '' }}
                                           placeholder="Full name of group member"
                                           class="flex-1 block w-full bg-slate-950 border-white/10 rounded-xl text-white {{ $authorName === $primaryAuthor ? 'opacity-50 cursor-not-allowed' : 'focus:border-indigo-500 focus:ring-indigo-500' }} shadow-sm text-sm" autocomplete="off">
                                    
                                    @if($authorName !== $primaryAuthor)
                                        <button type="button" onclick="this.closest('.author-row').remove()"
                                                class="w-10 h-10 flex items-center justify-center rounded-xl bg-red-900/20 text-red-500 hover:bg-red-900/40 transition-all flex-shrink-0 border border-red-500/20"
                                                title="Remove">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        {{-- Add Author button --}}
                        <button type="button" id="add-author-btn"
                                class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-900/20 hover:bg-indigo-900/40 text-indigo-400 text-[10px] font-black uppercase tracking-widest transition-all border border-indigo-500/20">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add Author
                        </button>

                        <p class="mt-3 text-[10px] text-slate-500 italic">Verify the full names of your research group members.</p>
                        @error('authors') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                        @error('authors.*') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                    </div>

                    {{-- Manuscript Replacement --}}
                    <div class="mb-10 p-6 bg-slate-950 rounded-2xl border-2 border-dashed border-white/5">
                        <label class="block font-bold text-xs uppercase tracking-widest text-blue-400 mb-4">Manuscript (PDF)</label>
                        
                        @php $manuscript = $project->files->firstWhere('type', 'manuscript'); @endphp
                        
                        {{-- Current File View --}}
                        <div id="current-manuscript-container" class="{{ $manuscript ? '' : 'hidden' }}">
                            @if($manuscript)
                                <div class="flex items-center gap-4 bg-slate-900 rounded-2xl p-4 border border-white/5 shadow-inner mb-6">
                                    <span class="text-2xl">📄</span>
                                    <div class="flex-1">
                                        <p class="text-[10px] text-slate-500 uppercase font-black tracking-widest mb-1">Current Version</p>
                                        <p class="text-sm font-bold text-white leading-tight mb-1">{{ $manuscript->filename }}</p>
                                        <p class="text-[9px] text-slate-400 font-medium">
                                            {{ number_format($manuscript->size / 1048576, 2) }} MB • Uploaded {{ $manuscript->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    <button type="button" onclick="removeCurrentManuscript()" 
                                            class="p-2 bg-red-500/10 text-red-500 rounded-xl border border-red-500/20 hover:bg-red-500/20 transition-all"
                                            title="Remove and Replace">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            @endif
                        </div>

                        {{-- Replacement State --}}
                        <div id="manuscript-upload-container" class="space-y-4">
                            <div id="replacement-warning" class="hidden mb-4 p-3 bg-amber-500/10 border border-amber-500/20 rounded-xl">
                                <p class="text-[10px] font-black text-amber-500 uppercase tracking-widest flex items-center gap-2">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                    New manuscript file required
                                </p>
                            </div>

                            <label class="block text-sm font-bold text-slate-300">
                                <span id="upload-label-text">{{ $manuscript ? 'Replace Manuscript (Optional)' : 'Upload Manuscript' }}</span>
                            </label>
                            
                            <input type="file" id="manuscript_input" name="manuscript" accept=".pdf"
                                   class="block w-full text-sm text-slate-400
                                          file:mr-4 file:py-2 file:px-4
                                          file:rounded-xl file:border-0
                                          file:text-[10px] file:font-black file:uppercase file:tracking-widest
                                          file:bg-indigo-600 file:text-white
                                          hover:file:bg-indigo-700 transition-all cursor-pointer">
                            <p class="text-[10px] text-slate-500 italic">Select a new PDF file to update your submission. Maximum size: 50MB.</p>
                        </div>
                        @error('manuscript') <p class="text-red-500 text-xs mt-2 font-bold">{{ $message }}</p> @enderror
                    </div>

                    {{-- Attachments Section --}}
                    <div class="mb-10 p-6 bg-slate-950 rounded-2xl border-2 border-dashed border-white/5">
                        @php
                            $allowedExts = \App\Models\Setting::get('allowed_file_types', 'pdf,zip,doc,docx,ppt,pptx,xls,xlsx,mp4,avi,mov,sql,txt,csv,json,xml,jpg,jpeg,png,gif,md,rar,7z');
                            $acceptString = '.' . str_replace(',', ',.', str_replace(' ', '', $allowedExts));
                            $currentAttachments = $project->files->where('type', 'attachment');
                        @endphp

                        <label class="block font-bold text-xs uppercase tracking-widest text-indigo-400 mb-4">
                            Attachments
                            <span class="normal-case font-normal text-slate-500">({{ strtoupper(str_replace(',', ', ', $allowedExts)) }})</span>
                        </label>

                        {{-- Current Attachments List --}}
                        @if($currentAttachments->count() > 0)
                            <div class="space-y-2 mb-6">
                                <p class="text-[10px] text-slate-500 uppercase font-black tracking-widest mb-2 ml-1">Existing Attachments</p>
                                @foreach($currentAttachments as $file)
                                    <div class="flex items-center gap-3 px-3 py-2 bg-slate-900 rounded-xl border border-white/5 shadow-sm text-xs group">
                                        <span class="px-1.5 py-0.5 bg-slate-800 text-slate-400 rounded font-black text-[9px] uppercase border border-white/5">
                                            {{ pathinfo($file->filename, PATHINFO_EXTENSION) }}
                                        </span>
                                        <span class="flex-1 truncate text-slate-400 font-medium">{{ $file->filename }}</span>
                                        <span class="text-slate-600 text-[10px]">{{ number_format($file->size / 1024, 1) }} KB</span>
                                        {{-- We don't allow removing individual existing attachments here for now to keep it simple, 
                                             but the student can add NEW ones below. --}}
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- New Attachments Picker --}}
                        <div id="new-attachments-container" class="space-y-4">
                            <p class="text-[10px] text-indigo-400 uppercase font-black tracking-widest mb-2 ml-1">Add New Attachments</p>
                            
                            {{-- Hidden real input --}}
                            <input type="file" id="attachments-real" name="attachments[]" multiple class="hidden" accept="{{ $acceptString }}">
                            {{-- Hidden picker --}}
                            <input type="file" id="attachment-picker" multiple class="hidden" accept="{{ $acceptString }}">

                            <div id="attachment-queue" class="space-y-2 mb-4 empty:hidden"></div>
                            <p id="attachment-empty" class="text-xs text-slate-600 italic mb-4">No new files added.</p>

                            <button type="button" id="add-attachment-btn"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600/20 hover:bg-indigo-600/40 text-indigo-400 text-[10px] font-black uppercase tracking-widest transition-all border border-indigo-500/20">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/>
                                </svg>
                                Add More Files
                            </button>
                        </div>
                    </div>
                    @error('attachments') <p class="text-red-500 text-xs mt-2 font-bold ml-1 italic">{{ $message }}</p> @enderror

                    <script>
                        function removeCurrentManuscript() {
                            if(confirm('Remove current manuscript? You will need to upload a new PDF to save your changes.')) {
                                document.getElementById('current-manuscript-container').classList.add('hidden');
                                document.getElementById('replacement-warning').classList.remove('hidden');
                                document.getElementById('manuscript_input').required = true;
                                document.getElementById('upload-label-text').textContent = 'Upload New Manuscript';
                                document.getElementById('manuscript_input').click();
                            }
                        }
                    </script>

                    {{-- Submit Area --}}
                    <div class="mt-10 pt-8 border-t border-gray-200">

                        @if($project->status === 'returned')
                            {{-- Resubmission info card --}}
                            <div class="mb-8 rounded-[2rem] border border-amber-500/20 p-8 bg-amber-900/10 shadow-2xl">
                                <div class="flex items-start gap-6">
                                    <div class="flex-shrink-0 w-12 h-12 rounded-2xl bg-amber-500/20 flex items-center justify-center border border-amber-500/30">
                                        <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-black text-amber-400 uppercase tracking-widest mb-2">Ready to resubmit?</h4>
                                        <p class="text-sm text-slate-300 leading-relaxed font-medium">
                                            When you click <strong>"Save & Resubmit"</strong>, the project status will reset to <strong>Processing</strong> 
                                            and your adviser will be notified to perform a fresh verification of your updates.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                            <a href="{{ route('student.home') }}" 
                               class="group inline-flex items-center gap-3 px-10 py-4 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-2xl text-sm transition-all border border-white/5 shadow-lg">
                                <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Cancel
                            </a>

                            @if($project->status === 'returned')
                                <button id="submit-btn" type="submit"
                                        class="group relative inline-flex items-center justify-center gap-3 px-20 py-4 text-white font-bold rounded-full shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-0.5 active:translate-y-0 text-sm uppercase tracking-widest whitespace-nowrap overflow-hidden"
                                        style="background: linear-gradient(to right, #f59e0b, #d97706, #ea580c); box-shadow: 0 10px 15px -3px rgba(245,158,11,0.3);"
                                        onmouseover="this.style.background='linear-gradient(to right, #d97706, #b45309, #c2410c)'; this.style.boxShadow='0 20px 25px -5px rgba(245,158,11,0.4)';"
                                        onmouseout="this.style.background='linear-gradient(to right, #f59e0b, #d97706, #ea580c)'; this.style.boxShadow='0 10px 15px -3px rgba(245,158,11,0.3)';">
                                    {{-- Shimmer effect --}}
                                    <span class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700 ease-in-out"></span>
                                    
                                    <span id="btn-icon" class="relative">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                    </span>
                                    <span id="btn-spinner" class="hidden relative">
                                        <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                        </svg>
                                    </span>
                                    <span id="btn-text" class="relative">Save & Resubmit</span>
                                </button>
                            @else
                                <button id="submit-btn" type="submit"
                                        class="group relative inline-flex items-center justify-center gap-3 px-20 py-4 text-white font-bold rounded-full shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-0.5 active:translate-y-0 text-sm uppercase tracking-widest whitespace-nowrap overflow-hidden"
                                        style="background: linear-gradient(to right, #6366f1, #4f46e5, #2563eb); box-shadow: 0 10px 15px -3px rgba(99,102,241,0.3);"
                                        onmouseover="this.style.background='linear-gradient(to right, #4f46e5, #4338ca, #1d4ed8)'; this.style.boxShadow='0 20px 25px -5px rgba(99,102,241,0.4)';"
                                        onmouseout="this.style.background='linear-gradient(to right, #6366f1, #4f46e5, #2563eb)'; this.style.boxShadow='0 10px 15px -3px rgba(99,102,241,0.3)';">
                                    {{-- Shimmer effect --}}
                                    <span class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700 ease-in-out"></span>

                                    <span id="btn-icon" class="relative">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </span>
                                    <span id="btn-spinner" class="hidden relative">
                                        <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                        </svg>
                                    </span>
                                    <span id="btn-text" class="relative">Save Changes</span>
                                </button>
                            @endif
                        </div>

                        <p class="text-center text-[10px] text-slate-500 mt-8 uppercase font-black tracking-[0.2em] flex items-center justify-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            {{ $project->status === 'returned' ? 'Resubmit your project' : 'Update project details' }}
                        </p>
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
                <h3 class="text-2xl font-black text-white mb-2">Resubmitting</h3>
                <p id="loading-status" class="text-sm text-indigo-400 font-bold tracking-widest uppercase">Uploading manuscript...</p>
            </div>

            <!-- Step indicators -->
            <div class="w-full space-y-3">
                <div id="step-upload" class="flex items-center gap-4 px-5 py-3 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 step-active-glow">
                    <div id="step-upload-spin" class="step-spinner-active w-5 h-5 rounded-full border-2 border-white/5 border-t-indigo-400 flex-shrink-0"></div>
                    <span class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.2em]">Updating Metadata</span>
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
                Abort Update
            </button>
            <p class="text-[9px] text-slate-600 text-center uppercase tracking-tighter leading-tight">Resubmitting your project.<br>Verifying your changes.</p>
        </div>
    </div>

    <script>
        // If the page reloads with errors, make sure overlay is hidden (default)
        // and reset button states. 
        document.addEventListener('DOMContentLoaded', () => {
            const overlay = document.getElementById('loading-overlay');
            if (overlay) {
                overlay.classList.add('hidden');
                overlay.classList.remove('flex');
            }
        });

        // ── Attachment File Queue ────────────────────────────────────────────────
        const attachmentFiles = []; // master file list
        const picker    = document.getElementById('attachment-picker');
        const realInput = document.getElementById('attachments-real');
        const queueEl   = document.getElementById('attachment-queue');
        const emptyEl   = document.getElementById('attachment-empty');

        if(document.getElementById('add-attachment-btn')) {
            document.getElementById('add-attachment-btn').addEventListener('click', () => picker.click());
        }

        if(picker) {
            picker.addEventListener('change', function () {
                for (const file of this.files) {
                    const isDupe = attachmentFiles.some(f => f.name === file.name && f.size === file.size);
                    if (!isDupe) attachmentFiles.push(file);
                }
                syncQueue();
                this.value = '';
            });
        }

        function syncQueue() {
            if(!queueEl) return;
            queueEl.innerHTML = '';
            if(emptyEl) emptyEl.style.display = attachmentFiles.length ? 'none' : '';

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

            if(realInput) {
                const dt = new DataTransfer();
                attachmentFiles.forEach(f => dt.items.add(f));
                realInput.files = dt.files;
            }
        }

        if(queueEl) {
            queueEl.addEventListener('click', function (e) {
                const btn = e.target.closest('.remove-attachment');
                if (!btn) return;
                attachmentFiles.splice(+btn.dataset.index, 1);
                syncQueue();
            });
        }

        // Track timeouts so we can clear them on cancel
        let scanTimeoutId = null;
        let valTimeoutId = null;

        document.getElementById('edit-form').addEventListener('submit', function() {
            // Show overlay
            const overlay = document.getElementById('loading-overlay');
            overlay.classList.remove('hidden');
            overlay.classList.add('flex');

            // Disable & transform the button
            const btn = document.getElementById('submit-btn');
            btn.disabled = true;
            btn.classList.add('opacity-80', 'cursor-not-allowed', 'pointer-events-none');
            btn.classList.remove('hover:-translate-y-0.5');
            
            const icon = document.getElementById('btn-icon');
            const spinner = document.getElementById('btn-spinner');
            const text = document.getElementById('btn-text');
            
            if(icon) icon.classList.add('hidden');
            if(spinner) spinner.classList.remove('hidden');
            if(text) text.textContent = 'Saving…';

            const status = document.getElementById('loading-status');
            const manuscriptInput = document.getElementById('manuscript_input');
            
            const hasNewManuscript = manuscriptInput && manuscriptInput.files.length > 0;
            const hasNewAttachments = attachmentFiles.length > 0;
            
            // Skip scanning animations if no new file is being uploaded
            if (!hasNewManuscript && !hasNewAttachments) {
                status.textContent = 'Saving changes...';
                // Finish quickly
                setTimeout(() => {
                    const uploadStep = document.getElementById('step-upload');
                    if (uploadStep) {
                        uploadStep.classList.replace('bg-indigo-500/10', 'bg-green-500/10');
                        uploadStep.classList.replace('border-indigo-500/20', 'border-green-500/20');
                        const spin = document.getElementById('step-upload-spin');
                        if (spin) spin.outerHTML = '<div class="w-5 h-5 flex items-center justify-center text-green-500 flex-shrink-0"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg></div>';
                    }
                    
                    // Hide scan/validate steps as they aren't happening
                    document.getElementById('step-scan').classList.add('hidden');
                    document.getElementById('step-validate').classList.add('hidden');
                }, 800);
                return;
            }

            scanTimeoutId = setTimeout(() => {
                // Step 1 done → Step 2 active
                const uploadStep = document.getElementById('step-upload');
                uploadStep.classList.replace('bg-indigo-500/10', 'bg-green-500/10');
                uploadStep.classList.replace('border-indigo-500/20', 'border-green-500/20');
                uploadStep.classList.remove('step-active-glow');
                document.getElementById('step-upload-spin').outerHTML = '<div class="w-5 h-5 flex items-center justify-center text-green-500 flex-shrink-0"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg></div>';

                const scanStep = document.getElementById('step-scan');
                scanStep.classList.replace('bg-white/[0.02]', 'bg-indigo-500/10');
                scanStep.classList.replace('border-white/5', 'border-indigo-500/20');
                scanStep.classList.add('step-active-glow');
                scanStep.querySelector('span').classList.replace('text-slate-500', 'text-indigo-400');
                document.getElementById('step-scan-spin').outerHTML = '<div id="step-scan-spin" class="step-spinner-active w-5 h-5 rounded-full border-2 border-white/5 border-t-indigo-400 flex-shrink-0"></div>';

                status.textContent = 'Running security scan…';
            }, 2500);

            valTimeoutId = setTimeout(() => {
                // Step 2 done → Step 3 active
                const scanStep = document.getElementById('step-scan');
                scanStep.classList.replace('bg-indigo-500/10', 'bg-green-500/10');
                scanStep.classList.replace('border-indigo-500/20', 'border-green-500/20');
                scanStep.classList.remove('step-active-glow');
                document.getElementById('step-scan-spin').outerHTML = '<div class="w-5 h-5 flex items-center justify-center text-green-500 flex-shrink-0"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg></div>';

                const valStep = document.getElementById('step-validate');
                valStep.classList.replace('bg-white/[0.02]', 'bg-indigo-500/10');
                valStep.classList.replace('border-white/5', 'border-indigo-500/20');
                valStep.classList.add('step-active-glow');
                valStep.querySelector('span').classList.replace('text-slate-500', 'text-indigo-400');
                document.getElementById('step-validate-spin').outerHTML = '<div id="step-validate-spin" class="step-spinner-active w-5 h-5 rounded-full border-2 border-white/5 border-t-indigo-400 flex-shrink-0"></div>';

                status.textContent = 'Validating PDF content…';
            }, 5000);
        });

        function cancelSubmission() {
            if (scanTimeoutId) clearTimeout(scanTimeoutId);
            if (valTimeoutId) clearTimeout(valTimeoutId);
            window.stop();

            // Fire-and-forget cleanup
            navigator.sendBeacon(
                '{{ route("projects.abort-submission") }}',
                new Blob([JSON.stringify({ _token: '{{ csrf_token() }}' })], { type: 'application/json' })
            );

            const overlay = document.getElementById('loading-overlay');
            overlay.classList.add('hidden');
            overlay.classList.remove('flex');

            const btn = document.getElementById('submit-btn');
            btn.disabled = false;
            btn.classList.remove('opacity-80', 'cursor-not-allowed', 'pointer-events-none');
            
            const icon = document.getElementById('btn-icon');
            const spinner = document.getElementById('btn-spinner');
            const text = document.getElementById('btn-text');
            
            if(icon) icon.classList.remove('hidden');
            if(spinner) spinner.classList.add('hidden');
            if(text) text.textContent = 'Save Changes';

            resetStepIndicators();
        }

        function resetStepIndicators() {
            const uploadStep = document.getElementById('step-upload');
            uploadStep.className = 'flex items-center gap-4 px-5 py-3 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 step-active-glow';
            uploadStep.innerHTML = '<div id="step-upload-spin" class="step-spinner-active w-5 h-5 rounded-full border-2 border-white/5 border-t-indigo-400 flex-shrink-0"></div><span class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.2em]">Updating Metadata</span>';

            const scanStep = document.getElementById('step-scan');
            scanStep.classList.remove('hidden');
            scanStep.className = 'flex items-center gap-4 px-5 py-3 rounded-2xl bg-white/[0.02] border border-white/5 transition-all duration-500';
            scanStep.innerHTML = '<div id="step-scan-spin" class="w-4 h-4 rounded-full border-2 border-white/10 flex-shrink-0 step-waiting-dot"></div><span class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Security Scan</span>';

            const valStep = document.getElementById('step-validate');
            valStep.classList.remove('hidden');
            valStep.className = 'flex items-center gap-4 px-5 py-3 rounded-2xl bg-white/[0.02] border border-white/5 transition-all duration-500';
            valStep.innerHTML = '<div id="step-validate-spin" class="w-4 h-4 rounded-full border-2 border-white/10 flex-shrink-0 step-waiting-dot" style="animation-delay:.4s"></div><span class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">PDF Validation</span>';

            document.getElementById('loading-status').textContent = 'Uploading manuscript...';
        }

        // Add Author JS
        document.getElementById('add-author-btn').addEventListener('click', function() {
            const container = document.getElementById('authors-container');
            const row = document.createElement('div');
            row.className = 'flex items-center gap-2 author-row';
            row.innerHTML = `
                <input type="text" name="authors[]" value="" placeholder="Full name of group member"
                       class="flex-1 block w-full bg-slate-950 border-white/10 rounded-xl text-white focus:border-indigo-500 focus:ring-indigo-500 text-sm" autocomplete="off">
                <button type="button" onclick="this.closest('.author-row').remove()"
                        class="w-10 h-10 flex items-center justify-center rounded-xl bg-red-900/20 text-red-500 hover:bg-red-900/40 transition-all flex-shrink-0 border border-red-500/20"
                        title="Remove">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>`;
            container.appendChild(row);
            row.querySelector('input').focus();
        });
    </script>
</x-app-layout>
