<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-white leading-tight">Edit Project Submission</h2>
            <a href="{{ route('student.home') }}" class="px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition ease-in-out duration-150">
                Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-3">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Show rejection feedback if project was returned --}}
            @if($project->status === 'rejected' && $project->rejection_reason)
                <div class="mb-6 bg-red-50 border-2 border-red-200 rounded-2xl p-6 shadow-sm">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-bold text-red-800 uppercase tracking-wider mb-1">Adviser's Feedback</h3>
                            <p class="text-red-700 text-sm font-medium mb-2">Your adviser returned this project with the following feedback:</p>
                            <div class="bg-white rounded-lg p-4 border border-red-200 text-gray-800 text-sm leading-relaxed whitespace-pre-wrap">{{ $project->rejection_reason }}</div>
                            <p class="text-xs text-red-500 mt-2 italic">Please address the feedback below and save your changes to resubmit.</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form id="edit-form" method="POST" action="{{ route('projects.update', $project) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Project Title</label>
                        <input type="text" name="title" value="{{ old('title', $project->title) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        @error('title') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Abstract</label>
                        <textarea name="abstract" rows="6" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('abstract', $project->abstract) }}</textarea>
                        @error('abstract') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Year</label>
                            <input type="number" name="year" value="{{ old('year', $project->year) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            @error('year') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block font-medium text-sm text-gray-700">Adviser</label>
                            <select name="adviser_id" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Select adviser</option>
                                @foreach($advisers as $adv)
                                    <option value="{{ $adv->id }}" {{ old('adviser_id', $project->adviser_id) == $adv->id ? 'selected' : '' }}>
                                        {{ $adv->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('adviser_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block font-medium text-sm text-gray-700">Program</label>
                            <select name="program" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="" disabled {{ (old('program', $project->program)) ? '' : 'selected' }}>Choose Program</option>
                                <option value="BSInT" {{ old('program', $project->program) == 'BSInT' ? 'selected' : '' }}>BSInT</option>
                                <option value="Com-Sci" {{ old('program', $project->program) == 'Com-Sci' ? 'selected' : '' }}>Com-Sci</option>
                            </select>
                            @error('program') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block font-bold text-xs uppercase tracking-widest text-gray-500 mb-2">Project Category</label>
                        <select name="specialization" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->name }}" {{ old('specialization', $project->specialization) == $category->name ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('specialization') <p class="text-red-600 text-sm mt-1 font-semibold">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block font-bold text-xs uppercase tracking-widest text-gray-500 mb-2">Keywords</label>
                        <input type="text" name="keywords" 
                               value="{{ old('keywords', is_array($project->keywords) ? implode(', ', $project->keywords) : $project->keywords) }}" 
                               class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="e.g., machine learning, web app, IoT">
                        <p class="text-[10px] text-gray-400 mt-1 italic">Separate keywords with commas.</p>
                        @error('keywords') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Authors (Editable) --}}
                    <div class="mb-6">
                        <label class="block font-bold text-xs uppercase tracking-widest text-gray-500 mb-2">Authors & Collaborators</label>

                        <div id="authors-container" class="space-y-2">
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
                                           class="flex-1 block w-full rounded-xl border-gray-300 {{ $authorName === $primaryAuthor ? 'bg-gray-100 cursor-not-allowed' : 'focus:border-indigo-500 focus:ring-indigo-500' }} shadow-sm text-sm">
                                    
                                    @if($authorName !== $primaryAuthor)
                                        <button type="button" onclick="this.closest('.author-row').remove()"
                                                class="w-8 h-8 flex items-center justify-center rounded-full bg-red-50 hover:bg-red-100 text-red-400 hover:text-red-600 transition-all flex-shrink-0"
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
                                class="mt-3 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-600 text-xs font-bold uppercase tracking-wider transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add Author
                        </button>

                        <p class="mt-2 text-[10px] text-gray-400 italic">Verify the full names of your research group members.</p>
                        @error('authors') <p class="text-red-600 text-sm mt-1 font-semibold">{{ $message }}</p> @enderror
                        @error('authors.*') <p class="text-red-600 text-sm mt-1 font-semibold">{{ $message }}</p> @enderror
                    </div>

                    {{-- Manuscript status (read-only) --}}
                    <div class="mb-6 p-4 bg-blue-50/50 rounded-xl border-2 border-dashed border-blue-200">
                        <label class="block font-bold text-xs uppercase tracking-widest text-blue-600 mb-2">Manuscript (PDF)</label>
                        @php $manuscript = $project->files->firstWhere('type', 'manuscript'); @endphp
                        @if($manuscript)
                            <div class="flex items-center gap-3 bg-white rounded-lg p-3 border border-blue-100">
                                <span class="text-xl">📄</span>
                                <div class="flex-1">
                                    <p class="text-sm font-bold text-gray-800">{{ $manuscript->filename }}</p>
                                    <p class="text-[10px] text-gray-500 uppercase font-black tracking-widest">
                                        {{ number_format($manuscript->size / 1048576, 2) }} MB
                                    </p>
                                </div>
                                <span class="bg-green-100 text-green-700 text-[9px] font-black px-2 py-1 rounded uppercase tracking-widest">Uploaded</span>
                            </div>
                        @endif
                        <p class="text-[10px] text-blue-400 mt-2 font-semibold">To replace the manuscript PDF, please cancel this submission and create a new one.</p>
                    </div>

                    {{-- Submit Area --}}
                    <div class="mt-10 pt-8 border-t border-gray-200">

                        @if($project->status === 'rejected')
                            {{-- Resubmission info card --}}
                            <div class="mb-6 rounded-2xl border border-amber-200 p-5" style="background: linear-gradient(to bottom right, #fffbeb, #fff7ed);">
                                <div class="flex items-start gap-4">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center shadow-sm">
                                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-amber-800 mb-1">Ready to resubmit?</h4>
                                        <p class="text-xs text-amber-700 leading-relaxed">
                                            When you click <strong>"Save & Resubmit"</strong>, the project status will reset to <strong>Processing</strong> 
                                            and your adviser will be notified via email to review the updated submission.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                            <a href="{{ route('student.home') }}" 
                               class="group inline-flex items-center gap-2 px-8 py-3.5 bg-white hover:bg-gray-50 text-gray-600 hover:text-gray-800 font-bold rounded-xl text-sm transition-all border-2 border-gray-200 hover:border-gray-300 shadow-sm hover:shadow">
                                <svg class="w-4 h-4 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

                        <p class="text-center text-[10px] text-gray-400 mt-5 uppercase font-bold tracking-widest flex items-center justify-center gap-2">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            {{ $project->status === 'rejected' ? 'Your adviser will be notified of the resubmission' : 'Changes will be saved immediately' }}
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
                       class="flex-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                <button type="button" onclick="this.closest('.author-row').remove()"
                        class="w-8 h-8 flex items-center justify-center rounded-full bg-red-50 hover:bg-red-100 text-red-400 hover:text-red-600 transition-all flex-shrink-0"
                        title="Remove">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>`;
            container.appendChild(row);
            row.querySelector('input').focus();
        });
    </script>
</x-app-layout>
