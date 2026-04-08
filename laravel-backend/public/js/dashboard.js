// Remove module imports, and run immediately
document.addEventListener('DOMContentLoaded', () => {
    const dMobile = document.getElementById('currentUserDisplayMobile');
    const dDesktop = document.getElementById('currentUserDisplayDesktop');
    if (dMobile) dMobile.innerText = window.CurrentUserEmail;
    if (dDesktop) dDesktop.innerText = window.CurrentUserEmail;

    // Logout logic mapping to Laravel POST
    const handleLogout = () => {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = window.LogoutRoute;
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = window.csrfToken;
        form.appendChild(csrfInput);
        document.body.appendChild(form);
        form.submit();
    };

    const btnMobile = document.getElementById('logoutBtnMobile');
    const btnDesktop = document.getElementById('logoutBtnDesktop');
    if (btnMobile) btnMobile.addEventListener('click', handleLogout);
    if (btnDesktop) btnDesktop.addEventListener('click', handleLogout);

    const gallery = document.getElementById('gallery');
    const filter = document.getElementById('filterUnit');
    const searchInput = document.getElementById('searchInput');
    const startDate = document.getElementById('startDate');
    const endDate = document.getElementById('endDate');
    const exportCsvBtn = document.getElementById('exportCsvBtn');
    const statsCount = document.getElementById('statsCount');
    
    let allReports = window.LaravelReports || [];
    let currentFiltered = [];
    let currentLimit = 12;
    let unitChartInstance = null;

    function refreshUI(resetLimit = false) {
        if (resetLimit) currentLimit = 12;

        const unit = filter.value;
        const search = searchInput.value.toLowerCase();

        const sDate = startDate.value ? new Date(startDate.value) : null;
        let eDate = endDate.value ? new Date(endDate.value) : null;
        if (eDate) eDate.setHours(23, 59, 59, 999);

        currentFiltered = allReports.filter(item => {
            const mUnit = unit === "All" || unit === "" || item.unit === unit;
            
            const progName = item.program_name || item.program || '';
            const userName = item.user?.name || item.name || '';
            const mSearch = progName.toLowerCase().includes(search) || userName.toLowerCase().includes(search);

            let mDate = true;
            if (sDate || eDate) {
                const itemDate = new Date(item.program_date || item.date);
                if (sDate && itemDate < sDate) mDate = false;
                if (eDate && itemDate > eDate) mDate = false;
            }

            return mUnit && mSearch && mDate;
        });

        renderUnitChart(currentFiltered);

        statsCount.innerText = `Showing ${currentFiltered.length} Reports`;

        const paginated = currentFiltered.slice(0, currentLimit);

        let html = paginated.map(item => {
            const progName = item.program_name || item.program;
            const userName = item.user?.name || item.name;
            const progDate = item.program_date || item.date;
            
            const createdObj = new Date(item.created_at || new Date());
            const createdDateStr = createdObj.toLocaleDateString();

            return `
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 flex flex-col hover:shadow-2xl transition-all duration-500 overflow-hidden">
                <img src="${item.images?.[0]?.image_path || 'https://via.placeholder.com/400x250'}" class="w-full h-48 object-cover">
                <div class="p-5 flex-grow">
                    <span class="text-[10px] font-black text-blue-600 bg-blue-50 px-2 py-1 rounded-md uppercase">${item.unit}</span>
                    <h3 onclick="window.location.href='/reports/${item.id}'" class="font-bold text-xl text-slate-800 cursor-pointer hover:text-blue-600 transition leading-tight mt-2 line-clamp-2 italic underline decoration-blue-100">
                        ${progName}
                    </h3>
                    <p class="text-xs text-gray-400 mt-2">${progDate} | ${userName}</p>
                </div>
                <div class="p-4 bg-gray-50 border-t flex justify-between items-center">
                    <span class="text-[10px] text-gray-400 font-bold">REPORTED ON: ${createdDateStr}</span>
                    <button onclick="deleteReport('${item.id}')" class="text-red-400 hover:text-red-600 text-[10px] font-black uppercase">Delete</button>
                </div>
            </div>
        `}).join('');

        if (currentLimit < currentFiltered.length) {
            html += `
            <div class="col-span-full flex justify-center mt-6 mb-4">
                <button id="loadMoreBtn" class="bg-indigo-50 text-indigo-700 border border-indigo-200 shadow-sm px-6 py-2 rounded-lg font-black uppercase tracking-widest text-sm hover:bg-indigo-100 transition">
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

    function renderUnitChart(reports) {
        const ctx = document.getElementById('unitReportsChart');
        if (!ctx) return;

        const unitCounts = {};
        reports.forEach(r => {
            const unit = r.unit || 'Unknown';
            unitCounts[unit] = (unitCounts[unit] || 0) + 1;
        });

        const labels = Object.keys(unitCounts);
        const data = Object.values(unitCounts);

        const backgroundColors = labels.map((_, i) => `hsl(${(i * 360) / Math.max(labels.length, 1)}, 70%, 65%)`);

        if (unitChartInstance) {
            unitChartInstance.data.labels = labels;
            unitChartInstance.data.datasets[0].data = data;
            unitChartInstance.data.datasets[0].backgroundColor = backgroundColors;
            unitChartInstance.update();
        } else {
            unitChartInstance = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: backgroundColors,
                        borderWidth: 1,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'right', labels: { font: { size: 12, family: "'Inter', sans-serif" }, color: '#475569' } },
                        tooltip: { backgroundColor: 'rgba(15, 23, 42, 0.9)', padding: 12, cornerRadius: 8, titleFont: { size: 13 }, bodyFont: { size: 13 } }
                    }
                }
            });
        }
    }

    filter.addEventListener('change', () => refreshUI(true));
    searchInput.addEventListener('input', () => refreshUI(true));
    startDate.addEventListener('change', () => refreshUI(true));
    endDate.addEventListener('change', () => refreshUI(true));

    if (exportCsvBtn) {
        exportCsvBtn.addEventListener('click', () => {
            if (currentFiltered.length === 0) return alert("No reports to export!");

            const headers = ["Date", "Unit", "Staff Name", "Program", "Summary", "Objective", "Full Report"];
            const csvRows = currentFiltered.map(item => {
                return [
                    item.program_date || item.date || "",
                    item.unit || "",
                    item.user?.name || item.name || "",
                    item.program_name || item.program || "",
                    item.description || "",
                    item.objective || "",
                    item.full_report || item.fullReport || ""
                ].map(val => `"${String(val).replace(/"/g, '""')}"`).join(",");
            });

            const csvContent = [headers.join(","), ...csvRows].join("\n");
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement("a");
            a.href = url;
            a.download = `One_Page_Report_Export_${new Date().toISOString().split('T')[0]}.csv`;
            a.click();
            URL.revokeObjectURL(url);
        });
    }

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

    // Initialize UI
    refreshUI(true);
});
