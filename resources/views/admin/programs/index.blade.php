<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 items-start md:items-center">
            <div>
                <h2 class="font-bold text-2xl text-white leading-tight">Program Management</h2>
                <p class="text-[10px] text-gray-500 uppercase tracking-widest font-black mt-1">Manage academic programs and abbreviations</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-800 dark:bg-gray-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-gray-700 dark:hover:bg-gray-600 transition-all shadow-sm hover:shadow-md border border-gray-700 dark:border-gray-600 whitespace-nowrap">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Dashboard
                </a>
                <button onclick="document.getElementById('add-program-modal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-3 py-1.5 bg-indigo-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-indigo-700 transition-all shadow-sm hover:shadow-md border border-indigo-500 whitespace-nowrap">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    Add Program
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded mb-6">
                    <p class="font-bold">Success</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded mb-6">
                    <p class="font-bold">Error</p>
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-0 sm:p-6 text-gray-900 dark:text-gray-100">
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-[10px] font-black text-gray-500 dark:text-gray-300 uppercase tracking-widest">Name</th>
                                    <th class="px-6 py-3 text-left text-[10px] font-black text-gray-500 dark:text-gray-300 uppercase tracking-widest">Abbr</th>
                                    <th class="px-6 py-3 text-left text-[10px] font-black text-gray-500 dark:text-gray-300 uppercase tracking-widest">Description</th>
                                    <th class="px-6 py-3 text-left text-[10px] font-black text-gray-500 dark:text-gray-300 uppercase tracking-widest">Usage Count</th>
                                    <th class="px-6 py-3 text-left text-[10px] font-black text-gray-500 dark:text-gray-300 uppercase tracking-widest">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($programs as $program)
                                    @php
                                        $projectCount = \App\Models\Project::where('program', $program->abbreviation)->count();
                                        $userCount = \App\Models\User::where('program', $program->abbreviation)->count();
                                    @endphp
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">
                                            {{ $program->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-indigo-600 dark:text-indigo-400 font-black">
                                            {{ $program->abbreviation }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 min-w-[200px] max-w-xs truncate">
                                            {{ $program->description ?? 'No description' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div class="flex flex-col gap-1">
                                                <span class="px-2.5 py-1 inline-flex text-[8px] leading-5 font-black uppercase tracking-widest rounded-lg bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800">
                                                    {{ $projectCount }} projects
                                                </span>
                                                <span class="px-2.5 py-1 inline-flex text-[8px] leading-5 font-black uppercase tracking-widest rounded-lg bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                                    {{ $userCount }} users
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center gap-4">
                                                <button onclick="editProgram({{ $program->id }}, '{{ addslashes($program->name) }}', '{{ addslashes($program->abbreviation) }}', '{{ addslashes($program->description) }}')" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 font-bold uppercase text-[10px] tracking-widest">Edit</button>
                                                
                                                @if($projectCount == 0 && $userCount == 0)
                                                    <form action="{{ route('admin.programs.destroy', $program) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this program?');" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 font-bold uppercase text-[10px] tracking-widest">Delete</button>
                                                    </form>
                                                @else
                                                    <span class="text-gray-400 cursor-not-allowed font-bold uppercase text-[10px] tracking-widest" title="Cannot delete program while in use">Delete</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                @if($programs->isEmpty())
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500 italic">No programs found. Start by adding one.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div id="add-program-modal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm hidden z-50 overflow-y-auto p-4">
        <div class="relative top-10 mx-auto p-8 border w-full max-w-xl shadow-2xl rounded-[2rem] bg-slate-900 border-slate-800">
            <div class="mt-3">
                <h3 class="text-2xl font-black text-white mb-6 uppercase tracking-tighter">Add New Program</h3>
                <form action="{{ route('admin.programs.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="text-left">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Full Program Name</label>
                        <input type="text" name="name" placeholder="e.g. Bachelor of Science in Information Technology" required 
                            class="block w-full px-5 py-4 border border-slate-700 rounded-2xl bg-slate-950/50 text-white placeholder-slate-600 focus:bg-slate-950 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all sm:text-sm">
                    </div>
                    <div class="text-left">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Abbreviation (For Filtering)</label>
                        <input type="text" name="abbreviation" placeholder="e.g. BSIT" required 
                            class="block w-full px-5 py-4 border border-slate-700 rounded-2xl bg-slate-950/50 text-white placeholder-slate-600 focus:bg-slate-950 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all sm:text-sm">
                    </div>
                    <div class="text-left">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Description (Optional)</label>
                        <textarea name="description" rows="4" 
                            class="block w-full px-5 py-4 border border-slate-700 rounded-2xl bg-slate-950/50 text-white placeholder-slate-600 focus:bg-slate-950 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all sm:text-sm" placeholder="Briefly describe the program scope..."></textarea>
                    </div>
                    <div class="flex justify-end gap-3 pt-6 border-t border-slate-800">
                        <button type="button" onclick="document.getElementById('add-program-modal').classList.add('hidden')" 
                            class="px-6 py-3 bg-slate-800 text-slate-300 font-bold uppercase text-[10px] tracking-widest rounded-xl hover:bg-slate-700 transition-all">Cancel</button>
                        <button type="submit" 
                            class="px-8 py-3 bg-indigo-600 text-white font-black uppercase text-[10px] tracking-widest rounded-xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-900/20 border border-indigo-500/50">Save Program</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="edit-program-modal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm hidden z-50 overflow-y-auto p-4">
        <div class="relative top-10 mx-auto p-8 border w-full max-w-xl shadow-2xl rounded-[2rem] bg-slate-900 border-slate-800">
            <div class="mt-3">
                <h3 class="text-2xl font-black text-white mb-6 uppercase tracking-tighter">Edit Program</h3>
                <form id="edit-program-form" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    <div class="text-left">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Full Program Name</label>
                        <input type="text" name="name" id="edit-program-name" required 
                            class="block w-full px-5 py-4 border border-slate-700 rounded-2xl bg-slate-950/50 text-white placeholder-slate-600 focus:bg-slate-950 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all sm:text-sm">
                    </div>
                    <div class="text-left">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Abbreviation</label>
                        <input type="text" name="abbreviation" id="edit-program-abbr" required 
                            class="block w-full px-5 py-4 border border-slate-700 rounded-2xl bg-slate-950/50 text-white placeholder-slate-600 focus:bg-slate-950 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all sm:text-sm">
                    </div>
                    <div class="text-left">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Description (Optional)</label>
                        <textarea name="description" id="edit-program-desc" rows="4" 
                            class="block w-full px-5 py-4 border border-slate-700 rounded-2xl bg-slate-950/50 text-white placeholder-slate-600 focus:bg-slate-950 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all sm:text-sm"></textarea>
                    </div>
                    <div class="flex justify-end gap-3 pt-6 border-t border-slate-800">
                        <button type="button" onclick="document.getElementById('edit-program-modal').classList.add('hidden')" 
                            class="px-6 py-3 bg-slate-800 text-slate-300 font-bold uppercase text-[10px] tracking-widest rounded-xl hover:bg-slate-700 transition-all">Cancel</button>
                        <button type="submit" 
                            class="px-8 py-3 bg-indigo-600 text-white font-black uppercase text-[10px] tracking-widest rounded-xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-900/20 border border-indigo-500/50">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function editProgram(id, name, abbr, desc) {
            document.getElementById('edit-program-name').value = name;
            document.getElementById('edit-program-abbr').value = abbr;
            document.getElementById('edit-program-desc').value = desc === 'null' ? '' : desc;
            document.getElementById('edit-program-form').action = '/admin/programs/' + id;
            document.getElementById('edit-program-modal').classList.remove('hidden');
        }
    </script>
</x-app-layout>
