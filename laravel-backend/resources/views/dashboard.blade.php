<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>SSTP One Page Report - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>

<body class="bg-gray-100 p-6">
    <div class="max-w-6xl mx-auto">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <div class="flex items-center justify-between w-full md:w-auto">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('KPM Logo.png') }}" alt="Logo" class="h-12 w-auto">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-black text-blue-900 tracking-tight leading-none">One Page Report</h1>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Sektor Sumber dan Teknologi
                            Pendidikan</p>
                    </div>
                </div>
                <div class="text-right md:hidden ml-4">
                    <p id="currentUserDisplayMobile" class="text-xs font-bold text-gray-600 mb-1"></p>
                    <button id="logoutBtnMobile"
                        class="bg-red-50 text-red-600 border border-red-200 px-3 py-1 rounded shadow-sm text-xs font-bold hover:bg-red-100 transition whitespace-nowrap">Logout</button>
                </div>
            </div>

            <div class="flex flex-wrap gap-3 items-center">
                <div class="relative">
                    <input type="text" id="searchInput" placeholder="Search title or staff..."
                        aria-label="Search Reports"
                        class="p-2 pl-9 border rounded-lg bg-white shadow-sm outline-none focus:ring-2 focus:ring-blue-400 w-64 text-sm">
                    <span class="absolute left-3 top-2.5 opacity-30" aria-hidden="true">🔍</span>
                </div>
                <input list="unit-filter-list" id="filterUnit" aria-label="Filter by Unit" placeholder="All Units" value="All"
                    class="p-2 border rounded-lg bg-white shadow-sm outline-none focus:ring-2 focus:ring-blue-400 text-sm font-semibold w-48">
                <datalist id="unit-filter-list">
                    <option value="All">
                    <option value="Unit Dasar dan Latihan">
                    <option value="Unit Pengurusan Pusat Sumber">
                    <option value="Unit Pendidikan Digital">
                    <option value="Unit Rakaman dan Penyiaran">
                    <option value="Unit Pembangunan dan Bahan Interaktif">
                    <option value="Unit Pelantar Pembelajaran">
                </datalist>
                <div class="flex items-center gap-2 border bg-white rounded-lg shadow-sm px-3 py-1.5 focus-within:ring-2 focus-within:ring-emerald-400 transition"
                    role="group" aria-label="Date Range Filter">
                    <span class="text-[10px] font-black text-emerald-600 tracking-wider">FROM</span>
                    <input type="date" id="startDate" aria-label="Start Date"
                        class="outline-none text-sm font-semibold text-slate-700 bg-transparent cursor-pointer">
                    <span class="text-[10px] font-black text-emerald-600 tracking-wider">TO</span>
                    <input type="date" id="endDate" aria-label="End Date"
                        class="outline-none text-sm font-semibold text-slate-700 bg-transparent cursor-pointer">
                </div>
                <button id="exportCsvBtn"
                    class="bg-emerald-600 text-white px-5 py-2 rounded-lg font-black shadow-md hover:bg-emerald-700 transition">
                    EXPORT CSV
                </button>
                <a href="{{ url('/reports/staff') }}"
                    class="bg-purple-600 text-white px-5 py-2 rounded-lg font-black shadow-md hover:bg-purple-700 transition">STAFF
                    REPORTS</a>
                <a href="{{ url('/reports/create') }}"
                    class="bg-blue-600 text-white px-5 py-2 rounded-lg font-black shadow-md hover:bg-blue-700 transition">+
                    NEW REPORT</a>
                <div class="hidden md:flex flex-col items-end ml-2 border-l pl-4 border-gray-300">
                    <p id="currentUserDisplayDesktop" class="text-xs font-bold text-gray-600 mb-1"></p>
                    <button id="logoutBtnDesktop"
                        class="bg-red-50 text-red-600 border border-red-200 px-3 py-1 rounded shadow-sm text-xs font-bold hover:bg-red-100 transition whitespace-nowrap">Logout</button>
                </div>
            </div>
        </div>

        <div class="mb-8 bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
            <h2 class="text-lg font-bold text-slate-800 mb-4">Reports by Unit</h2>
            <div class="relative w-full h-64 flex justify-center">
                <canvas id="unitReportsChart"></canvas>
            </div>
        </div>

        <p id="statsCount" class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-6"></p>
        <div id="gallery" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8"></div>
    </div>

    <footer class="mt-12 mb-4 text-center text-xs text-slate-500 font-medium no-print">
        <p>Hakcipta Terpelihara 2026 &copy; Sektor Sumber dan Teknologi Pendidikan, Jabatan Pendidikan Negeri Sabah.</p>
        <p class="mt-1">Penafian: SSTP JPN Sabah tidak akan bertanggungjawab ke atas sebarang kehilangan atau kerosakan
            yang diakibatkan oleh penggunaan maklumat yang dicapai daripada dashboard ini.</p>
        <p class="mt-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest cursor-pointer"
            title="Ian Nathaniel Chew@UNIMAS; LI2026">Kredit</p>
    </footer>

    <script>
        window.LaravelReports = @json($reports);
        window.CurrentUserEmail = "{{ auth()->user()->email ?? '' }}";
        window.LogoutRoute = "{{ route('logout') }}";
        window.csrfToken = "{{ csrf_token() }}";
    </script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
</body>

</html>