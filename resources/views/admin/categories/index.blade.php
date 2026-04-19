<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 items-start md:items-center">
            <div>
                <h2 class="font-bold text-2xl text-white leading-tight">Category Management</h2>
                <p class="text-[10px] text-gray-500 uppercase tracking-widest font-black mt-1">Manage project specializations and labels</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-800 dark:bg-gray-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-gray-700 dark:hover:bg-gray-600 transition-all shadow-sm hover:shadow-md border border-gray-700 dark:border-gray-600 whitespace-nowrap">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Dashboard
                </a>
                <button onclick="document.getElementById('add-category-modal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-3 py-1.5 bg-indigo-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-indigo-700 transition-all shadow-sm hover:shadow-md border border-indigo-500 whitespace-nowrap">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    Add Category
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
                                    <th class="px-6 py-3 text-left text-[10px] font-black text-gray-500 dark:text-gray-300 uppercase tracking-widest">Description</th>
                                    <th class="px-6 py-3 text-left text-[10px] font-black text-gray-500 dark:text-gray-300 uppercase tracking-widest">Usage Count</th>
                                    <th class="px-6 py-3 text-left text-[10px] font-black text-gray-500 dark:text-gray-300 uppercase tracking-widest">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($categories as $category)
                                    @php
                                        $usageCount = $category->projects()->count();
                                    @endphp
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">
                                            {{ $category->name }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 min-w-[200px] max-w-xs truncate">
                                            {{ $category->description ?? 'No description' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="px-2.5 py-1 inline-flex text-[10px] leading-5 font-black uppercase tracking-widest rounded-lg {{ $usageCount > 0 ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 border border-green-200 dark:border-green-800' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400 border border-gray-200 dark:border-gray-600' }}">
                                                {{ $usageCount }} projects
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center gap-4">
                                                <button onclick="editCategory({{ $category->id }}, '{{ addslashes($category->name) }}', '{{ addslashes($category->description) }}')" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 font-bold uppercase text-[10px] tracking-widest">Edit</button>
                                                
                                                @if($usageCount == 0)
                                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this category?');" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 font-bold uppercase text-[10px] tracking-widest">Delete</button>
                                                    </form>
                                                @else
                                                    <span class="text-gray-400 cursor-not-allowed font-bold uppercase text-[10px] tracking-widest" title="Cannot delete category while in use">Delete</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                @if($categories->isEmpty())
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500 italic">No categories found. Start by adding one.</td>
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
    <div id="add-category-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700">
            <div class="mt-3">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-2">Add New Category</h3>
                <form action="{{ route('admin.categories.store') }}" method="POST">
                    @csrf
                    <div class="mt-2 text-left">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category Name</label>
                        <input type="text" name="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600">
                    </div>
                    <div class="mt-4 text-left">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description (Optional)</label>
                        <textarea name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600"></textarea>
                    </div>
                    <div class="items-center px-4 py-3 mt-4 flex justify-end gap-3">
                        <button type="button" onclick="document.getElementById('add-category-modal').classList.add('hidden')" class="px-4 py-2 bg-gray-200 text-gray-800 font-bold rounded hover:bg-gray-300 text-sm">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-bold rounded hover:bg-indigo-700 text-sm">Save Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="edit-category-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700">
            <div class="mt-3">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-2">Edit Category</h3>
                <form id="edit-category-form" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mt-2 text-left">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category Name</label>
                        <input type="text" name="name" id="edit-category-name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600">
                    </div>
                    <div class="mt-4 text-left">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description (Optional)</label>
                        <textarea name="description" id="edit-category-desc" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600"></textarea>
                    </div>
                    <div class="items-center px-4 py-3 mt-4 flex justify-end gap-3">
                        <button type="button" onclick="document.getElementById('edit-category-modal').classList.add('hidden')" class="px-4 py-2 bg-gray-200 text-gray-800 font-bold rounded hover:bg-gray-300 text-sm">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-bold rounded hover:bg-indigo-700 text-sm">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function editCategory(id, name, desc) {
            document.getElementById('edit-category-name').value = name;
            document.getElementById('edit-category-desc').value = desc === 'null' ? '' : desc;
            document.getElementById('edit-category-form').action = '/admin/categories/' + id;
            document.getElementById('edit-category-modal').classList.remove('hidden');
        }
    </script>
</x-app-layout>
