<footer class="text-white py-6" style="background-color: #111827;">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex flex-col md:flex-row justify-between items-center text-center md:text-left gap-4">
            <div class="flex-1">
                <h3 class="font-black text-xs uppercase tracking-widest">
                    {{ \App\Models\Setting::get('repository_name', 'CSIT Capstone Repository') }}
                </h3>
                <p class="text-[10px] text-indigo-200 font-bold uppercase mt-1">Institutional Research Library</p>
            </div>

            <div class="flex-1 flex flex-col items-center">
                <div class="text-xs font-black uppercase tracking-widest">Negros Oriental State University</div>
                <div class="text-[10px] text-indigo-200 mt-1 uppercase font-bold">Kagawasan Avenue, Dumaguete City,
                    Negros Oriental
                </div>
            </div>

            <div class="flex-1 flex flex-col items-center md:items-end">
                <div class="text-[10px] font-bold uppercase tracking-widest text-indigo-200">
                    &copy; {{ date('Y') }} All Rights Reserved
                </div>
            </div>
        </div>
    </div>
</footer>