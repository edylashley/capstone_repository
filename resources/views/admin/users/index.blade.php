<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            {{-- Integrated Header --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-2">
                <div>
                    <h2 class="font-black text-4xl text-white uppercase tracking-tighter leading-none">User Accounts</h2>
                    <p class="text-[10px] text-indigo-400 uppercase tracking-[0.4em] font-black mt-3 opacity-80">Identity Management & Access Control</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-slate-900/50 text-slate-400 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-slate-800 hover:text-white transition-all shadow-inner border border-white/5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Dashboard
                    </a>
                    <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-900/20 transform hover:-translate-y-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                        Create Account
                    </a>
                </div>
            </div>
            
            <div class="bg-slate-900 overflow-hidden shadow-sm sm:rounded-lg border border-white/5">
                <div class="p-6 text-white">




                    @if (session('error'))
                        <div class="bg-red-600 border border-red-700 text-white px-4 py-3 rounded-xl relative mb-4 shadow-lg shadow-red-100/50 flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-300"
                            role="alert">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="block sm:inline font-bold text-sm">{{ session('error') }}</span>
                        </div>
                    @endif

                    <!-- Filters Section -->
                    <div
                        class="mb-6 flex flex-wrap items-center justify-between gap-4 bg-slate-800 p-4 rounded-2xl border border-white/5">
                        <div class="flex items-center gap-4">
                            <form action="{{ route('admin.users.index') }}" method="GET"
                                class="flex flex-wrap items-center gap-4">
                                <div class="flex items-center gap-2">
                                    <label for="role_filter"
                                        class="text-xs font-black uppercase tracking-widest text-slate-400">Role:</label>
                                    <select name="role" id="role_filter" onchange="this.form.submit()"
                                        class="bg-slate-900 border-white/10 text-white text-xs font-bold rounded-xl focus:ring-indigo-500 focus:border-indigo-500 py-2 pl-3 pr-8 transition-all">
                                        <option value="" {{ request('role') == '' ? 'selected' : '' }}>All Roles</option>
                                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admins
                                        </option>
                                        <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>
                                            Students</option>
                                    </select>
                                </div>

                                <div class="flex items-center gap-2">
                                    <label for="program_filter"
                                        class="text-xs font-black uppercase tracking-widest text-slate-400">Program:</label>
                                    <select name="program" id="program_filter" onchange="this.form.submit()"
                                        class="bg-slate-900 border-white/10 text-white text-xs font-bold rounded-xl focus:ring-indigo-500 focus:border-indigo-500 py-2 pl-3 pr-8 transition-all">
                                        <option value="" {{ request('program') == '' ? 'selected' : '' }}>All Programs
                                        </option>
                                        <option value="BSInT" {{ request('program') == 'BSInT' ? 'selected' : '' }}>BSInT
                                        </option>
                                        <option value="Com-Sci" {{ request('program') == 'Com-Sci' ? 'selected' : '' }}>
                                            Com-Sci</option>
                                    </select>
                                </div>

                                @if(request('role') || request('program'))
                                    <a href="{{ route('admin.users.index') }}"
                                        class="text-[10px] font-black uppercase text-indigo-500 hover:text-indigo-600 transition-colors">Clear
                                        Filters</a>
                                @endif
                            </form>
                        </div>

                        <div class="text-[10px] text-gray-400 font-black uppercase tracking-widest">
                            Total: {{ $users->total() }} User{{ $users->total() != 1 ? 's' : '' }}
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-white/5">
                            <thead class="bg-slate-800">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">
                                        Name / ID</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">
                                        Email</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">
                                        Role</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-slate-400 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-slate-900 divide-y divide-white/5">
                                @foreach($users as $user)
                                                            <tr x-data="{}" @click="window.location.href = '{{ route('admin.users.show', $user) }}'"
                                                                class="group cursor-pointer hover:bg-gray-950 dark:hover:bg-white/[0.05] transition-all duration-300 hover:shadow-2xl hover:shadow-black/20">
                                                                <td class="px-6 py-4 whitespace-nowrap">
                                                                    <div class="font-bold text-white group-hover:text-indigo-400 transition-colors">
                                                                        {{ $user->name }}
                                                                    </div>
                                                                    @if($user->isStudent())
                                                                        <div class="flex items-center gap-2 mt-1">
                                                                            <div
                                                                                class="text-[10px] text-gray-400 font-mono uppercase tracking-widest leading-tight group-hover:text-gray-400 transition-colors">
                                                                                {{ $user->student_id ?? 'N/A' }}
                                                                            </div>
                                                                            @if($user->program)
                                                                                <span
                                                                                    class="text-[9px] px-2 py-0.5 bg-slate-800 text-slate-300 rounded-full font-black uppercase border border-white/5 group-hover:bg-slate-700 transition-colors">
                                                                                    {{ $user->program }}
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    @endif
                                                                </td>
                                                                <td
                                                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-200 group-hover:text-gray-300 transition-colors">
                                                                    {{ $user->email }}</td>
                                                                <td class="px-6 py-4 whitespace-nowrap">
                                                                    <span class="px-3 py-1 inline-flex text-[10px] leading-5 font-black rounded-full transition-all duration-300 uppercase tracking-widest border border-white/5 shadow-inner
                                                                        {{ $user->role === 'admin' ? 'bg-red-500/20 text-red-400 group-hover:bg-red-500/30' : 'bg-indigo-500/20 text-indigo-400 group-hover:bg-indigo-500/30' }}">
                                                                        {{ $user->role }}
                                                                    </span>
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap">
                                                                    @if($user->is_active)
                                                                        <span
                                                                            class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-[10px] font-black bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 uppercase tracking-wider transition-all duration-300 group-hover:bg-emerald-500/20">
                                                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                                                            Verified
                                                                        </span>
                                                                    @else
                                                                        <span
                                                                            class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-[10px] font-black bg-amber-500/10 text-amber-400 border border-amber-500/20 uppercase tracking-wider transition-all duration-300 group-hover:bg-amber-500/20"
                                                                            title="Requires BSInT Verification">
                                                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                                                                            Pending
                                                                        </span>
                                                                    @endif
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex items-center justify-end gap-3"
                                                                    @click.stop>
                                                                    @if(!$user->is_active)
                                                                        {{-- Approval Flow --}}
                                                                        <form action="{{ route('admin.users.approve', $user) }}" method="POST"
                                                                            class="inline">
                                                                            @csrf
                                                                            <button type="submit"
                                                                                class="px-4 py-1.5 bg-emerald-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-700 shadow-lg shadow-emerald-900/20 transition-all transform hover:-translate-y-0.5">
                                                                                Approve
                                                                            </button>
                                                                        </form>
                                                                        <form action="{{ route('admin.users.reject', $user) }}" method="POST"
                                                                            class="inline"
                                                                            onsubmit="return confirm('Reject this registration? The account will be moved to Trash.');">
                                                                            @csrf
                                                                            <button type="submit"
                                                                                class="px-4 py-1.5 bg-rose-600/10 text-rose-500 border border-rose-500/20 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-rose-600 hover:text-white transition-all transform hover:-translate-y-0.5">
                                                                                Reject
                                                                            </button>
                                                                        </form>
                                                                    @else
                                                                        {{-- Standard Management Flow --}}
                                                                        <a href="{{ route('admin.users.edit', $user) }}"
                                                                            class="inline-flex items-center gap-1 px-3 py-1 bg-slate-800 text-indigo-400 hover:text-white hover:bg-indigo-600 border border-white/5 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all">
                                                                            Edit
                                                                        </a>

                                                                        @if($user->id !== auth()->id())
                                                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                                                                class="inline"
                                                                                onsubmit="return confirm('Move this account to Trash?');">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                                <button type="submit"
                                                                                    class="text-slate-500 hover:text-rose-500 font-bold uppercase text-[10px] tracking-widest transition-colors ml-2">Delete</button>
                                                                            </form>
                                                                        @endif
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