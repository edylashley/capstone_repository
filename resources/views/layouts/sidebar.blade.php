@auth
<!-- Desktop Sidebar (hidden on mobile) -->
<aside class="hidden lg:flex flex-col text-white shadow-2xl bg-slate-950 fixed top-0 left-0 z-40 transition-transform duration-300 ease-in-out border-r border-white/5"
       :class="{ '-translate-x-full': sidebarCollapsed, 'translate-x-0': !sidebarCollapsed }"
       style="width: 280px; height: 100vh; min-height: 100vh;">
    @include('layouts.sidebar-content')
</aside>

<!-- Mobile Sidebar (Off-canvas) -->
<div x-show="sidebarOpen"
     class="fixed inset-0 z-50 lg:hidden flex"
     style="display: none;">

    <!-- Dark overlay -->
    <div x-show="sidebarOpen"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm"></div>

    <!-- Sidebar content -->
    <div x-show="sidebarOpen"
         x-transition:enter="transition ease-in-out duration-300 transform"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in-out duration-300 transform"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full"
         class="relative flex flex-col max-w-[280px] w-full h-full bg-slate-950 text-white overflow-hidden">

        <div class="absolute top-4 right-4 z-10">
            <button @click="sidebarOpen = false" class="p-2 text-white hover:bg-white/10 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        @include('layouts.sidebar-content')
    </div>
</div>
@endauth
