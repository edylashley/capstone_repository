<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-white leading-tight flex-1 break-words">
                {{ __('Edit Project Metadata') }}: <span class="opacity-90">{{ $project->title }}</span>
            </h2>
            <a href="{{ route('admin.projects.index') }}" class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-800 dark:bg-gray-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-gray-700 dark:hover:bg-gray-600 transition-all shadow-sm hover:shadow-md border border-gray-700 dark:border-gray-600 shrink-0 whitespace-nowrap mt-1 sm:mt-0">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to List
            </a>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <form method="POST" action="{{ route('admin.projects.update', $project) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Title -->
                            <div class="col-span-2">
                                <x-input-label for="title" :value="__('Title')" class="text-white"/>
                                <x-text-input id="title" class="block mt-1 w-full dark:bg-gray-900" type="text" name="title" :value="old('title', $project->title)" required />
                                <x-input-error :messages="$errors->get('title')" class="mt-2" />
                            </div>

                            <!-- Authors -->
                            <div class="col-span-2">
                                <x-input-label for="authors_list" :value="__('Authors')" class="text-white"/>
                                <x-text-input id="authors_list" class="block mt-1 w-full dark:bg-gray-900 text-sm" type="text" name="authors_list" :value="old('authors_list', $project->authors_list ?: $project->authors->pluck('name')->join(', '))" placeholder="Comma-separated e.g. John Doe, Jane Doe" />
                                <x-input-error :messages="$errors->get('authors_list')" class="mt-2" />
                                <p class="text-[10px] text-gray-400 mt-1 uppercase tracking-widest font-bold">Only for projects stored without active student accounts. Leave blank if submitted directly by students.</p>
                            </div>

                            <!-- Year -->
                            <div>
                                <x-input-label for="year" :value="__('Year')" class="text-white"/>
                                <x-text-input id="year" class="block mt-1 w-full dark:bg-gray-900" type="number" name="year" :value="old('year', $project->year)" required />
                                <x-input-error :messages="$errors->get('year')" class="mt-2" />
                            </div>

                            <!-- Status -->
                            <div>
                                <x-input-label for="status" :value="__('Status')" class="text-white"/>
                                <select id="status" name="status" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    @foreach(['pending', 'verified', 'approved', 'published', 'archived'] as $status)
                                        <option value="{{ $status }}" {{ $project->status === $status ? 'selected' : '' }}>
                                            {{ ucfirst($status) }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>

                            <!-- Adviser -->
                            <div class="col-span-2">
                                <x-input-label for="adviser_id" :value="__('Adviser')" class="text-white"/>
                                <select id="adviser_id" name="adviser_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    @foreach($advisers as $adviser)
                                        <option value="{{ $adviser->id }}" {{ $project->adviser_id === $adviser->id ? 'selected' : '' }}>
                                            {{ $adviser->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('adviser_id')" class="mt-2" />
                            </div>

                            <!-- Abstract -->
                            <div class="col-span-2">
                                <x-input-label for="abstract" :value="__('Abstract')" class="text-white"/>
                                <textarea id="abstract" name="abstract" rows="6" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('abstract', $project->abstract) }}</textarea>
                                <x-input-error :messages="$errors->get('abstract')" class="mt-2" />
                            </div>

                            <!-- Project Categories -->
                            <div class="col-span-2">
                                <x-input-label :value="__('Project Categories')" class="text-white mb-2"/>
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 bg-gray-900/50 p-4 rounded-lg border border-gray-700">
                                    @foreach($categories as $cat)
                                        <label class="flex items-center gap-3 cursor-pointer group">
                                            <input type="checkbox" name="categories[]" value="{{ $cat->id }}" 
                                                {{ (is_array(old('categories')) && in_array($cat->id, old('categories'))) || (!old('categories') && $project->categories->contains($cat->id)) ? 'checked' : '' }}
                                                class="rounded border-gray-700 bg-gray-900 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                            <span class="text-sm text-gray-300 group-hover:text-white transition-colors">{{ $cat->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <x-input-error :messages="$errors->get('categories')" class="mt-2" />
                            </div>

                            <!-- Program -->
                            <div>
                                <x-input-label for="program" :value="__('Program')" class="text-white"/>
                                <select id="program" name="program" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="" disabled {{ (old('program', $project->program)) ? '' : 'selected' }}>Choose Program</option>
                                    <option value="BSInT" {{ old('program', $project->program) == 'BSInT' ? 'selected' : '' }}>BSInT</option>
                                    <option value="Com-Sci" {{ old('program', $project->program) == 'Com-Sci' ? 'selected' : '' }}>Com-Sci</option>
                                </select>
                                <x-input-error :messages="$errors->get('program')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('admin.dashboard') }}">
                                {{ __('Cancel') }}
                            </a>

                            <x-primary-button class="ml-4">
                                {{ __('Update Project') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
