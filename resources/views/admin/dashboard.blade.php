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
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            {{-- Integrated Header --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-2">
                <div>
                    <h2 class="font-black text-4xl text-white uppercase tracking-tighter leading-none">Dashboard</h2>
                    <p class="text-[10px] text-indigo-400 uppercase tracking-[0.4em] font-black mt-3 opacity-80">System Management & Monitoring</p>
                </div>
                @php $sysStatus = \App\Models\Setting::getSystemStatus(); @endphp
                <div class="flex flex-wrap items-center gap-y-4 gap-x-8 bg-slate-900/50 px-6 py-3 rounded-2xl border border-white/5 shadow-inner">
                    <div class="flex items-center gap-3 border-r border-white/10 pr-6 last:border-r-0">
                        <span class="text-[9px] font-black uppercase text-slate-500 tracking-widest">System Status:</span>
                        <div class="flex items-center gap-2 {{ $sysStatus['text'] }}">
                            <span class="w-2 h-2 rounded-full {{ $sysStatus['color'] == 'green' ? 'bg-green-500 animate-pulse' : ($sysStatus['color'] == 'yellow' ? 'bg-yellow-500' : 'bg-red-500 animate-bounce') }}"></span>
                            <span class="text-[11px] font-black uppercase tracking-widest">{{ $sysStatus['label'] }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-[9px] font-black uppercase text-slate-500 tracking-widest">Security Scanner:</span>
                        @php
                            $secColor = match($stats['security_status']) {
                                'online' => 'text-emerald-500',
                                'ready'  => 'text-amber-500',
                                default  => 'text-rose-500',
                            };
                            $secDot = match($stats['security_status']) {
                                'online' => 'bg-emerald-500 animate-pulse',
                                'ready'  => 'bg-amber-500',
                                default  => 'bg-rose-500 animate-pulse',
                            };
                        @endphp
                        <div class="flex items-center gap-2 {{ $secColor }}">
                            <span class="w-2 h-2 rounded-full {{ $secDot }}"></span>
                            <span class="text-[11px] font-black uppercase tracking-widest">{{ $stats['security_status'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-2 stats-grid gap-4">
                {{-- Total Projects --}}
                <div
                    class="bg-slate-900 rounded-3xl p-6 border border-white/5 border-b-4 border-b-blue-500 shadow-lg shadow-blue-500/5 transition-all hover:shadow-blue-500/10 group relative overflow-hidden">
                    <div
                        class="absolute -right-4 -top-4 w-16 h-16 bg-blue-500/5 rounded-full blur-2xl group-hover:bg-blue-500/10 transition-all">
                    </div>
                    <div class="text-slate-500 text-[11px] uppercase font-black tracking-widest mb-2">Total Projects
                    </div>
                    <div class="text-3xl font-black text-white group-hover:text-blue-400 transition-colors uppercase tracking-widest">{{ $stats['total_projects'] }}</div>
                </div>

                {{-- Processing --}}
                <div
                    class="bg-slate-900 rounded-3xl p-6 border border-white/5 border-b-4 border-b-yellow-500 shadow-lg shadow-yellow-500/5 transition-all hover:shadow-yellow-500/10 group relative overflow-hidden">
                    <div
                        class="absolute -right-4 -top-4 w-16 h-16 bg-yellow-500/5 rounded-full blur-2xl group-hover:bg-yellow-500/10 transition-all">
                    </div>
                    <div class="text-slate-500 text-[11px] uppercase font-black tracking-widest mb-2">Processing</div>
                    <div class="text-3xl font-black text-yellow-500 group-hover:text-yellow-400 transition-colors uppercase tracking-widest">{{ $stats['pending_projects'] }}</div>
                </div>

                {{-- Confirmed --}}
                <div
                    class="bg-slate-900 rounded-3xl p-6 border border-white/5 border-b-4 border-b-emerald-500 shadow-lg shadow-emerald-500/5 transition-all hover:shadow-emerald-500/10 group relative overflow-hidden">
                    <div
                        class="absolute -right-4 -top-4 w-16 h-16 bg-emerald-500/5 rounded-full blur-2xl group-hover:bg-emerald-500/10 transition-all">
                    </div>
                    <div class="text-slate-500 text-[11px] uppercase font-black tracking-widest mb-2">Confirmed</div>
                        <div class="text-3xl font-black text-emerald-500 group-hover:text-emerald-400 transition-colors uppercase tracking-widest">{{ $stats['approved_projects'] }}</div>
                </div>

                {{-- Users --}}
                <div
                    class="bg-slate-900 rounded-3xl p-6 border border-white/5 border-b-4 border-b-indigo-500 shadow-lg shadow-indigo-500/5 transition-all hover:shadow-indigo-500/10 group relative overflow-hidden">
                    <div
                        class="absolute -right-4 -top-4 w-16 h-16 bg-indigo-500/5 rounded-full blur-2xl group-hover:bg-indigo-500/10 transition-all">
                    </div>
                    <div class="text-slate-500 text-[11px] uppercase font-black tracking-widest mb-2">Users</div>
                        <div class="text-3xl font-black text-indigo-500 group-hover:text-indigo-400 transition-colors uppercase tracking-widest">{{ $stats['total_users'] }}</div>
                </div>

                {{-- Storage --}}
                <div
                    class="bg-slate-900 rounded-3xl p-6 border border-white/5 border-b-4 border-b-rose-500 shadow-lg shadow-rose-500/5 transition-all hover:shadow-rose-500/10 group relative overflow-hidden col-span-2 storage-card">
                    <div
                        class="absolute -right-4 -top-4 w-16 h-16 bg-rose-500/5 rounded-full blur-2xl group-hover:bg-rose-500/10 transition-all">
                    </div>
                    <div class="text-slate-500 text-[11px] uppercase font-black tracking-widest mb-2">Repository Storage
                    </div>
                    <div class="text-3xl font-black text-rose-500 group-hover:text-rose-400 transition-colors uppercase tracking-widest">{{ $stats['total_storage'] }}</div>
                </div>
            </div>

            <!-- Refined 4-Pillar Quick Actions Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 actions-grid gap-4">
                <!-- Pillar 1: User Management -->
                <a href="{{ route('admin.users.index') }}"
                    class="bg-slate-900 p-4 rounded-2xl shadow-sm border border-white/5 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group border-b-4 border-b-blue-500">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-xl bg-blue-500/10 flex items-center justify-center text-2xl group-hover:bg-blue-500 group-hover:text-white transition-all duration-300">
                            👤
                        </div>
                        <div>
                            <div class="text-sm font-black text-white uppercase tracking-tight">Users</div>
                            <div class="text-[10px] text-slate-500 uppercase font-bold tracking-widest leading-tight">
                                Identity & Access</div>
                        </div>
                    </div>
                </a>

                <!-- Pillar 2: Project Workflow -->
                <a href="{{ route('admin.projects.index') }}"
                    class="bg-slate-900 p-4 rounded-2xl shadow-sm border border-white/5 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group border-b-4 border-b-indigo-500">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-xl bg-indigo-500/10 flex items-center justify-center text-2xl group-hover:bg-indigo-500 group-hover:text-white transition-all duration-300">
                            📂
                        </div>
                        <div>
                            <div class="text-sm font-black text-white uppercase tracking-tight">Projects</div>
                            <div class="text-[10px] text-slate-500 uppercase font-bold tracking-widest leading-tight">
                                Verification Flow</div>
                        </div>
                    </div>
                </a>

                <!-- Pillar 3: Categories -->
                <a href="{{ route('admin.categories.index') }}"
                    class="bg-slate-900 p-4 rounded-2xl shadow-sm border border-white/5 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group border-b-4 border-b-emerald-500">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-xl bg-emerald-500/10 flex items-center justify-center text-2xl group-hover:bg-emerald-500 group-hover:text-white transition-all duration-300">
                            🏷️
                        </div>
                        <div>
                            <div class="text-sm font-black text-white uppercase tracking-tight">Specializations</div>
                            <div class="text-[10px] text-slate-500 uppercase font-bold tracking-widest leading-tight">
                                Category Sorting</div>
                        </div>
                    </div>
                </a>

                <!-- Pillar 4: Settings -->
                <a href="{{ route('admin.settings.index') }}"
                    class="bg-slate-900 p-4 rounded-2xl shadow-sm border border-white/5 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group border-b-4 border-b-orange-500">
                    <div class="flex items-center gap-4">
                        @php $isMaintenance = \App\Models\Setting::get('maintenance_mode', '0') == '1'; @endphp
                        <div
                            class="w-12 h-12 rounded-xl {{ $isMaintenance ? 'bg-red-500/10' : 'bg-orange-500/10' }} flex items-center justify-center text-2xl group-hover:bg-gray-700 group-hover:text-white transition-all duration-300">
                            {{ $isMaintenance ? '⚠️' : '⚙️' }}
                        </div>
                        <div>
                            <div class="text-sm font-black text-white uppercase tracking-tight">System Configuration
                            </div>
                            <div
                                class="text-[10px] {{ $isMaintenance ? 'text-red-500 font-black' : 'text-slate-400' }} uppercase font-bold tracking-widest leading-tight">
                                {{ $isMaintenance ? 'Maintenance Active' : 'Deadlines & Config' }}
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Master Archive -->
            <div class="bg-slate-900 overflow-hidden shadow-sm sm:rounded-lg border border-white/5">
                <div class="p-6 text-white">
                    <h3 class="text-lg font-bold mb-4">Master Project Directory</h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-white/5">
                            <thead class="bg-slate-800">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">
                                        ID</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider min-w-[500px]">
                                        Title</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">
                                        Adviser</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-slate-900 divide-y divide-white/5">
                                @foreach($projects as $project)
                                                            <tr>
                                                                <td class="px-4 py-4 whitespace-nowrap text-sm text-slate-500">{{ $project->id }}
                                                                </td>
                                                                <td class="px-4 py-4">
                                                                    <div class="text-sm font-medium text-white whitespace-nowrap">
                                                                        {{ $project->title }}</div>
                                                                    <div class="flex items-center gap-2 mt-1">
                                                                        <span
                                                                            class="text-[10px] bg-slate-800 text-slate-400 px-1.5 py-0.5 rounded font-bold uppercase tracking-tighter border border-white/5">{{ $project->year }}</span>
                                                                        <span class="text-[10px] text-slate-500 font-medium">
                                                                            <i class="far fa-calendar-alt mr-1"></i>
                                                                            {{ $project->created_at->format('m-d-Y') }}
                                                                        </span>
                                                                    </div>
                                                                </td>
                                                                <td class="px-4 py-4 whitespace-nowrap text-sm text-slate-400">
                                                                    {{ $project->adviser->name ?? 'None' }}</td>
                                                                <td class="px-4 py-4 whitespace-nowrap">
                                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                                        {{ $project->status === 'approved' || $project->status === 'published' ? 'bg-green-500/10 text-green-500' :
                                    ($project->status === 'archived' ? 'bg-slate-800 text-slate-400' :
                                        'bg-yellow-500/10 text-yellow-500') }} border border-current">
                                                                        {{ ucfirst($project->status) }}
                                                                    </span>
                                                                </td>
                                                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                                                    <a href="{{ route('projects.show', $project) }}"
                                                                        class="text-indigo-400 hover:text-indigo-300 mr-3">View</a>
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