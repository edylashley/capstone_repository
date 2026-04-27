<footer class="text-gray-900 dark:text-white py-6 bg-white dark:bg-slate-950 border-t border-gray-200 dark:border-transparent transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex flex-col md:flex-row justify-between items-center text-center md:text-left gap-4">
            <div class="flex-1">
                <h3 class="font-black text-xs uppercase tracking-widest">
                    {{ \App\Models\Setting::get('repository_name', 'CSIT Capstone Repository') }}
                </h3>
                <p class="text-[10px] text-gray-500 dark:text-slate-400 font-bold uppercase mt-1">Capstone Research Library</p>
            </div>

            <div class="flex-1 flex flex-col items-center">
                <div class="text-xs font-black uppercase tracking-widest">Negros Oriental State University</div>
                <div class="text-[10px] text-gray-500 dark:text-slate-400 mt-1 uppercase font-bold">Kagawasan Avenue, Dumaguete City,
                    Negros Oriental
                </div>
            </div>

            <div class="flex-1 flex flex-col items-center md:items-end">
                <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-slate-400">
                    &copy; {{ date('Y') }} All Rights Reserved
                </div>
            </div>
        </div>
    </div>
</footer>