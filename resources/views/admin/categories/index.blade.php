<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Category Management') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition ease-in-out duration-150">
                    Back to Dashboard
                </a>
                <button onclick="document.getElementById('add-category-modal').classList.remove('hidden')" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded shadow-md transition-colors text-sm">
                    + Add New Category
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
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
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Description</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Usage Count</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($categories as $category)
                                @php
                                    $usageCount = $category->projects()->count();
                                @endphp
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">
                                        {{ $category->name }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                                        {{ $category->description ?? 'No description' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $usageCount > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $usageCount }} projects
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex gap-3">
                                        <button onclick="editCategory({{ $category->id }}, '{{ addslashes($category->name) }}', '{{ addslashes($category->description) }}')" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">Edit</button>
                                        
                                        @if($usageCount == 0)
                                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this category?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Delete</button>
                                            </form>
                                        @else
                                            <span class="text-gray-400 cursor-not-allowed" title="Cannot delete category while in use">Delete</span>
                                        @endif
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
