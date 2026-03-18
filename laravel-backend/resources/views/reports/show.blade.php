<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Full Report View</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="style.css">
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }

        /* Essential print fixes for exact A4 rendering */
        @media print {
            body {
                background: white !important;
                margin: 0 !important;
                padding: 0 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                display: block !important;
            }

            .no-print {
                display: none !important;
            }

            .report-box {
                border: none !important;
                box-shadow: none !important;
                width: 210mm !important;
                max-width: 210mm !important;
                height: 296mm !important;
                /* Slightly under 297mm to prevent blank second page bleed */
                max-height: 296mm !important;
                padding: 15mm !important;
                margin: 0 auto !important;
                position: relative !important;
                page-break-after: avoid !important;
                page-break-before: avoid !important;
                overflow: hidden !important;
            }
        }

        /* Allow images to take more space natively */
        .dynamic-gallery {
            max-height: 380px;
            /* Increased to allow multiple images to be larger */
        }

        .img-container {
            aspect-ratio: auto;
            min-height: 120px;
            height: 100%;
            display: flex;
        }

        /* Hide scrollbar for clean UI */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="bg-gray-100 p-4 md:p-8 flex flex-col items-center min-h-screen">

    <!-- Action Buttons (Hidden in Print) -->
    <div class="w-full max-w-[210mm] flex justify-end gap-3 mb-4 no-print shrink-0 mx-auto">
        <button onclick="window.location.href='dashboard.html'"
            class="bg-white border text-gray-700 px-6 py-2 rounded shadow-sm text-sm font-bold hover:bg-gray-50 transition-colors">Back</button>
        <button onclick="window.print()"
            class="bg-blue-600 text-white px-6 py-2 rounded shadow-sm text-sm font-bold hover:bg-blue-700 transition-colors">Download
            PDF</button>
    </div>

    <!-- The A4 Printable Area -->
    <div
        class="w-full max-w-[210mm] mx-auto bg-white p-8 md:p-12 border border-gray-200 shadow-xl report-box flex flex-col h-[297mm] overflow-hidden shrink-0 relative rounded-lg print:rounded-none">

        <!-- Header -->
        <div class="border-b-[3px] border-blue-900 pb-4 mb-5 flex justify-between items-end flex-shrink-0">
            <div class="flex items-center gap-4">
                <img src="KPM Logo.png" alt="Logo" class="h-12 w-auto object-contain">
                <div>
                    <h1 class="text-3xl font-black text-blue-900 italic leading-none mb-1">LaporKini</h1>
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Sektor Sumber dan Teknologi
                        Pendidikan</p>
                </div>
            </div>
            <div class="text-right max-w-[50%]">
                <p id="vUnit" class="text-blue-600 text-[10px] font-bold tracking-widest uppercase mb-1"></p>
                <h2 id="vProgram" class="text-lg font-black text-slate-800 uppercase leading-tight line-clamp-2">...
                </h2>
            </div>
        </div>

        <div class="flex-grow flex flex-col gap-5 overflow-hidden">

            <!-- Objective Section -->
            <section class="flex-shrink-0">
                <h2 class="text-[10px] font-black text-slate-400 tracking-widest uppercase mb-2">Program Objective</h2>
                <p id="vObjective"
                    class="text-sm text-slate-700 leading-relaxed border-l-[3px] border-blue-200 pl-4 font-medium italic">
                </p>
            </section>

            <!-- Gallery Section -->
            <section class="flex-shrink-0">
                <h2 class="text-[10px] font-black text-slate-400 tracking-widest uppercase mb-2">Photographic Evidence
                </h2>
                <div id="vGallery" class="dynamic-gallery grid gap-1 w-full overflow-hidden rounded-md min-h-[150px]">
                </div>
            </section>

            <!-- Detailed Report Section -->
            <section class="flex-shrink flex flex-col min-h-[80px] relative mt-2">
                <h2 class="text-[10px] font-black text-slate-400 tracking-widest uppercase mb-2">Detailed Report</h2>
                <div class="overflow-hidden pb-2 relative text-fit-container">
                    <div id="vFull"
                        class="text-[14px] text-slate-800 leading-relaxed text-justify whitespace-pre-wrap columns-1 md:columns-2 gap-8">
                    </div>
                </div>
            </section>
        </div>

        <!-- Footer Signatures -->
        <div class="mt-auto pt-4 border-t-2 border-slate-100 flex justify-between items-end flex-shrink-0">
            <div>
                <div class="w-40 border-b border-gray-300 mb-2 h-10"></div>
                <p class="text-[10px] text-gray-400 uppercase font-bold tracking-widest mb-1">Reported By</p>
                <p id="vName" class="text-sm font-bold text-slate-800"></p>
            </div>
            <div class="text-right">
                <p class="text-[10px] text-gray-400 uppercase font-bold tracking-widest mb-1">Date of Program</p>
                <p id="vDate" class="text-sm font-bold text-slate-800"></p>
            </div>
        </div>
    </div>

    <footer class="mt-12 mb-4 w-full max-w-[210mm] mx-auto text-center text-xs text-slate-500 font-medium no-print">
        <p>Hakcipta Terpelihara 2026 &copy; Sektor Sumber dan Teknologi Pendidikan, Jabatan Pendidikan Negeri Sabah.</p>
        <p class="mt-1">Penafian: SSTP JPN Sabah tidak akan bertanggungjawab ke atas sebarang kehilangan atau kerosakan
            yang diakibatkan oleh penggunaan maklumat yang dicapai daripada dashboard ini.</p>
        <p class="mt-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest cursor-pointer"
            title="Ian Nathaniel Chew@UNIMAS; LI2026">Kredit</p>
    </footer>

    <script src="js/view-report.js" type="module"></script>
</body>

</html>