<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Report</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>
<body class="bg-gray-100 p-4 md:p-8 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-4xl bg-white p-8 md:p-12 rounded-2xl shadow-xl border border-gray-200">
        <div class="flex items-center justify-center gap-4 mb-8 border-b pb-6">
            <img src="{{ asset('KPM Logo.png') }}" alt="Logo" class="h-16 w-auto">
            <div class="text-left">
                <h1 class="text-2xl md:text-3xl font-black text-blue-900 tracking-tight leading-none">Edit Report</h1>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Update existing report details</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 font-bold shadow-sm border border-red-200">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('reports.update', $report) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="date" name="program_date" id="programDate" aria-label="Program Date" required
                    value="{{ old('program_date', $report->program_date) }}"
                    class="w-full p-2 border rounded shadow-sm outline-none focus:ring-2 focus:ring-blue-400 font-semibold text-slate-700 uppercase tracking-wider">
            </div>

            <input type="text" name="program_name" id="programName" placeholder="Program/Activity Title"
                aria-label="Program or Activity Title" required value="{{ old('program_name', $report->program_name) }}" maxlength="100"
                class="w-full p-2 border rounded shadow-sm outline-none focus:ring-2 focus:ring-blue-400">

            <input list="unit-list" name="unit" id="unit" aria-label="Unit Selection" placeholder="Select or search unit..."
                value="{{ old('unit', $report->unit) }}"
                class="w-full p-2 border rounded bg-white shadow-sm outline-none focus:ring-2 focus:ring-blue-400">
            <datalist id="unit-list">
                <option value="Unit Dasar dan Latihan">
                <option value="Unit Pengurusan Pusat Sumber">
                <option value="Unit Pendidikan Digital">
                <option value="Unit Rakaman dan Penyiaran">
                <option value="Unit Pembangunan dan Bahan Interaktif">
                <option value="Unit Pelantar Pembelajaran">
            </datalist>

            <textarea name="description" id="description" rows="2" placeholder="Short Summary (for Dashboard)" aria-label="Short Summary" maxlength="150"
                class="w-full p-2 border rounded outline-none focus:ring-2 focus:ring-blue-400 shadow-sm">{{ old('description', $report->description) }}</textarea>
            
            <div>
                <textarea name="objective" id="objective" rows="2" placeholder="Program Objective" aria-label="Program Objective" maxlength="300"
                    class="w-full p-2 border rounded outline-none focus:ring-2 focus:ring-blue-400 shadow-sm">{{ old('objective', $report->objective) }}</textarea>
                <div class="text-right text-[11px] text-gray-500 mt-1 font-semibold tracking-wide flex justify-between">
                    <span class="text-red-500 font-black tracking-widest uppercase text-[10px]">Strict: Max 300 Characters</span>
                    <span>Word count: <span id="objectiveWordCount" class="text-blue-600">0</span></span>
                </div>
            </div>
            
            <div>
                <textarea name="full_report" id="fullReport" rows="5" placeholder="Detailed Report Content"
                    aria-label="Detailed Report Content" required maxlength="1500"
                    class="w-full p-2 border rounded outline-none focus:ring-2 focus:ring-blue-400 shadow-sm">{{ old('full_report', $report->full_report) }}</textarea>
                <div class="text-right text-[11px] text-gray-500 mt-1 font-semibold tracking-wide flex justify-between">
                    <span class="text-red-500 font-black tracking-widest uppercase text-[10px]">Strict: Max 1500 Characters</span>
                    <span>Word count: <span id="fullReportWordCount" class="text-blue-600">0</span></span>
                </div>
            </div>

            <div class="bg-blue-50 p-6 rounded-xl border-2 border-dashed border-blue-200">
                <label class="block text-sm font-bold text-blue-800 mb-2">Existing Photos</label>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 mb-4">
                    @foreach($report->images as $img)
                        <div class="relative group" id="existing_img_{{ $img->id }}">
                            <img src="{{ asset($img->image_path) }}" class="w-full h-24 object-cover rounded shadow-sm">
                            <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition rounded">
                                <button type="button" onclick="removeExistingImage({{ $img->id }})" class="text-white font-bold bg-red-600 px-3 py-1 text-xs rounded hover:bg-red-700">Remove</button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div id="removedImagesInputs"></div> <!-- Hidden inputs for removed images will go here -->

                <label class="block text-sm font-bold text-blue-800 mb-2 mt-4">Upload Additional Photos</label>
                <input type="file" name="images[]" id="imageUpload" accept="image/*" aria-label="Upload Program Photos" multiple
                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                <div id="previewGallery" class="grid grid-cols-3 sm:grid-cols-4 gap-2 mt-4"></div>
            </div>

            <button type="submit" id="submitBtn"
                class="w-full bg-emerald-600 text-white py-4 rounded-xl font-black text-lg hover:bg-emerald-700 transition shadow-lg uppercase tracking-widest">Update
                Report</button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('reports.show', $report) }}" class="text-blue-600 hover:underline text-sm font-bold">← Cancel and return</a>
        </div>
    </div>

    <script>
        function removeExistingImage(id) {
            document.getElementById('existing_img_' + id).style.display = 'none';
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'images_toremove[]';
            input.value = id;
            document.getElementById('removedImagesInputs').appendChild(input);
        }

        const imageUpload = document.getElementById('imageUpload');
        const previewGallery = document.getElementById('previewGallery');
        const submitBtn = document.getElementById('submitBtn');
        const reportForm = document.querySelector('form');

        // Word count functionality
        const countWords = (str) => str.trim().split(/\s+/).filter(w => w.length > 0).length;
        
        ['objective', 'fullReport'].forEach(id => {
            const el = document.getElementById(id);
            const counter = document.getElementById(id + 'WordCount');
            if (el && counter) {
                const updateCount = () => { counter.innerText = countWords(el.value); };
                el.addEventListener('input', updateCount);
                updateCount(); // Init on load
            }
        });

        // Basic frontend preview
        imageUpload.addEventListener('change', function(e) {
            previewGallery.innerHTML = '';
            
            const files = Array.from(e.target.files);
            
            files.slice(0, 4).forEach((file, index) => {
                if (!file.type.startsWith('image/')) return;
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'w-full h-24 rounded-lg overflow-hidden relative shadow-sm border border-gray-200';
                    if (index === 3 && files.length > 4) {
                        div.innerHTML = `
                            <img src="${e.target.result}" class="w-full h-full object-cover blur-[2px] brightness-75">
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-white font-black text-lg">+${files.length - 4}</span>
                            </div>
                        `;
                    } else {
                        div.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                    }
                    previewGallery.appendChild(div);
                }
                reader.readAsDataURL(file);
            });
        });

        reportForm.addEventListener('submit', () => {
            submitBtn.innerText = 'UPDATING... PLEASE WAIT';
            submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
        });
    </script>
</body>
</html>
