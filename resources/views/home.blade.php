@extends('layouts.app')

@section('content')
    <div class="min-h-screen w-full pb-12 bg-gradient-to-br from-gray-300 via-gray-100 to-gray-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">

            <!-- Hero Section -->
            <section
                class="relative rounded-3xl overflow-hidden shadow-2xl mb-12 bg-gradient-to-br from-indigo-600 via-blue-600 to-purple-700">
                <!-- Decorative background elements -->
                <div class="absolute inset-0 opacity-10">
                    <div
                        class="absolute top-0 left-0 w-96 h-96 bg-white rounded-full -translate-x-1/2 -translate-y-1/2 blur-3xl">
                    </div>
                    <div
                        class="absolute bottom-0 right-0 w-96 h-96 bg-teal-300 rounded-full translate-x-1/2 translate-y-1/2 blur-3xl">
                    </div>
                </div>

                <div class="relative grid lg:grid-cols-2 gap-8 lg:gap-20 p-8 md:p-16 items-center">
                    <div class="flex flex-col md:flex-row items-center justify-center lg:justify-start lg:items-start gap-4">
                        <div
                            class="flex-shrink-0 w-32 h-32 md:w-48 md:h-48 lg:w-56 lg:h-56 flex items-center justify-center overflow-hidden rounded-full shadow-2xl bg-white animate-float border-4 border-white/20">
                            <img src="{{ asset('images/system-logo.jpg') }}" alt="NORSU Logo"
                                class="w-full h-full object-cover transform scale-150 hover:scale-110 transition-transform duration-500 ease-in-out animate-pop-in" />
                        </div>
                        <div class="text-center lg:text-left flex-shrink-0">
                            <h1
                                class="text-4xl md:text-5xl lg:text-6xl font-black tracking-tight leading-tight text-white mb-2 whitespace-nowrap md:whitespace-normal animate-fade-in-up">
                                <span class="text-teal-300 drop-shadow-lg">CSIT</span><br>
                                <span class="text-blue-100">Capstone</span><br>
                                <span class="text-blue-100">Repository</span>
                            </h1>
                        </div>
                    </div>

                    <div class="space-y-6 text-white text-center lg:text-left animate-fade-in-up delay-200">
                        <div>
                            <p class="text-blue-200 font-bold text-xs uppercase tracking-widest mb-3">Institutional Platform
                            </p>
                            <h2 class="text-2xl md:text-3xl font-bold text-white leading-tight mb-4">
                                Centralized Web-Based Management System for Capstone Projects in NORSU
                            </h2>
                        </div>

                        <p class="text-blue-100 text-lg leading-relaxed">
                            A centralized digital archive for storing, verifying, and preserving student capstone research.
                            This platform ensures long-term accessibility of project manuscripts and source code for the
                            university community.
                        </p>

                        <!-- Global Search Bar -->
                        <div class="pt-2">
                            <style>
                                @keyframes smooth-marquee {
                                    0% {
                                        transform: translateX(0%);
                                    }

                                    100% {
                                        transform: translateX(-50%);
                                    }
                                }

                                .animate-smooth-marquee {
                                    display: inline-block;
                                    animation: smooth-marquee 15s linear infinite;
                                }
                            </style>
                            <form action="{{ route('projects.index') ?? '#' }}" method="GET">
                                <div
                                    class="relative w-full bg-white border border-gray-200 rounded-2xl shadow-xl overflow-hidden flex items-center ring-1 ring-gray-100 focus-within:ring-2 focus-within:ring-indigo-500 focus-within:shadow-indigo-500/10 transition-all duration-300">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-5 pointer-events-none z-10">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>

                                    <!-- Smooth Visual Marquee -->
                                    <div id="marquee-placeholder"
                                        class="absolute inset-y-0 flex items-center overflow-hidden pointer-events-none transition-opacity duration-300"
                                        style="left: 3.5rem; right: 7rem; mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent); -webkit-mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);">
                                        <div
                                            class="whitespace-nowrap animate-smooth-marquee text-sm text-gray-400 font-medium">
                                            Find past projects: Search by capstone titles, authors, faculty advisers, or
                                            keywords... &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Find
                                            projects: Search by capstone titles, authors, faculty advisers, or keywords...
                                        </div>
                                    </div>

                                    <input type="search" id="dynamic-search" name="keyword"
                                        class="block w-full py-5 pl-14 pr-[140px] text-sm text-gray-900 bg-transparent outline-none focus:ring-0 border-0 relative z-20"
                                        placeholder="" required>
                                    <button type="button" id="clear-search-btn"
                                        class="absolute text-gray-400 hover:text-gray-600 hidden z-30 focus:outline-none"
                                        style="right: 120px; top: 50%; transform: translateY(-50%);" title="Clear search">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                    <button type="submit"
                                        class="text-white absolute right-2.5 bg-indigo-600 hover:bg-indigo-700 hover:scale-105 active:scale-95 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-black rounded-xl text-[10px] md:text-xs px-6 py-2.5 transition-all duration-200 z-30 uppercase tracking-widest shadow-lg shadow-indigo-600/20">
                                        Search
                                    </button>
                                </div>
                            </form>

                            <div
                                class="flex flex-wrap items-center justify-center lg:justify-between gap-4 pt-6 mt-6 border-t border-white/20">
                                <!-- Browse Button on Left -->
                                <a href="{{ route('projects.index') }}"
                                    class="inline-flex items-center px-5 py-2.5 bg-white/10 hover:bg-white/20 text-white text-sm font-medium rounded-lg backdrop-blur-sm border border-white/20 transition-colors duration-200 shadow-sm hover:shadow-lg">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                        </path>
                                    </svg>
                                    Browse All Projects
                                </a>

                                <!-- Auth Buttons on Right -->
                                <div class="flex flex-wrap gap-3">
                                    <a href="{{ route('login') }}"
                                        class="px-6 py-2.5 bg-white text-indigo-600 rounded-lg font-bold hover:bg-blue-50 shadow transition-all duration-200 text-sm">
                                        Login
                                    </a>
                                    <a href="{{ route('register') }}"
                                        class="px-6 py-2.5 bg-indigo-500/50 text-white border border-indigo-300 rounded-lg font-bold hover:bg-indigo-500 shadow transition-all duration-200 text-sm">
                                        Register
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
            </section>

            <!-- How to Use System Guide -->
            <h2
                class="text-2xl font-bold text-gray-800 mb-6 text-center uppercase tracking-widest animate-fade-in-up delay-300">
                System Guide </h2>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12 animate-fade-in-up delay-500">
                <!-- Step 1 -->
                <div
                    class="group bg-white p-8 rounded-2xl border border-indigo-100 hover:border-indigo-300 hover:shadow-xl hover:shadow-indigo-100 transition-all duration-300 transform hover:-translate-y-1 shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-30 font-black text-6xl text-indigo-500 z-0">01</div>
                    <div class="relative z-10">
                        <div
                            class="w-14 h-14 bg-indigo-100 text-indigo-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">1. Check Your Idea</h3>
                        <p class="text-gray-600 text-sm leading-relaxed">
                            Anyone can use the search bar to check if their capstone idea has already been done by a
                            previous group.
                        </p>
                    </div>
                </div>

                <!-- Step 2 -->
                <div
                    class="group bg-white p-8 rounded-2xl border border-indigo-100 hover:border-indigo-300 hover:shadow-xl hover:shadow-indigo-100 transition-all duration-300 transform hover:-translate-y-1 shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-30 font-black text-6xl text-purple-500 z-0">02</div>
                    <div class="relative z-10">
                        <div
                            class="w-14 h-14 bg-purple-100 text-purple-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-purple-600 group-hover:text-white transition-colors duration-300">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0l-4 4m4-4v12"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">2. Upload Your Work</h3>
                        <p class="text-gray-600 text-sm leading-relaxed">
                            Graduating students log in to upload their final capstone project and fill out the details about
                            their system.
                        </p>
                    </div>
                </div>

                <!-- Step 3 -->
                <div
                    class="group bg-white p-8 rounded-2xl border border-indigo-100 hover:border-indigo-300 hover:shadow-xl hover:shadow-indigo-100 transition-all duration-300 transform hover:-translate-y-1 shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-30 font-black text-6xl text-teal-500 z-0">03</div>
                    <div class="relative z-10">
                        <div
                            class="w-14 h-14 bg-teal-100 text-teal-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-teal-600 group-hover:text-white transition-colors duration-300">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">3. Teacher Approval</h3>
                        <p class="text-gray-600 text-sm leading-relaxed">
                            Advisers look over the submitted projects to make sure everything is correct before they approve
                            them for the library.
                        </p>
                    </div>
                </div>

                <!-- Step 4 -->
                <div
                    class="group bg-white p-8 rounded-2xl border border-indigo-100 hover:border-indigo-300 hover:shadow-xl hover:shadow-indigo-100 transition-all duration-300 transform hover:-translate-y-1 shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-30 font-black text-6xl text-blue-500 z-0">04</div>
                    <div class="relative z-10">
                        <div
                            class="w-14 h-14 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">4. Explore Projects</h3>
                        <p class="text-gray-600 text-sm leading-relaxed">
                            Once approved, anyone can read the summary of the project. But you must be logged in to download
                            the full files.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Recent Projects Section -->
            @if(isset($projects) && $projects->count() > 0)
                <section class="bg-white rounded-2xl shadow-lg p-8 mb-12 border border-indigo-100">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Recent Projects</h2>
                        <a href="{{ route('projects.index') }}"
                            class="text-indigo-600 hover:text-indigo-700 font-semibold text-sm">View All →</a>
                    </div>
                    <div class="grid md:grid-cols-3 gap-6">
                        @foreach($projects->take(3) as $project)
                            <div
                                class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-xl p-6 border border-indigo-100 hover:shadow-md transition-shadow">
                                <h3 class="font-bold text-gray-900 mb-2 line-clamp-2">{{ $project->title }}</h3>
                                <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ Str::limit($project->abstract, 100) }}</p>
                                <div class="flex items-center justify-between text-xs text-gray-500">
                                    <span>{{ $project->year }}</span>
                                    @if($project->adviser)
                                        <span>{{ $project->adviser->name }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Force replay of logo pop-in animation
            const logo = document.querySelector('.animate-pop-in');
            if (logo) {
                logo.classList.remove('animate-pop-in');
                void logo.offsetWidth; // Trigger reflow
                logo.classList.add('animate-pop-in');
            }

            // Smooth CSS Marquee Placeholder behavior
            const searchInput = document.getElementById('dynamic-search');
            const marqueePlaceholder = document.getElementById('marquee-placeholder');
            const clearSearchBtn = document.getElementById('clear-search-btn');

            if (searchInput && marqueePlaceholder) {
                const toggleMarquee = () => {
                    if (searchInput.value.length > 0 || document.activeElement === searchInput) {
                        marqueePlaceholder.style.opacity = '0';
                    } else {
                        marqueePlaceholder.style.opacity = '1';
                    }
                    if (clearSearchBtn) {
                        if (searchInput.value.length > 0) {
                            clearSearchBtn.classList.remove('hidden');
                        } else {
                            clearSearchBtn.classList.add('hidden');
                        }
                    }
                };

                searchInput.addEventListener('focus', toggleMarquee);
                searchInput.addEventListener('blur', toggleMarquee);
                searchInput.addEventListener('input', toggleMarquee);

                if (clearSearchBtn) {
                    clearSearchBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        searchInput.value = '';
                        toggleMarquee();
                        searchInput.focus();
                    });
                }

                // Set initial state
                toggleMarquee();
            }
        });
    </script>
@endsection