<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 items-start md:items-center">
            <div>
                <h2 class="font-bold text-2xl text-white leading-tight">User Management</h2>
                <p class="text-[10px] text-gray-500 uppercase tracking-widest font-black mt-1">Manage system accounts and permissions</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-800 dark:bg-gray-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-gray-700 dark:hover:bg-gray-600 transition-all shadow-sm hover:shadow-md border border-gray-700 dark:border-gray-600 whitespace-nowrap">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Dashboard
                </a>
                <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 px-3 py-1.5 bg-indigo-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-indigo-700 transition-all shadow-sm hover:shadow-md border border-indigo-500 whitespace-nowrap">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    Add New User
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    



                    @if (session('error'))
                        <div class="bg-red-600 border border-red-700 text-white px-4 py-3 rounded-xl relative mb-4 shadow-lg shadow-red-100/50 flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-300" role="alert">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="block sm:inline font-bold text-sm">{{ session('error') }}</span>
                        </div>
                    @endif

                    <!-- Filters Section -->
                    <div class="mb-6 flex flex-wrap items-center justify-between gap-4 bg-gray-50 dark:bg-gray-900/50 p-4 rounded-2xl border border-gray-100 dark:border-gray-700/50">
                        <div class="flex items-center gap-4">
                            <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-wrap items-center gap-4">
                                <div class="flex items-center gap-2">
                                    <label for="role_filter" class="text-xs font-black uppercase tracking-widest text-gray-400">Role:</label>
                                    <select name="role" id="role_filter" onchange="this.form.submit()" class="bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 text-xs font-bold rounded-xl focus:ring-indigo-500 focus:border-indigo-500 py-2 pl-3 pr-8 transition-all">
                                        <option value="" {{ request('role') == '' ? 'selected' : '' }}>All Roles</option>
                                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admins</option>
                                        <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>Students</option>
                                        <option value="adviser" {{ request('role') == 'adviser' ? 'selected' : '' }}>Advisers</option>
                                    </select>
                                </div>

                                <div class="flex items-center gap-2">
                                    <label for="program_filter" class="text-xs font-black uppercase tracking-widest text-gray-400">Program:</label>
                                    <select name="program" id="program_filter" onchange="this.form.submit()" class="bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 text-xs font-bold rounded-xl focus:ring-indigo-500 focus:border-indigo-500 py-2 pl-3 pr-8 transition-all">
                                        <option value="" {{ request('program') == '' ? 'selected' : '' }}>All Programs</option>
                                        <option value="BSInT" {{ request('program') == 'BSInT' ? 'selected' : '' }}>BSInT</option>
                                        <option value="Com-Sci" {{ request('program') == 'Com-Sci' ? 'selected' : '' }}>Com-Sci</option>
                                    </select>
                                </div>

                                @if(request('role') || request('program'))
                                    <a href="{{ route('admin.users.index') }}" class="text-[10px] font-black uppercase text-indigo-500 hover:text-indigo-600 transition-colors">Clear Filters</a>
                                @endif
                            </form>
                        </div>
                        
                        <div class="text-[10px] text-gray-400 font-black uppercase tracking-widest">
                            Total: {{ $users->total() }} User{{ $users->total() != 1 ? 's' : '' }}
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name / ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Role</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($users as $user)
                                <tr 
                                    x-data="{}" 
                                    @click="window.location.href = '{{ route('admin.users.show', $user) }}'"
                                    class="group cursor-pointer hover:bg-gray-950 dark:hover:bg-white/[0.05] transition-all duration-300 hover:shadow-2xl hover:shadow-black/20"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-bold text-gray-900 dark:text-white group-hover:text-white transition-colors">
                                            {{ $user->name }}
                                        </div>
                                        @if($user->isStudent())
                                            <div class="flex items-center gap-2 mt-1">
                                                <div class="text-[10px] text-gray-400 font-mono uppercase tracking-widest leading-tight group-hover:text-gray-400 transition-colors">
                                                    {{ $user->student_id ?? 'N/A' }}
                                                </div>
                                                @if($user->program)
                                                    <span class="text-[9px] px-1 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded font-black uppercase group-hover:bg-gray-600 group-hover:text-white transition-colors">
                                                        {{ $user->program }}
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-200 group-hover:text-gray-300 transition-colors">{{ $user->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full transition-all duration-300
                                            {{ $user->role === 'admin' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 group-hover:bg-red-500/20 group-hover:text-red-400' : 
                                              ($user->role === 'adviser' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400 group-hover:bg-purple-500/20 group-hover:text-purple-400' : 
                                              'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 group-hover:bg-blue-500/20 group-hover:text-blue-400') }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                        @if($user->role === 'adviser')
                                            <div class="mt-2 text-[10px] text-gray-500 font-bold uppercase tracking-widest">
                                                Workload: {{ $user->advised_projects_count ?? 0 }} Project(s)
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($user->is_active)
                                            <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-xs font-bold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 uppercase tracking-wider transition-all duration-300 group-hover:bg-green-500/20 group-hover:text-green-300">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-600 dark:bg-green-400 group-hover:bg-green-300"></span>
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-xs font-bold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 uppercase tracking-wider transition-all duration-300 group-hover:bg-amber-500/20 group-hover:text-amber-300" title="Requires BSInT Verification">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-600 dark:bg-amber-400 animate-pulse group-hover:bg-amber-300"></span>
                                                Pending Review
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex items-center justify-end gap-3" @click.stop>
                                        @if(!$user->is_active)
                                            <form action="{{ route('admin.users.approve', $user) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-green-700 shadow-sm transition-all">
                                                    Approve
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('admin.users.edit', $user) }}" class="text-indigo-400 hover:text-white font-bold uppercase text-[10px] tracking-widest transition-colors">Edit</a>
                                        
                                        @if($user->id !== auth()->id())
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to PERMANENTLY delete this user? This cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-400 hover:text-white font-bold uppercase text-[10px] tracking-widest transition-colors">Delete</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
