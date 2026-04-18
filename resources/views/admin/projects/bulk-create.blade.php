<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-2xl text-white leading-tight">Bulk Direct Entry</h2>
                <p class="text-xs text-indigo-200 mt-1 uppercase tracking-widest font-black">Upload multiple legacy
                    capstone projects at once</p>
            </div>
            <a href="{{ route('admin.projects.index') }}"
                class="px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition ease-in-out duration-150">
                Back to List
            </a>
        </div>
    </x-slot>

    <div class="py-3">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 rounded-xl bg-green-50 text-green-700 border border-green-200 text-sm font-bold">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-xl bg-red-50 text-red-700 border border-red-200 text-sm font-bold">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 rounded-xl bg-red-50 border border-red-200">
                    <p class="text-red-700 font-bold text-sm mb-2">There were errors with your submission:</p>
                    <ul class="list-disc pl-5 text-sm text-red-600 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="bulk-form" method="POST" action="{{ route('admin.projects.bulk-store') }}"
                enctype="multipart/form-data">
                @csrf

                <div id="projects-container" class="space-y-6"></div>

                <!-- Add Project Button -->
                <div class="mt-6 flex items-center gap-4">
                    <button type="button" id="add-project-btn"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-white dark:bg-gray-800 hover:bg-indigo-50 dark:hover:bg-gray-700 text-indigo-600 dark:text-indigo-400 font-black text-xs uppercase tracking-widest rounded-2xl border-2 border-dashed border-indigo-300 dark:border-indigo-600 hover:border-indigo-500 transition-all shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M12 4v16m8-8H4" />
                        </svg>
                        Add Another Project
                    </button>
                    <span id="project-counter" class="text-[10px] text-gray-400 font-black uppercase tracking-widest">0
                        Projects</span>
                </div>

                <!-- Submit -->
                <div class="mt-8 pt-6 border-t border-gray-200 flex items-center justify-between">
                    <a href="{{ route('admin.projects.index') }}"
                        class="text-sm font-bold text-gray-500 hover:text-gray-800">Cancel</a>
                    <button id="submit-btn" type="submit"
                        class="inline-flex items-center gap-2 px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-sm uppercase tracking-widest rounded-xl shadow-lg hover:shadow-indigo-500/30 transition transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed"
                        disabled>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        <span id="btn-text">Publish All Projects</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // ── Template data ──────────────────────────────────────────────────────
        const advisers = @json($advisers->map(fn($a) => ['id' => $a->id, 'name' => $a->name]));
        const categories = @json($categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name]));
        let projectIndex = 0;

        // ── Build project card HTML ────────────────────────────────────────────
        function createProjectCard(idx) {
            const adviserOptions = advisers.map(a => `<option value="${a.id}">${a.name}</option>`).join('');
            const categoryCheckboxes = categories.map(c => `
                <label class="flex items-center gap-2 p-2 rounded border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer transition-colors group">
                    <input type="checkbox" name="projects[${idx}][categories][]" value="${c.id}" class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-indigo-600 shadow-sm focus:ring-indigo-500">
                    <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 group-hover:text-indigo-600 dark:group-hover:text-indigo-400">${c.name}</span>
                </label>
            `).join('');

            const card = document.createElement('div');
            card.className = 'bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-2xl border border-gray-100 dark:border-gray-700 project-card';
            card.dataset.index = idx;
            card.innerHTML = `
                <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-3 flex items-center justify-between">
                    <span class="text-white font-black text-xs uppercase tracking-widest flex items-center gap-2">
                        <span class="w-6 h-6 bg-white/20 rounded-full flex items-center justify-center text-[10px]">${idx + 1}</span>
                        Project Entry #${idx + 1}
                    </span>
                    <button type="button" onclick="removeProject(this)" class="text-white/60 hover:text-white hover:bg-white/20 rounded-lg p-1 transition" title="Remove this entry">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Project Title <span class="text-red-500">*</span></label>
                            <input type="text" name="projects[${idx}][title]" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                        </div>
                        <div>
                            <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Authors (Comma-separated) <span class="text-red-500">*</span></label>
                            <input type="text" name="projects[${idx}][authors_list]" placeholder="e.g. John Doe, Jane Smith" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Year <span class="text-red-500">*</span></label>
                            <input type="number" name="projects[${idx}][year]" value="${new Date().getFullYear()}" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                        </div>
                        <div>
                            <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Adviser</label>
                            <select name="projects[${idx}][adviser_id]" class="adviser-select mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" onchange="toggleAdviserOther(this, ${idx})">
                                <option value="">-- Not listed --</option>
                                ${adviserOptions}
                            </select>
                        </div>
                        <div id="adviser-other-${idx}">
                            <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Other Adviser</label>
                            <input type="text" name="projects[${idx}][adviser_name]" placeholder="e.g. Dr. Alan Turing" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Program <span class="text-red-500">*</span></label>
                            <select name="projects[${idx}][program]" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                                <option value="" disabled selected>Choose...</option>
                                <option value="BSInT">BSInT</option>
                                <option value="Com-Sci">Com-Sci</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-2">Categories <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-32 overflow-y-auto pr-2 border border-gray-200 dark:border-gray-700 rounded-lg p-2 bg-gray-50 dark:bg-gray-900/50">
                                ${categoryCheckboxes}
                            </div>
                        </div>
                        <div>
                            <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Abstract <span class="text-gray-400 text-[10px] uppercase">(Optional)</span></label>
                            <textarea name="projects[${idx}][abstract]" rows="2" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Paste abstract here..."></textarea>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600">
                            <label class="block font-bold text-xs uppercase tracking-widest text-blue-600 dark:text-blue-300 mb-2">Main Manuscript (PDF) <span class="text-red-500">*</span></label>
                            <input type="file" accept="application/pdf" name="projects[${idx}][manuscript]" required
                                   class="block w-full text-sm text-gray-700 dark:text-gray-200 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-black file:bg-gray-200 file:text-gray-700 hover:file:bg-gray-300 transition-all cursor-pointer">
                            <p class="text-[10px] text-blue-500 dark:text-blue-300 mt-1.5 font-semibold">PDF only &mdash; max 50 MB</p>
                        </div>

                        <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600">
                            <label class="block font-bold text-xs uppercase tracking-widest text-indigo-600 dark:text-indigo-300 mb-2">
                                Attachments
                                <span class="normal-case font-normal text-gray-500 dark:text-gray-400">(Optional)</span>
                            </label>
                            <input type="file" name="projects[${idx}][attachments][]" multiple
                                   accept=".zip,.rar,.7z,.pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.mp4,.avi,.mov,.sql,.txt,.csv,.json,.xml,.jpg,.jpeg,.png,.gif,.md"
                                   class="block w-full text-sm text-gray-700 dark:text-gray-200 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-black file:bg-gray-200 file:text-gray-700 hover:file:bg-gray-300 transition-all cursor-pointer">
                            <p class="text-[10px] text-indigo-500 dark:text-indigo-300 mt-1.5 font-semibold">Max 200 MB per file</p>
                        </div>
                    </div>
                </div>
            `;
            return card;
        }

        // ── Toggle adviser other input ─────────────────────────────────────────
        function toggleAdviserOther(select, idx) {
            const otherDiv = document.getElementById(`adviser-other-${idx}`);
            if (select.value === '') {
                otherDiv.classList.remove('hidden');
            } else {
                otherDiv.classList.add('hidden');
                otherDiv.querySelector('input').value = '';
            }
        }

        // ── Add project ────────────────────────────────────────────────────────
        function addProject() {
            const container = document.getElementById('projects-container');
            const card = createProjectCard(projectIndex);
            container.appendChild(card);
            projectIndex++;
            updateCounter();
            card.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        // ── Remove project ─────────────────────────────────────────────────────
        function removeProject(btn) {
            const card = btn.closest('.project-card');
            card.style.transition = 'all 0.3s ease';
            card.style.opacity = '0';
            card.style.transform = 'translateX(-20px)';
            setTimeout(() => {
                card.remove();
                renumberCards();
                updateCounter();
            }, 300);
        }

        // ── Renumber visible cards ─────────────────────────────────────────────
        function renumberCards() {
            const cards = document.querySelectorAll('.project-card');
            cards.forEach((card, i) => {
                const header = card.querySelector('.bg-gradient-to-r span');
                const badge = header.querySelector('span');
                badge.textContent = i + 1;
                header.innerHTML = '';
                header.appendChild(badge);
                header.append(` Project Entry #${i + 1}`);
            });
        }

        // ── Update counter and submit button ───────────────────────────────────
        function updateCounter() {
            const count = document.querySelectorAll('.project-card').length;
            document.getElementById('project-counter').textContent = `${count} Project${count !== 1 ? 's' : ''}`;
            document.getElementById('submit-btn').disabled = count === 0;
            document.getElementById('btn-text').textContent = count === 0 ? 'Add projects first' : `Publish All ${count} Project${count !== 1 ? 's' : ''}`;
        }

        // ── Wire up ────────────────────────────────────────────────────────────
        document.getElementById('add-project-btn').addEventListener('click', addProject);

        document.getElementById('bulk-form').addEventListener('submit', function () {
            const btn = document.getElementById('submit-btn');
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            document.getElementById('btn-text').textContent = 'Uploading... Please wait';
        });

        // Start with one card
        addProject();
    </script>
</x-app-layout>