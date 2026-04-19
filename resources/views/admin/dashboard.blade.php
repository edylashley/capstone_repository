<x-app-layout>
    <style>
        @media (min-width: 850px) {
            .stats-grid { 
                display: grid !important;
                grid-template-columns: repeat(5, minmax(0, 1fr)) !important; 
            }
            .storage-card { 
                grid-column: span 1 / span 1 !important; 
                text-align: left !important;
                align-items: flex-start !important;
            }
        }
        @media (min-width: 1100px) {
            .actions-grid { 
                display: grid !important;
                grid-template-columns: repeat(4, minmax(0, 1fr)) !important; 
            }
        }
    </style>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 items-start md:items-center">
            <div>
                <h2 class="font-bold text-2xl text-white leading-tight">Admin Dashboard & Master Directory</h2>
                <p class="text-[10px] text-gray-400 uppercase tracking-widest font-black mt-1">Real-time system oversight and statistics</p>
            </div>
            @php $sysStatus = \App\Models\Setting::getSystemStatus(); @endphp
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-black uppercase text-gray-500">System Status:</span>
                <div class="flex items-center gap-1.5 px-2.5 py-1 {{ $sysStatus['bg'] }} {{ $sysStatus['text'] }} rounded-full border border-{{ $sysStatus['color'] }}-200 shadow-sm transition-all duration-300">
                    <span class="w-1.5 h-1.5 rounded-full {{ $sysStatus['color'] == 'green' ? 'bg-green-500 animate-pulse' : ($sysStatus['color'] == 'yellow' ? 'bg-yellow-500' : 'bg-red-500 animate-bounce') }}"></span>
                    <span class="text-[10px] font-black uppercase tracking-tighter">{{ $sysStatus['label'] }}</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            <!-- Statistics Cards -->
            <div class="grid grid-cols-2 stats-grid gap-4">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-2xl p-4 sm:p-6 border-b-4 border-blue-500">
                    <div class="text-gray-500 dark:text-gray-400 text-xs uppercase font-bold mb-1">Total Projects</div>
                    <div class="text-2xl font-black text-gray-900 dark:text-gray-100">{{ $stats['total_projects'] }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-2xl p-4 sm:p-6 border-b-4 border-yellow-500">
                    <div class="text-gray-500 dark:text-gray-400 text-xs uppercase font-bold mb-1">Processing</div>
                    <div class="text-2xl font-black text-yellow-600">{{ $stats['pending_projects'] }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-2xl p-4 sm:p-6 border-b-4 border-green-500">
                    <div class="text-gray-500 dark:text-gray-400 text-xs uppercase font-bold mb-1">Confirmed</div>
                    <div class="text-2xl font-black text-green-600">{{ $stats['approved_projects'] }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-2xl p-4 sm:p-6 border-b-4 border-indigo-500">
                    <div class="text-gray-500 dark:text-gray-400 text-xs uppercase font-bold mb-1">Users</div>
                    <div class="text-2xl font-black text-indigo-600">{{ $stats['total_users'] }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-2xl p-4 sm:p-6 border-b-4 border-red-500 col-span-2 storage-card flex flex-col items-center text-center">
                    <div class="text-gray-500 dark:text-gray-400 text-xs uppercase font-bold mb-1">Repository Storage</div>
                    <div class="text-2xl font-black text-red-600">{{ $stats['total_storage'] }}</div>
                </div>
            </div>

            <!-- Refined 4-Pillar Quick Actions Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 actions-grid gap-4">
                <!-- Pillar 1: User Management -->
                <a href="{{ route('admin.users.index') }}" class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group border-b-4 border-b-blue-500">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-900/40 flex items-center justify-center text-2xl group-hover:bg-blue-500 group-hover:text-white transition-all duration-300">
                            👤
                        </div>
                        <div>
                            <div class="text-sm font-black text-gray-900 dark:text-gray-100 uppercase tracking-tight">Users</div>
                            <div class="text-[10px] text-gray-500 uppercase font-bold tracking-widest leading-tight">Identity & Access</div>
                        </div>
                    </div>
                </a>

                <!-- Pillar 2: Project Workflow -->
                <a href="{{ route('admin.projects.index') }}" class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group border-b-4 border-b-indigo-500">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-900/40 flex items-center justify-center text-2xl group-hover:bg-indigo-500 group-hover:text-white transition-all duration-300">
                            📂
                        </div>
                        <div>
                            <div class="text-sm font-black text-gray-900 dark:text-gray-100 uppercase tracking-tight">Projects</div>
                            <div class="text-[10px] text-gray-500 uppercase font-bold tracking-widest leading-tight">Verification Flow</div>
                        </div>
                    </div>
                </a>

                <!-- Pillar 3: Categories -->
                <a href="{{ route('admin.categories.index') }}" class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group border-b-4 border-b-emerald-500">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/40 flex items-center justify-center text-2xl group-hover:bg-emerald-500 group-hover:text-white transition-all duration-300">
                            🏷️
                        </div>
                        <div>
                            <div class="text-sm font-black text-gray-900 dark:text-gray-100 uppercase tracking-tight">Specializations</div>
                            <div class="text-[10px] text-gray-500 uppercase font-bold tracking-widest leading-tight">Category Sorting</div>
                        </div>
                    </div>
                </a>

                <!-- Pillar 4: Settings -->
                <a href="{{ route('admin.settings.index') }}" class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group border-b-4 border-b-orange-500">
                    <div class="flex items-center gap-4">
                        @php $isMaintenance = \App\Models\Setting::get('maintenance_mode', '0') == '1'; @endphp
                        <div class="w-12 h-12 rounded-xl {{ $isMaintenance ? 'bg-red-50 dark:bg-red-900/40' : 'bg-orange-50 dark:bg-orange-900/40' }} flex items-center justify-center text-2xl group-hover:bg-gray-700 group-hover:text-white transition-all duration-300">
                            {{ $isMaintenance ? '⚠️' : '⚙️' }}
                        </div>
                        <div>
                            <div class="text-sm font-black text-gray-900 dark:text-gray-100 uppercase tracking-tight">System Configuration</div>
                            <div class="text-[10px] {{ $isMaintenance ? 'text-red-500 font-black' : 'text-gray-400' }} uppercase font-bold tracking-widest leading-tight">
                                {{ $isMaintenance ? 'Maintenance Active' : 'Deadlines & Config' }}
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Master Archive -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-bold mb-4">Master Project Directory</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider min-w-[500px]">Title</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Adviser</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($projects as $project)
                                <tr>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ $project->id }}</td>
                                    <td class="px-4 py-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap">{{ $project->title }}</div>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-[10px] bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 px-1.5 py-0.5 rounded font-bold uppercase tracking-tighter">{{ $project->year }}</span>
                                            <span class="text-[10px] text-gray-500 font-medium">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ $project->created_at->format('m-d-Y') }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ $project->adviser->name ?? 'None' }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $project->status === 'approved' || $project->status === 'published' ? 'bg-green-100 text-green-800' : 
                                              ($project->status === 'archived' ? 'bg-gray-100 text-gray-800' : 
                                              'bg-yellow-100 text-yellow-800') }}">
                                            {{ ucfirst($project->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('projects.show', $project) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                                        <!-- Edit button would go here -->
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $projects->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
