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
        <div x-data="dashboardPolling()" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Integrated Header --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-2">
                <div>
                    <div class="flex items-center gap-4">
                        <h2
                            class="font-black text-4xl text-gray-900 dark:text-white uppercase tracking-tighter leading-none">
                            Dashboard</h2>
                        
                        {{-- Security Engine Status Pill --}}
                        <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-full bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10 shadow-sm transition-all hover:shadow-md">
                            <div class="relative flex h-2 w-2">
                                @php
                                    $statusColor = match($stats['security_status']) {
                                        'online', 'ready' => 'bg-emerald-500',
                                        'offline' => 'bg-rose-500',
                                        default => 'bg-amber-500'
                                    };
                                @endphp
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $statusColor }} opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 {{ $statusColor }}"></span>
                            </div>
                            <span class="text-[9px] font-black uppercase tracking-[0.1em] text-gray-500 dark:text-slate-400">
                                Security Engine: 
                                <span class="{{ $stats['security_status'] === 'offline' ? 'text-rose-500' : 'text-emerald-500' }}">
                                    {{ strtoupper($stats['security_status']) }}
                                </span>
                            </span>
                        </div>
                    </div>
                    <p
                        class="text-[10px] text-blue-600 dark:text-indigo-400 uppercase tracking-[0.4em] font-black mt-3 opacity-80">
                        System Management & Monitoring</p>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div id="dashboard-stats" class="grid grid-cols-2 stats-grid gap-4">
                {{-- Total Projects --}}
                <div
                    class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-gray-200 dark:border-white/5 border-b-4 border-b-blue-500 shadow-sm dark:shadow-lg dark:shadow-blue-500/5 transition-all hover:shadow-md dark:hover:shadow-blue-500/10 group relative overflow-hidden">
                    <div
                        class="absolute -right-4 -top-4 w-16 h-16 bg-blue-50 dark:bg-blue-500/5 rounded-full blur-2xl group-hover:bg-blue-100 dark:group-hover:bg-blue-500/10 transition-all">
                    </div>
                    <div
                        class="text-gray-500 dark:text-slate-500 text-[11px] uppercase font-black tracking-widest mb-2">
                        Total Projects
                    </div>
                    <div
                        class="text-3xl font-black text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors uppercase tracking-widest">
                        {{ $stats['total_projects'] }}
                    </div>
                </div>

                {{-- Processing --}}
                <div
                    class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-gray-200 dark:border-white/5 border-b-4 border-b-yellow-500 shadow-sm dark:shadow-lg dark:shadow-yellow-500/5 transition-all hover:shadow-md dark:hover:shadow-yellow-500/10 group relative overflow-hidden">
                    <div
                        class="absolute -right-4 -top-4 w-16 h-16 bg-yellow-50 dark:bg-yellow-500/5 rounded-full blur-2xl group-hover:bg-yellow-100 dark:group-hover:bg-yellow-500/10 transition-all">
                    </div>
                    <div
                        class="text-gray-500 dark:text-slate-500 text-[11px] uppercase font-black tracking-widest mb-2">
                        Processing</div>
                    <div
                        class="text-3xl font-black text-yellow-600 dark:text-yellow-500 group-hover:text-yellow-500 dark:group-hover:text-yellow-400 transition-colors uppercase tracking-widest">
                        {{ $stats['pending_projects'] }}
                    </div>
                </div>

                {{-- Confirmed --}}
                <div
                    class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-gray-200 dark:border-white/5 border-b-4 border-b-emerald-500 shadow-sm dark:shadow-lg dark:shadow-emerald-500/5 transition-all hover:shadow-md dark:hover:shadow-emerald-500/10 group relative overflow-hidden">
                    <div
                        class="absolute -right-4 -top-4 w-16 h-16 bg-emerald-50 dark:bg-emerald-500/5 rounded-full blur-2xl group-hover:bg-emerald-100 dark:group-hover:bg-emerald-500/10 transition-all">
                    </div>
                    <div
                        class="text-gray-500 dark:text-slate-500 text-[11px] uppercase font-black tracking-widest mb-2">
                        Confirmed</div>
                    <div
                        class="text-3xl font-black text-emerald-600 dark:text-emerald-500 group-hover:text-emerald-500 dark:group-hover:text-emerald-400 transition-colors uppercase tracking-widest">
                        {{ $stats['approved_projects'] }}
                    </div>
                </div>

                {{-- Users --}}
                <div
                    class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-gray-200 dark:border-white/5 border-b-4 border-b-indigo-500 shadow-sm dark:shadow-lg dark:shadow-indigo-500/5 transition-all hover:shadow-md dark:hover:shadow-indigo-500/10 group relative overflow-hidden">
                    <div
                        class="absolute -right-4 -top-4 w-16 h-16 bg-indigo-50 dark:bg-indigo-500/5 rounded-full blur-2xl group-hover:bg-indigo-100 dark:group-hover:bg-indigo-500/10 transition-all">
                    </div>
                    <div
                        class="text-gray-500 dark:text-slate-500 text-[11px] uppercase font-black tracking-widest mb-2">
                        Users</div>
                    <div
                        class="text-3xl font-black text-indigo-600 dark:text-indigo-500 group-hover:text-indigo-500 dark:group-hover:text-indigo-400 transition-colors uppercase tracking-widest">
                        {{ $stats['total_users'] }}
                    </div>
                </div>

                {{-- Storage --}}
                <div
                    class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-gray-200 dark:border-white/5 border-b-4 border-b-rose-500 shadow-sm dark:shadow-lg dark:shadow-rose-500/5 transition-all hover:shadow-md dark:hover:shadow-rose-500/10 group relative overflow-hidden col-span-2 storage-card">
                    <div
                        class="absolute -right-4 -top-4 w-16 h-16 bg-rose-50 dark:bg-rose-500/5 rounded-full blur-2xl group-hover:bg-rose-100 dark:group-hover:bg-rose-500/10 transition-all">
                    </div>
                    <div
                        class="text-gray-500 dark:text-slate-500 text-[11px] uppercase font-black tracking-widest mb-2">
                        Repository Storage
                    </div>
                    <div
                        class="text-3xl font-black text-rose-600 dark:text-rose-500 group-hover:text-rose-500 dark:group-hover:text-rose-400 transition-colors uppercase tracking-widest">
                        {{ $stats['total_storage'] }}
                    </div>
                </div>
            </div>

            <!-- System Analytics & Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
                <!-- Recent Activity Feed (1/2) -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between px-2">
                        <h3
                            class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest flex items-center gap-2">
                            <span class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></span>
                            Recent Activity
                        </h3>
                        <a href="{{ route('admin.logs') }}"
                            class="text-[10px] font-black text-indigo-500 hover:text-indigo-600 uppercase tracking-widest transition-colors">View
                            All Logs</a>
                    </div>

                    <div id="dashboard-activity"
                        class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-200 dark:border-white/5 shadow-sm overflow-hidden">
                        <div class="divide-y divide-gray-100 dark:divide-white/5">
                            @forelse($recentActivities as $activity)
                                @php
                                    $link = null;
                                    if ($activity->action === 'account_request' && $activity->user_id) {
                                        if (\App\Models\User::where('id', $activity->user_id)->exists()) {
                                            $link = route('admin.users.show', $activity->user_id);
                                        }
                                    } elseif (in_array($activity->action, ['upload_project', 'project_submitted', 'security_threat_blocked']) && $activity->target_id) {
                                        if (\App\Models\Project::where('id', $activity->target_id)->exists()) {
                                            $link = route('projects.show', $activity->target_id);
                                        }
                                    }
                                @endphp
                                @if($link)
                                    <a href="{{ $link }}"
                                        class="p-3 hover:bg-gray-50 dark:hover:bg-slate-800/30 transition-colors group block cursor-pointer">
                                @else
                                        <div
                                            class="p-3 hover:bg-gray-50 dark:hover:bg-slate-800/30 transition-colors group block cursor-default">
                                    @endif
                                        <div class="flex items-start gap-4">
                                            <div
                                                class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-slate-800 flex items-center justify-center text-lg shadow-inner">
                                                @php
                                                    $icon = match ($activity->action) {
                                                        'login' => '🔑',
                                                        'logout' => '🚪',
                                                        'project_created', 'project_submitted', 'upload_project' => '📄',
                                                        'project_approved', 'project_published' => '✅',
                                                        'project_returned' => '↩️',
                                                        'account_request' => '👤',
                                                        'account_approved' => '🎉',
                                                        'account_denied' => '🚫',
                                                        'security_scan', 'security_threat_blocked' => '🛡️',
                                                        'file_blocked' => '❌',
                                                        default => '📝'
                                                    };
                                                @endphp
                                                {{ $icon }}
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between gap-2 mb-0.5">
                                                    <div class="flex items-center gap-2 truncate">
                                                        <p
                                                            class="text-[13px] font-black text-gray-900 dark:text-white group-hover:text-indigo-500 transition-colors truncate">
                                                            {{ $activity->user->name ?? ($activity->meta['name'] ?? ($activity->meta['title'] ?? 'User')) }}
                                                        </p>
                                                        @php
                                                            $statusLabel = null;
                                                            if ($activity->action === 'account_request' && $activity->user) {
                                                                $isApproved = $activity->user->is_active;
                                                                $statusLabel = $isApproved
                                                                    ? ['text' => 'Approved', 'class' => 'bg-emerald-500/10 text-emerald-600']
                                                                    : ['text' => 'Pending', 'class' => 'bg-amber-500/10 text-amber-600'];
                                                            } elseif (in_array($activity->action, ['upload_project', 'project_submitted'])) {
                                                                $project = \App\Models\Project::find($activity->target_id);
                                                                if ($project) {
                                                                    $statusLabel = match ($project->status) {
                                                                        'pending' => ['text' => 'Pending', 'class' => 'bg-amber-500/10 text-amber-600'],
                                                                        'published', 'approved' => ['text' => 'Approved', 'class' => 'bg-emerald-500/10 text-emerald-600'],
                                                                        'returned' => ['text' => 'Returned', 'class' => 'bg-indigo-500/10 text-indigo-600'],
                                                                        default => ['text' => ucfirst($project->status), 'class' => 'bg-gray-500/10 text-gray-600']
                                                                    };
                                                                }
                                                            } elseif ($activity->action === 'security_threat_blocked') {
                                                                $statusLabel = ['text' => 'Blocked', 'class' => 'bg-rose-500/10 text-rose-600'];
                                                            }
                                                        @endphp
                                                        @if($statusLabel)
                                                            <span
                                                                class="text-[8px] font-black uppercase tracking-widest px-1.5 py-0.5 rounded {{ $statusLabel['class'] }}">
                                                                {{ $statusLabel['text'] }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <span
                                                        class="text-[9px] font-black text-gray-400 dark:text-slate-600 uppercase tracking-tighter flex-shrink-0">
                                                        {{ $activity->created_at->diffForHumans() }}
                                                    </span>
                                                </div>
                                                <p class="text-xs text-gray-500 dark:text-slate-400 leading-relaxed">
                                                    {{-- Basic description mapping --}}
                                                    @php
                                                        $description = match ($activity->action) {
                                                            'login' => 'Signed into the administrative portal.',
                                                            'project_submitted', 'upload_project' => 'Submitted a new project: ' . ($activity->meta['title'] ?? 'Untitled'),
                                                            'project_published' => 'Published project: ' . ($activity->meta['title'] ?? 'Untitled'),
                                                            'project_returned' => 'Returned project for revisions.',
                                                            'account_request' => 'Registered a new student account.',
                                                            'account_approved' => 'Approved account for ' . ($activity->meta['name'] ?? 'User'),
                                                            'account_denied' => 'Denied registration request for ' . ($activity->meta['name'] ?? 'User'),
                                                            'project_approved' => 'Approved project: ' . ($activity->meta['title'] ?? 'Untitled'),
                                                            'security_scan' => 'Completed a security integrity check.',
                                                            'file_blocked', 'security_threat_blocked' => 'Malicious file upload attempt (System Blocked).',
                                                            default => ucfirst(str_replace('_', ' ', $activity->action))
                                                        };
                                                    @endphp
                                                    {{ $description }}
                                                </p>
                                            </div>
                                        </div>
                                        @if($link)
                                        </a> @else
                                </div> @endif
                            @empty
                            <div class="p-12 text-center">
                                <p class="text-xs font-black text-gray-400 uppercase tracking-widest">No recent activity
                                    found.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Analytics Charts (1/2) -->
            <div class="grid grid-cols-1 gap-6">
                <!-- Program Distribution -->
                <div
                    class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-gray-200 dark:border-white/5 shadow-sm">
                    <h3
                        class="text-[10px] font-black text-gray-500 dark:text-slate-400 uppercase tracking-[0.2em] mb-6">
                        Programs</h3>
                    <div class="relative h-48">
                        <canvas id="programChart"></canvas>
                    </div>
                </div>

                <!-- Top Specializations -->
                <div
                    class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-gray-200 dark:border-white/5 shadow-sm">
                    <h3
                        class="text-[10px] font-black text-gray-500 dark:text-slate-400 uppercase tracking-[0.2em] mb-6">
                        Project Categories</h3>
                    <div class="relative h-64">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>


        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const isDark = document.documentElement.classList.contains('dark');
                    const textColor = isDark ? '#94a3b8' : '#64748b';

                    // Real data
                    const programLabels = {!! json_encode(array_keys($programStats)) !!};
                    const programData = {!! json_encode(array_values($programStats)) !!};
                    const categoryLabels = {!! json_encode(array_keys($categoryStats)) !!};
                    const categoryData = {!! json_encode(array_values($categoryStats)) !!};

                    // Program Distribution Chart — start with zeros
                    const ctxProgram = document.getElementById('programChart').getContext('2d');
                    const programChart = new Chart(ctxProgram, {
                        type: 'doughnut',
                        data: {
                            labels: programLabels,
                            datasets: [{
                                data: programData.map(() => 0),
                                backgroundColor: ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
                                borderWidth: 0,
                                hoverOffset: 10
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: {
                                animateRotate: true,
                                animateScale: true,
                                duration: 1500,
                                easing: 'easeOutQuart'
                            },
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        color: textColor,
                                        font: { size: 10, weight: 'bold' },
                                        boxWidth: 10,
                                        padding: 15
                                    }
                                }
                            },
                            cutout: '75%'
                        }
                    });

                    // Specialization Distribution Chart — start with zeros
                    const ctxCategory = document.getElementById('categoryChart').getContext('2d');
                    const categoryChart = new Chart(ctxCategory, {
                        type: 'bar',
                        data: {
                            labels: categoryLabels,
                            datasets: [{
                                label: 'Projects',
                                data: categoryData.map(() => 0),
                                backgroundColor: isDark ? 'rgba(99, 102, 241, 0.8)' : 'rgba(79, 70, 229, 0.9)',
                                hoverBackgroundColor: '#6366f1',
                                borderRadius: 8,
                                barThickness: 20
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: {
                                duration: 1500,
                                easing: 'easeOutQuart'
                            },
                            layout: {
                                padding: {
                                    left: 10
                                }
                            },
                            scales: {
                                x: {
                                    grid: {
                                        display: true,
                                        color: isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)',
                                        drawBorder: false
                                    },
                                    ticks: {
                                        display: true,
                                        color: textColor,
                                        font: { size: 10, weight: '700' },
                                        precision: 0,
                                        padding: 10
                                    }
                                },
                                y: {
                                    grid: { display: false },
                                    ticks: {
                                        color: textColor,
                                        font: { size: 10, weight: '700' },
                                        padding: 10,
                                        callback: function(value) {
                                            const label = this.getLabelForValue(value);
                                            const maxLen = window.innerWidth < 768 ? 15 : 30;
                                            return label.length > maxLen ? label.substring(0, maxLen) + '…' : label;
                                        }
                                    }
                                }
                            },
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: isDark ? '#1e293b' : '#ffffff',
                                    titleColor: isDark ? '#ffffff' : '#1e293b',
                                    bodyColor: isDark ? '#ffffff' : '#1e293b',
                                    borderColor: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)',
                                    borderWidth: 1,
                                    padding: 12,
                                    displayColors: false,
                                    callbacks: {
                                        label: function (context) {
                                            return ' Total Projects: ' + context.parsed.x;
                                        }
                                    }
                                }
                            }
                        }
                    });

                    // Inject real data after a short delay — this triggers a visible animation
                    setTimeout(function () {
                        programChart.data.datasets[0].data = programData;
                        programChart.update();

                        categoryChart.data.datasets[0].data = categoryData;
                        categoryChart.update();
                    }, 300);
                });
            </script>
        @endpush
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('dashboardPolling', () => ({
                pollInterval: null,
                init() { this.startPolling(); },
                startPolling() {
                    this.pollInterval = setInterval(() => { this.fetchUpdate(); }, 10000); // 10 seconds for dashboard
                },
                async fetchUpdate() {
                    try {
                        const response = await fetch(window.location.href, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        if (!response.ok) return;
                        const html = await response.text();
                        const doc = new DOMParser().parseFromString(html, 'text/html');
                        ['dashboard-stats', 'dashboard-activity'].forEach(id => {
                            const newEl = doc.getElementById(id);
                            const currentEl = document.getElementById(id);
                            if (newEl && currentEl && currentEl.innerHTML !== newEl.innerHTML) {
                                currentEl.innerHTML = newEl.innerHTML;
                            }
                        });
                    } catch (e) { console.error('Dashboard polling failed:', e); }
                }
            }));
        });
    </script>
</x-app-layout>