<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SSTP LaporKini - Submit</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>

<body class="bg-slate-100 p-4 md:p-8">
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow-lg border border-gray-200">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <img src="{{ asset('KPM Logo.png') }}" alt="Logo" class="h-12 w-auto">
                <div>
                    <h1 class="text-3xl font-black text-blue-900 italic leading-none">LaporKini</h1>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Sektor Sumber dan Teknologi Pendidikan</p>
                </div>
            </div>
            <div class="text-right">
                <p id="currentUserDisplay" class="text-xs font-bold text-gray-600 mb-1">{{ auth()->user()->email ?? '' }}</p>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-red-50 text-red-600 border border-red-200 px-3 py-1 rounded shadow-sm text-xs font-bold hover:bg-red-100 transition whitespace-nowrap">Logout</button>
                </form>
            </div>
        </div>

        <form action="{{ route('reports.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            
            @if ($errors->any())
                <div class="bg-red-50 text-red-600 p-3 rounded-lg text-sm font-bold border border-red-200">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="text" value="{{ auth()->user()->name }}" disabled
                    class="p-2 border rounded shadow-sm outline-none bg-gray-100 text-gray-500">
                <input type="date" name="program_date" id="programDate" aria-label="Program Date" required value="{{ old('program_date') }}"
                    class="p-2 border rounded shadow-sm outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <input type="text" name="program_name" id="programName" placeholder="Program/Activity Title"
                aria-label="Program or Activity Title" required value="{{ old('program_name') }}"
                class="w-full p-2 border rounded shadow-sm outline-none focus:ring-2 focus:ring-blue-400">

            <select name="unit" id="unit" aria-label="Unit Selection"
                class="w-full p-2 border rounded bg-white shadow-sm outline-none focus:ring-2 focus:ring-blue-400">
                <option value="Unit Dasar dan Latihan" {{ old('unit') == 'Unit Dasar dan Latihan' ? 'selected' : '' }}>Unit Dasar dan Latihan</option>
                <option value="Unit Pengurusan Pusat Sumber" {{ old('unit') == 'Unit Pengurusan Pusat Sumber' ? 'selected' : '' }}>Unit Pengurusan Pusat Sumber</option>
                <option value="Unit Pendidikan Digital" {{ old('unit') == 'Unit Pendidikan Digital' ? 'selected' : '' }}>Unit Pendidikan Digital</option>
                <option value="Unit Rakaman dan Penyiaran" {{ old('unit') == 'Unit Rakaman dan Penyiaran' ? 'selected' : '' }}>Unit Rakaman dan Penyiaran</option>
                <option value="Unit Pembangunan dan Bahan Interaktif" {{ old('unit') == 'Unit Pembangunan dan Bahan Interaktif' ? 'selected' : '' }}>Unit Pembangunan dan Bahan Interaktif</option>
                <option value="Unit Pelantar Pembelajaran" {{ old('unit') == 'Unit Pelantar Pembelajaran' ? 'selected' : '' }}>Unit Pelantar Pembelajaran</option>
            </select>

            <textarea name="description" id="description" rows="2" placeholder="Short Summary (for Dashboard)" aria-label="Short Summary"
                class="w-full p-2 border rounded outline-none focus:ring-2 focus:ring-blue-400 shadow-sm">{{ old('description') }}</textarea>
            
            <div>
                <textarea name="objective" id="objective" rows="2" placeholder="Program Objective" aria-label="Program Objective"
                    class="w-full p-2 border rounded outline-none focus:ring-2 focus:ring-blue-400 shadow-sm">{{ old('objective') }}</textarea>
                <div class="text-right text-[11px] text-gray-500 mt-1 font-semibold tracking-wide">
                    Word count: <span id="objectiveWordCount" class="text-blue-600">0</span> <span class="text-gray-400">/ ~50 (Recommended)</span>
                </div>
            </div>
            
            <div>
                <textarea name="full_report" id="fullReport" rows="5" placeholder="Detailed Report Content"
                    aria-label="Detailed Report Content" required
                    class="w-full p-2 border rounded outline-none focus:ring-2 focus:ring-blue-400 shadow-sm">{{ old('full_report') }}</textarea>
                <div class="text-right text-[11px] text-gray-500 mt-1 font-semibold tracking-wide">
                    Word count: <span id="fullReportWordCount" class="text-blue-600">0</span> <span class="text-gray-400">/ ~250 (Recommended)</span>
                </div>
            </div>

            <div class="bg-blue-50 p-6 rounded-xl border-2 border-dashed border-blue-200">
                <label class="block text-sm font-bold text-blue-800 mb-2">Upload Program Photos (Multiple)</label>
                <input type="file" name="images[]" id="imageUpload" accept="image/*" aria-label="Upload Program Photos" multiple required
                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                <div id="previewGallery" class="grid grid-cols-3 sm:grid-cols-4 gap-2 mt-4"></div>
            </div>

            <button type="submit" id="submitBtn"
                class="w-full bg-blue-600 text-white py-4 rounded-xl font-black text-lg hover:bg-blue-700 transition shadow-lg uppercase tracking-widest">Submit
                Full Report</button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('dashboard') }}" class="text-blue-600 hover:underline text-sm font-bold">← Back to Dashboard</a>
        </div>
    </div>

    <script>
        const imageUpload = document.getElementById('imageUpload');
        const previewGallery = document.getElementById('previewGallery');
        const submitBtn = document.getElementById('submitBtn');
        const reportForm = document.querySelector('form');

        // Word count functionality
        const countWords = (str) => str.trim().split(/\s+/).filter(w => w.length > 0).length;

        const objInput = document.getElementById('objective');
        const objCount = document.getElementById('objectiveWordCount');
        objInput.addEventListener('input', () => {
            const count = countWords(objInput.value);
            objCount.innerText = count;
            objCount.className = count > 50 ? 'text-red-600 font-bold' : 'text-blue-600';
        });

        const repInput = document.getElementById('fullReport');
        const repCount = document.getElementById('fullReportWordCount');
        repInput.addEventListener('input', () => {
            const count = countWords(repInput.value);
            repCount.innerText = count;
            repCount.className = count > 250 ? 'text-red-600 font-bold' : 'text-blue-600';
        });

        imageUpload.addEventListener('change', (e) => {
            previewGallery.innerHTML = "";
            const files = Array.from(e.target.files);
            files.forEach((file) => {
                const reader = new FileReader();
                reader.onload = (ev) => {
                    const div = document.createElement('div');
                    div.className = "relative group";
                    div.innerHTML = \`<img src="\${ev.target.result}" class="w-full h-20 object-cover rounded shadow-sm border">\`;
                    previewGallery.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        });

        reportForm.addEventListener('submit', () => {
            submitBtn.innerText = "UPLOADING...";
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50');
        });
    </script>
</body>
</html>