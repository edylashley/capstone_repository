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
            @if($project->status === 'rejected' && $project->rejection_reason)
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
                               required>
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
                            <label class="block font-bold text-xs uppercase tracking-widest text-slate-400 mb-2">Technical Adviser</label>
                            <input type="text" name="adviser_name" value="{{ old('adviser_name', $project->adviser_name ?? $project->adviser->name ?? '') }}" 
                                   class="mt-1 block w-full bg-slate-950 border-white/10 rounded-xl text-white focus:border-indigo-500 focus:ring-indigo-500 placeholder-slate-600" 
                                   placeholder="Full name of your adviser" required>
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
                               placeholder="e.g., machine learning, web app, IoT">
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
                                           class="flex-1 block w-full bg-slate-950 border-white/10 rounded-xl text-white {{ $authorName === $primaryAuthor ? 'opacity-50 cursor-not-allowed' : 'focus:border-indigo-500 focus:ring-indigo-500' }} shadow-sm text-sm">
                                    
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

                        @if($project->status === 'rejected')
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

                            @if($project->status === 'rejected')
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
                            {{ $project->status === 'rejected' ? 'Institutional Resubmission Protocol' : 'Verified metadata update' }}
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('edit-form').addEventListener('submit', function() {
            const btn = document.getElementById('submit-btn');
            const icon = document.getElementById('btn-icon');
            const spinner = document.getElementById('btn-spinner');
            const text = document.getElementById('btn-text');

            btn.disabled = true;
            btn.classList.add('opacity-80', 'cursor-not-allowed', 'pointer-events-none');
            btn.classList.remove('hover:-translate-y-0.5');
            icon.classList.add('hidden');
            spinner.classList.remove('hidden');
            text.textContent = 'Saving…';
        });

        // Add Author JS
        document.getElementById('add-author-btn').addEventListener('click', function() {
            const container = document.getElementById('authors-container');
            const row = document.createElement('div');
            row.className = 'flex items-center gap-2 author-row';
            row.innerHTML = `
                <input type="text" name="authors[]" value="" placeholder="Full name of group member"
                       class="flex-1 block w-full bg-slate-950 border-white/10 rounded-xl text-white focus:border-indigo-500 focus:ring-indigo-500 text-sm">
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
