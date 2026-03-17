import { collection, query, orderBy, onSnapshot, doc, deleteDoc } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-firestore.js";
import { onAuthStateChanged, signOut } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-auth.js";
import { db, auth } from "./firebase-config.js";

// Protected Route Logic
onAuthStateChanged(auth, (user) => {
    if (!user) {
        window.location.replace("login.html");
    } else {
        const dMobile = document.getElementById('currentUserDisplayMobile');
        const dDesktop = document.getElementById('currentUserDisplayDesktop');
        if (dMobile) dMobile.innerText = user.email;
        if (dDesktop) dDesktop.innerText = user.email;
    }
});

const handleLogout = () => signOut(auth).then(() => window.location.replace("login.html"));
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
const ADMIN_PIN = "1234"; // Admin security
let allReports = [];
let currentFiltered = [];
let currentLimit = 12;
let userChartInstance = null;

onSnapshot(query(collection(db, "reports"), orderBy("createdAt", "desc")), (snap) => {
    allReports = snap.docs.map(d => ({ id: d.id, ...d.data() }));
    refreshUI(true);
});

function refreshUI(resetLimit = false) {
    if (resetLimit) currentLimit = 12;

    const unit = filter.value;
    const search = searchInput.value.toLowerCase();

    const sDate = startDate.value ? new Date(startDate.value) : null;
    let eDate = endDate.value ? new Date(endDate.value) : null;
    if (eDate) eDate.setHours(23, 59, 59, 999);

    currentFiltered = allReports.filter(item => {
        const mUnit = unit === "All" || item.unit === unit;
        const mSearch = item.program.toLowerCase().includes(search) || (item.name && item.name.toLowerCase().includes(search));

        let mDate = true;
        if (sDate || eDate) {
            const itemDate = new Date(item.date);
            if (sDate && itemDate < sDate) mDate = false;
            if (eDate && itemDate > eDate) mDate = false;
        }

        return mUnit && mSearch && mDate;
    });

    renderUserChart(currentFiltered);

    statsCount.innerText = `Showing ${currentFiltered.length} Reports`;

    const paginated = currentFiltered.slice(0, currentLimit);

    let html = paginated.map(item => `
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 flex flex-col hover:shadow-2xl transition-all duration-500 overflow-hidden">
            <img src="${item.imageUrls?.[0] || 'https://via.placeholder.com/400x250'}" class="w-full h-48 object-cover">
            <div class="p-5 flex-grow">
                <span class="text-[10px] font-black text-blue-600 bg-blue-50 px-2 py-1 rounded-md uppercase">${item.unit}</span>
                <h3 onclick="window.location.href='view-report.html?id=${item.id}'" class="font-bold text-xl text-slate-800 cursor-pointer hover:text-blue-600 transition leading-tight mt-2 line-clamp-2 italic underline decoration-blue-100">
                    ${item.program}
                </h3>
                <p class="text-xs text-gray-400 mt-2">${item.date} | ${item.name}</p>
            </div>
            <div class="p-4 bg-gray-50 border-t flex justify-between items-center">
                <span class="text-[10px] text-gray-400 font-bold">REPORTED ON: ${item.createdAt?.toDate?.() ? item.createdAt.toDate().toLocaleDateString() : new Date().toLocaleDateString()}</span>
                <button onclick="deleteReport('${item.id}')" class="text-red-400 hover:text-red-600 text-[10px] font-black uppercase">Delete</button>
            </div>
        </div>
    `).join('');

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

function renderUserChart(reports) {
    const ctx = document.getElementById('userReportsChart');
    if (!ctx) return;

    // Aggregate reports by user
    const userCounts = {};
    reports.forEach(r => {
        const name = r.name || 'Unknown';
        userCounts[name] = (userCounts[name] || 0) + 1;
    });

    const labels = Object.keys(userCounts);
    const data = Object.values(userCounts);

    // Generate colors
    const backgroundColors = labels.map((_, i) => `hsl(${(i * 360) / Math.max(labels.length, 1)}, 70%, 65%)`);

    if (userChartInstance) {
        userChartInstance.data.labels = labels;
        userChartInstance.data.datasets[0].data = data;
        userChartInstance.data.datasets[0].backgroundColor = backgroundColors;
        userChartInstance.update();
    } else {
        userChartInstance = new Chart(ctx, {
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
                    legend: {
                        position: 'right',
                        labels: {
                            font: {
                                size: 12,
                                family: "'Inter', 'Segoe UI', sans-serif"
                            },
                            color: '#475569'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        padding: 12,
                        cornerRadius: 8,
                        titleFont: { size: 13, family: "'Inter', 'Segoe UI', sans-serif" },
                        bodyFont: { size: 13, family: "'Inter', 'Segoe UI', sans-serif" }
                    }
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
                item.date || "",
                item.unit || "",
                item.name || "",
                item.program || "",
                item.description || "",
                item.objective || "",
                item.fullReport || ""
            ].map(val => `"${String(val).replace(/"/g, '""')}"`).join(",");
        });

        const csvContent = [headers.join(","), ...csvRows].join("\n");
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = `LaporKini_Export_${new Date().toISOString().split('T')[0]}.csv`;
        a.click();
        URL.revokeObjectURL(url);
    });
}

window.deleteReport = async (id) => {
    if (!auth.currentUser) {
        alert("You must be logged in to delete reports.");
        return;
    }

    // Simplistic check for demo purposes. 
    // Real security should be implemented via Firestore Security Rules.
    if (confirm("Confirm permanent deletion?")) {
        try {
            await deleteDoc(doc(db, "reports", id));
            alert("Report deleted.");
        } catch (error) {
            console.error("Error deleting document: ", error);
            alert("Failed to delete report. You might not have permission.");
        }
    }
};
