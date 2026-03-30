document.addEventListener('DOMContentLoaded', () => {
    const gallery = document.getElementById('gallery');
    const filterUnit = document.getElementById('filterUnit');
    const filterStaff = document.getElementById('filterStaff');
    const selectedStaffName = document.getElementById('selectedStaffName');
    const selectedStaffUnit = document.getElementById('selectedStaffUnit');
    const staffReportCount = document.getElementById('staffReportCount');

    const ADMIN_PIN = "1234";
    let allReports = window.LaravelReports || [];
    let currentLimit = 12;

    const staffByUnit = {
        "Unit Dasar dan Latihan": ["Julai Bin David Jipin @ Gipin", "Desmond Ak Sandum"],
        "Unit Pengurusan Pusat Sumber": ["Pawasia Binti Baha"],
        "Unit Pendidikan Digital": ["JC Jane Canisius James"],
        "Unit Rakaman dan Penyiaran": ["Soenizal Bin Awang Mokhtar"],
        "Unit Pembangunan dan Bahan Interaktif": ["Cornelia Audrey Mudi"],
        "Unit Pelantar Pembelajaran": ["Razmeh Bin Rahman"]
    };

    filterUnit.addEventListener('change', () => {
        const unit = filterUnit.value;
        filterStaff.innerHTML = '<option value="All">All Staff</option>';

        if (unit !== "All" && staffByUnit[unit]) {
            staffByUnit[unit].forEach(staff => {
                const option = document.createElement('option');
                option.value = staff;
                option.textContent = staff;
                filterStaff.appendChild(option);
            });
        } else if (unit === "All") {
            Object.values(staffByUnit).flat().forEach(staff => {
                const option = document.createElement('option');
                option.value = staff;
                option.textContent = staff;
                filterStaff.appendChild(option);
            });
        }
        refreshUI();
    });

    filterUnit.dispatchEvent(new Event('change'));

    function refreshUI(resetLimit = false) {
        if (resetLimit) currentLimit = 12;

        const unit = filterUnit.value;
        const staff = filterStaff.value;

        if (staff !== "All") {
            selectedStaffName.innerText = staff;
            let sUnit = unit;
            if (unit === "All") {
                for (const [u, staffList] of Object.entries(staffByUnit)) {
                    if (staffList.includes(staff)) {
                        sUnit = u;
                        break;
                    }
                }
            }
            selectedStaffUnit.innerText = sUnit !== "All" ? sUnit : "Multiple/Unknown Unit";
        } else {
            selectedStaffName.innerText = unit === "All" ? "All Staff" : unit;
            selectedStaffUnit.innerText = "Select a specific staff member";
        }

        const filtered = allReports.filter(item => {
            const mUnit = unit === "All" || item.unit === unit;
            const reportNameStr = (item.user?.name || item.name || "").toLowerCase().trim();
            const targetStaffStr = staff.toLowerCase().trim();
            const mStaff = staff === "All" || reportNameStr.includes(targetStaffStr) || targetStaffStr.includes(reportNameStr);

            return mUnit && mStaff;
        });

        staffReportCount.innerText = filtered.length;

        if (filtered.length === 0) {
            gallery.innerHTML = `<div class="col-span-1 md:col-span-2 lg:col-span-3 text-center py-10 text-gray-400 font-bold">No reports found for this selection.</div>`;
            return;
        }

        const paginated = filtered.slice(0, currentLimit);

        const grouped = {};
        paginated.forEach(item => {
            const d = new Date(item.program_date || item.date);
            const monthYear = !isNaN(d.getTime()) ? d.toLocaleString('en-US', { month: 'long', year: 'numeric' }) : 'Unknown Date';
            const sortKey = !isNaN(d.getTime()) ? d.getFullYear() * 100 + d.getMonth() : 0;

            if (!grouped[sortKey]) {
                grouped[sortKey] = { label: monthYear, items: [] };
            }
            grouped[sortKey].items.push(item);
        });

        const sortedKeys = Object.keys(grouped).sort((a, b) => b - a);

        let html = "";
        sortedKeys.forEach(key => {
            const group = grouped[key];
            html += `
            <div class="col-span-1 md:col-span-2 lg:col-span-3 mt-8 mb-2 border-b-2 border-purple-200 pb-2">
                <h3 class="text-xl font-black text-slate-800 flex items-center gap-3">
                    <span class="text-2xl opacity-40"></span> ${group.label}
                    <span class="text-xs text-purple-600 bg-purple-100 px-2 py-0.5 rounded-full font-bold ml-2 shadow-sm">${group.items.length} Reports</span>
                </h3>
            </div>
            `;

            html += group.items.map(item => {
                const progName = item.program_name || item.program;
                const userName = item.user?.name || item.name;
                const progDate = item.program_date || item.date;
                const createdObj = new Date(item.created_at || new Date());
                const createdDateStr = createdObj.toLocaleDateString();

                return `
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 flex flex-col hover:shadow-2xl transition-all duration-500 overflow-hidden">
                    <img src="${item.images?.[0]?.image_path || 'https://via.placeholder.com/400x250'}" class="w-full h-48 object-cover">
                    <div class="p-5 flex-grow">
                        <span class="text-[10px] font-black text-purple-600 bg-purple-50 px-2 py-1 rounded-md uppercase">${item.unit}</span>
                        <h3 onclick="window.location.href='/reports/${item.id}'" class="font-bold text-xl text-slate-800 cursor-pointer hover:text-purple-600 transition leading-tight mt-2 line-clamp-2 italic underline decoration-purple-100">
                            ${progName}
                        </h3>
                        <p class="text-xs text-gray-400 mt-2 font-semibold">Submitted by: <span class="text-gray-600">${userName}</span></p>
                        <p class="text-xs text-gray-400 mt-1">Date: ${progDate}</p>
                    </div>
                    <div class="p-4 bg-gray-50 border-t flex justify-between items-center">
                        <span class="text-[10px] text-gray-400 font-bold">REPORTED ON: ${createdDateStr}</span>
                        <button onclick="deleteReport('${item.id}')" class="text-red-400 hover:text-red-600 text-[10px] font-black uppercase">Delete</button>
                    </div>
                </div>
            `}).join('');
        });

        if (currentLimit < filtered.length) {
            html += `
            <div class="col-span-1 md:col-span-2 lg:col-span-3 flex justify-center mt-6 mb-4">
                <button id="loadMoreBtn" class="bg-purple-50 text-purple-700 border border-purple-200 shadow-sm px-6 py-2 rounded-lg font-black uppercase tracking-widest text-sm hover:bg-purple-100 transition">
                    Load More Reports
                </button>
            </div>
            `;
        }

        gallery.innerHTML = html;

        const loadMoreBtn = document.getElementById('loadMoreBtn');
        if (loadMoreBtn) {
            loadMoreBtn.addEventListener('click', () => {
                currentLimit += 12;
                refreshUI(false);
            });
        }
    }

    filterStaff.addEventListener('change', () => refreshUI(true));

    window.deleteReport = async (id) => {
        if (!window.CurrentUserEmail) {
            alert("You must be logged in to delete reports.");
            return;
        }

        if (confirm("Confirm permanent deletion?")) {
            try {
                const response = await fetch(`/reports/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': window.csrfToken,
                        'Accept': 'application/json'
                    }
                });
                
                if(response.ok) {
                    alert("Report deleted.");
                    allReports = allReports.filter(r => r.id !== parseInt(id) && r.id !== id);
                    refreshUI(false);
                } else {
                    alert("Failed to delete report.");
                }
            } catch (error) {
                console.error("Error deleting document: ", error);
                alert("Failed to delete report. You might not have permission.");
            }
        }
    };
});
