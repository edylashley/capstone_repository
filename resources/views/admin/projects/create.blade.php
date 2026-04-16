<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-2xl text-white leading-tight">Admin Direct Entry</h2>
                <p class="text-xs text-indigo-200 mt-1 uppercase tracking-widest font-black">Instantly publish a legacy capstone project</p>
            </div>
            <a href="{{ route('admin.projects.index') }}" class="px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition ease-in-out duration-150">
                Back to List
            </a>
        </div>
    </x-slot>

    <div class="py-3">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form id="project-form" method="POST" action="{{ route('admin.projects.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Project Title</label>
                        <input type="text" name="title" value="{{ old('title') }}" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        @error('title') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Authors (Comma-separated)</label>
                        <input type="text" name="authors_list" value="{{ old('authors_list') }}" placeholder="e.g. John Doe, Jane Smith" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <p class="mt-1 text-[10px] text-gray-400 italic">Enter the full names of the alumni authors separated by commas.</p>
                        @error('authors_list') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Abstract (Optional)</label>
                        <textarea name="abstract" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="3">{{ old('abstract') }}</textarea>
                        @error('abstract') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Year Defended</label>
                            <input type="number" name="year" value="{{ old('year', date('Y')) }}" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            @error('year') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                        </div>

                        <div class="col-span-1 md:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Adviser</label>
                                <select id="adviser_id_select" name="adviser_id" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">-- Not listed / Left university --</option>
                                    @foreach($advisers as $adv)
                                        <option value="{{ $adv->id }}" {{ old('adviser_id') == $adv->id ? 'selected' : '' }}>{{ $adv->name }}</option>
                                    @endforeach
                                </select>
                                @error('adviser_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                            </div>
                            <div id="other-adviser-container" class="{{ old('adviser_id') ? 'hidden' : '' }}">
                                <label class="block font-medium text-sm text-gray-700">Other</label>
                                <input type="text" name="adviser_name" value="{{ old('adviser_name') }}" placeholder="e.g. Dr. Alan Turing" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <p class="mt-1 text-[10px] text-gray-400 italic">Only fill this if they are not in the list.</p>
                                @error('adviser_name') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block font-medium text-sm text-gray-700">Project Category</label>
                            <select name="specialization" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->name }}" {{ old('specialization') == $category->name ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('specialization') <p class="text-red-600 text-sm mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block font-medium text-sm text-gray-700">Program</label>
                        <select name="program" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="" disabled {{ old('program') ? '' : 'selected' }}>Choose Program</option>
                            <option value="BSInT" {{ old('program') == 'BSInT' ? 'selected' : '' }}>BSInT</option>
                            <option value="Com-Sci" {{ old('program') == 'Com-Sci' ? 'selected' : '' }}>Com-Sci</option>
                        </select>
                        @error('program') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="p-4 bg-blue-50/50 rounded-xl border-2 border-dashed border-blue-200">
                            <label class="block font-bold text-xs uppercase tracking-widest text-blue-600 mb-2">Main Manuscript (PDF)</label>
                            <input type="file" accept="application/pdf" name="manuscript" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-black file:bg-blue-600 file:text-white hover:file:bg-blue-700 transition-all cursor-pointer">
                            <p class="text-[10px] text-blue-400 mt-1.5 font-semibold">PDF only &mdash; max 50 MB</p>
                            @error('manuscript') <div class="text-red-600 text-sm mt-1 font-semibold whitespace-pre-wrap">{{ $message }}</div> @enderror
                        </div>

                        <div class="p-4 bg-indigo-50/50 rounded-xl border-2 border-dashed border-indigo-200">
                            <label class="block font-bold text-xs uppercase tracking-widest text-indigo-600 mb-3">
                                Attachments
                                <span class="normal-case font-normal text-gray-400">(ZIP, RAR, MP4, PDF, Word, Excel, PowerPoint, SQL, Images, CSV, JSON &amp; more)</span>
                            </label>

                            {{-- Hidden real input — synced via JS DataTransfer --}}
                            <input type="file" id="attachments-real" name="attachments[]" multiple class="hidden"
                                   accept=".zip,.rar,.7z,.pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.mp4,.avi,.mov,.sql,.txt,.csv,.json,.xml,.jpg,.jpeg,.png,.gif,.md">

                            {{-- Hidden picker (opened programmatically) --}}
                            <input type="file" id="attachment-picker" multiple class="hidden"
                                   accept=".zip,.rar,.7z,.pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.mp4,.avi,.mov,.sql,.txt,.csv,.json,.xml,.jpg,.jpeg,.png,.gif,.md">

                            {{-- File queue list --}}
                            <div id="attachment-queue" class="space-y-2 mb-3 empty:hidden"></div>

                            {{-- Empty state --}}
                            <p id="attachment-empty" class="text-xs text-indigo-300 italic mb-3">No files added yet.</p>

                            {{-- Add File button --}}
                            <button type="button" id="add-attachment-btn"
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black uppercase tracking-wider transition-all shadow-sm hover:shadow-indigo-200">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/>
                                </svg>
                                Add File
                            </button>
                            <p class="text-[10px] text-indigo-400 mt-2 font-semibold">Max 200 MB per file. Click <strong>Add File</strong> again to add more.</p>

                            @error('attachments') <div class="text-red-600 text-sm mt-2 font-semibold">{{ $message }}</div> @enderror
                            @foreach($errors->get('attachments.*') as $attachError)
                                @foreach($attachError as $msg)
                                    <div class="text-red-600 text-sm mt-1 font-semibold">{{ $msg }}</div>
                                @endforeach
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-8 pt-8 border-t border-gray-100 flex items-center justify-end gap-4">
                        <a href="{{ route('admin.projects.index') }}" class="text-sm font-bold text-gray-500 hover:text-gray-800">Cancel</a>
                        <button id="submit-btn" type="submit"
                                class="inline-flex items-center gap-2 px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-sm uppercase tracking-widest rounded-xl shadow-lg hover:shadow-indigo-500/30 transition transform hover:-translate-y-0.5">
                            <span id="btn-text">Submit & Publish Record</span>
                        </button>
                    </div>
                </form>
            </div>
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
                // Skip duplicates
                const isDupe = attachmentFiles.some(f => f.name === file.name && f.size === file.size);
                if (!isDupe) attachmentFiles.push(file);
            }
            syncQueue();
            this.value = '';
        });

        function syncQueue() {
            queueEl.innerHTML = '';
            emptyEl.style.display = attachmentFiles.length ? 'none' : '';

            attachmentFiles.forEach((file, i) => {
                const ext = file.name.split('.').pop().toUpperCase();
                const size = file.size < 1048576 ? (file.size / 1024).toFixed(1) + ' KB' : (file.size / 1048576).toFixed(1) + ' MB';
                const row = document.createElement('div');
                row.className = 'flex items-center gap-2 px-3 py-2 bg-white rounded-xl border border-indigo-100 shadow-sm text-xs';
                row.innerHTML = `
                    <span class="px-1.5 py-0.5 bg-indigo-100 text-indigo-700 rounded font-black text-[9px] flex-shrink-0">${ext}</span>
                    <span class="flex-1 truncate text-gray-700 font-semibold">${file.name}</span>
                    <span class="text-gray-400 flex-shrink-0">${size}</span>
                    <button type="button" data-index="${i}" class="remove-attachment ml-1 w-5 h-5 flex items-center justify-center rounded-full bg-red-50 hover:bg-red-100 text-red-400 hover:text-red-600 flex-shrink-0" title="Remove">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>`;
                queueEl.appendChild(row);
            });

            const dt = new DataTransfer();
            attachmentFiles.forEach(f => dt.items.add(f));
            realInput.files = dt.files;
        }

        queueEl.addEventListener('click', function (e) {
            const btn = e.target.closest('.remove-attachment');
            if (!btn) return;
            attachmentFiles.splice(+btn.dataset.index, 1);
            syncQueue();
        });

        document.getElementById('project-form').addEventListener('submit', function () {
            const btn = document.getElementById('submit-btn');
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            document.getElementById('btn-text').textContent = 'Uploading...';
        });

        // ── Adviser Toggle Logic ────────────────────────────────────────────────
        const adviserSelect = document.getElementById('adviser_id_select');
        const otherContainer = document.getElementById('other-adviser-container');
        const otherInput = otherContainer.querySelector('input');

        adviserSelect.addEventListener('change', function() {
            if (this.value === '') {
                otherContainer.classList.remove('hidden');
            } else {
                otherContainer.classList.add('hidden');
                otherInput.value = ''; // Clear value so we don't accidentally submit it
            }
        });
    </script>
</x-app-layout>
