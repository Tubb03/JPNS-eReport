<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>SSTP LaporKini - Staff Reports</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>

<body class="bg-gray-100 p-6">
    <div class="max-w-6xl mx-auto">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <div class="flex items-center justify-between w-full md:w-auto">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('KPM Logo.png') }}" alt="Logo" class="h-12 w-auto">
                    <div>
                        <h1 class="text-3xl font-black text-purple-900 italic leading-none">LaporKini</h1>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Sektor Sumber dan Teknologi
                            Pendidikan</p>
                    </div>
                </div>
                <div class="text-right md:hidden ml-4">
                    <p id="currentUserDisplayMobile" class="text-xs font-bold text-gray-600 mb-1">{{ auth()->user()->email ?? '' }}</p>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-red-50 text-red-600 border border-red-200 px-3 py-1 rounded shadow-sm text-xs font-bold hover:bg-red-100 transition whitespace-nowrap">Logout</button>
                    </form>
                </div>
            </div>

            <div class="flex flex-wrap gap-3 items-center">
                <select id="filterUnit" aria-label="Filter by Unit"
                    class="p-2 border rounded-lg bg-white shadow-sm outline-none focus:ring-2 focus:ring-purple-400 text-sm font-semibold max-w-[200px] truncate">
                    <option value="All">All Units</option>
                    <option value="Unit Dasar dan Latihan">Unit Dasar dan Latihan</option>
                    <option value="Unit Pengurusan Pusat Sumber">Unit Pengurusan Pusat Sumber</option>
                    <option value="Unit Pendidikan Digital">Unit Pendidikan Digital</option>
                    <option value="Unit Rakaman dan Penyiaran">Unit Rakaman dan Penyiaran</option>
                    <option value="Unit Pembangunan dan Bahan Interaktif">Unit Pembangunan dan Bahan Interaktif</option>
                    <option value="Unit Pelantar Pembelajaran">Unit Pelantar Pembelajaran</option>
                </select>
                <select id="filterStaff" aria-label="Filter by Staff"
                    class="p-2 border rounded-lg bg-white shadow-sm outline-none focus:ring-2 focus:ring-purple-400 text-sm font-semibold max-w-[200px] truncate">
                    <option value="All">All Staff</option>
                </select>
                <a href="{{ route('dashboard') }}"
                    class="bg-blue-600 text-white px-5 py-2 rounded-lg font-black shadow-md hover:bg-blue-700 transition">DASHBOARD</a>
                <div class="hidden md:flex flex-col items-end ml-2 border-l pl-4 border-gray-300">
                    <p id="currentUserDisplayDesktop" class="text-xs font-bold text-gray-600 mb-1">{{ auth()->user()->email ?? '' }}</p>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-red-50 text-red-600 border border-red-200 px-3 py-1 rounded shadow-sm text-xs font-bold hover:bg-red-100 transition whitespace-nowrap">Logout</button>
                    </form>
                </div>
            </div>
        </div>

        <div
            class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 mb-6 flex flex-col md:flex-row justify-between items-center">
            <div>
                <h2 id="selectedStaffName" class="text-2xl font-black text-slate-800">Select a Staff Member</h2>
                <p id="selectedStaffUnit" class="text-sm text-gray-400 font-bold uppercase tracking-wider mt-1">From the
                    dropdown menu</p>
            </div>
            <div class="text-center mt-4 md:mt-0 bg-purple-50 px-6 py-3 rounded-xl border border-purple-100">
                <p class="text-xs font-bold text-purple-400 uppercase tracking-widest">Total Reports Submit</p>
                <p id="staffReportCount" class="text-3xl font-black text-purple-700 leading-none mt-1">0</p>
            </div>
        </div>

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
    <script src="{{ asset('js/staff-reports.js') }}"></script>
</body>

</html>