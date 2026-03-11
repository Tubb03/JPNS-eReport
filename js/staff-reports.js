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
const filterUnit = document.getElementById('filterUnit');
const filterStaff = document.getElementById('filterStaff');
const selectedStaffName = document.getElementById('selectedStaffName');
const selectedStaffUnit = document.getElementById('selectedStaffUnit');
const staffReportCount = document.getElementById('staffReportCount');

const ADMIN_PIN = "1234";
let allReports = [];

const staffByUnit = {
    "Unit Dasar dan Latihan": ["Julai Bin David Jipin @ Gipin", "Desmond Ak Sandum"],
    "Unit Pengurusan Pusat Sumber": ["Pawasia Binti Baha"],
    "Unit Pendidikan Digital": ["JC Jane Canisius James"],
    "Unit Rakaman dan Penyiaran": ["Soenizal Bin Awang Mokhtar"],
    "Unit Pembangunan dan Bahan Interaktif": ["Cornelia Audrey Mudi"],
    "Unit Pelantar Pembelajaran": ["Razmeh Bin Rahman"]
};

// Populate Staff dropdown based on Unit selection
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
        // If "All Units", list all staff
        Object.values(staffByUnit).flat().forEach(staff => {
            const option = document.createElement('option');
            option.value = staff;
            option.textContent = staff;
            filterStaff.appendChild(option);
        });
    }
    refreshUI();
});

// Initialize empty staff list (All)
filterUnit.dispatchEvent(new Event('change'));

onSnapshot(query(collection(db, "reports"), orderBy("createdAt", "desc")), (snap) => {
    allReports = snap.docs.map(d => ({ id: d.id, ...d.data() }));
    refreshUI();
});

function refreshUI() {
    const unit = filterUnit.value;
    const staff = filterStaff.value;

    // Update Header Display
    if (staff !== "All") {
        selectedStaffName.innerText = staff;
        // Find unit if "All" is selected in unit dropdown but a specific staff is picked
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
        // Normalize names to handle slight inconsistencies in user input when they created the report
        const reportNameStr = (item.name || "").toLowerCase().trim();
        const targetStaffStr = staff.toLowerCase().trim();

        // We check if the target staff matches the reported staff name
        // To be safe, check if either one includes the other, since user input might be incomplete
        const mStaff = staff === "All" || reportNameStr.includes(targetStaffStr) || targetStaffStr.includes(reportNameStr);

        return mUnit && mStaff;
    });

    staffReportCount.innerText = filtered.length;

    if (filtered.length === 0) {
        gallery.innerHTML = `<div class="col-span-1 md:col-span-2 lg:col-span-3 text-center py-10 text-gray-400 font-bold">No reports found for this selection.</div>`;
        return;
    }

    // Group by Month and Year
    const grouped = {};
    filtered.forEach(item => {
        const d = new Date(item.date);
        // Extract "Month YYYY", e.g. "January 2024" if available, else fallback
        const monthYear = !isNaN(d.getTime()) ? d.toLocaleString('en-US', { month: 'long', year: 'numeric' }) : 'Unknown Date';

        // Key for robust sorting: YYYYMM (e.g. 202401)
        const sortKey = !isNaN(d.getTime()) ? d.getFullYear() * 100 + d.getMonth() : 0;

        if (!grouped[sortKey]) {
            grouped[sortKey] = { label: monthYear, items: [] };
        }
        grouped[sortKey].items.push(item);
    });

    // Sort keys descending so newest months are first
    const sortedKeys = Object.keys(grouped).sort((a, b) => b - a);

    let html = "";
    sortedKeys.forEach(key => {
        const group = grouped[key];

        // Add header for the month/year grouping
        html += `
        <div class="col-span-1 md:col-span-2 lg:col-span-3 mt-8 mb-2 border-b-2 border-purple-200 pb-2">
            <h3 class="text-xl font-black text-slate-800 flex items-center gap-3">
                <span class="text-2xl opacity-40"></span> ${group.label}
                <span class="text-xs text-purple-600 bg-purple-100 px-2 py-0.5 rounded-full font-bold ml-2 shadow-sm">${group.items.length} Reports</span>
            </h3>
        </div>
        `;

        // Add all items in the group
        html += group.items.map(item => `
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 flex flex-col hover:shadow-2xl transition-all duration-500 overflow-hidden">
                <img src="${item.imageUrls?.[0] || 'https://via.placeholder.com/400x250'}" class="w-full h-48 object-cover">
                <div class="p-5 flex-grow">
                    <span class="text-[10px] font-black text-purple-600 bg-purple-50 px-2 py-1 rounded-md uppercase">${item.unit}</span>
                    <h3 onclick="window.location.href='view-report.html?id=${item.id}'" class="font-bold text-xl text-slate-800 cursor-pointer hover:text-purple-600 transition leading-tight mt-2 line-clamp-2 italic underline decoration-purple-100">
                        ${item.program}
                    </h3>
                    <p class="text-xs text-gray-400 mt-2 font-semibold">Submitted by: <span class="text-gray-600">${item.name}</span></p>
                    <p class="text-xs text-gray-400 mt-1">Date: ${item.date}</p>
                </div>
                <div class="p-4 bg-gray-50 border-t flex justify-between items-center">
                    <span class="text-[10px] text-gray-400 font-bold">REPORTED ON: ${item.createdAt?.toDate?.() ? item.createdAt.toDate().toLocaleDateString() : new Date().toLocaleDateString()}</span>
                    <button onclick="deleteReport('${item.id}')" class="text-red-400 hover:text-red-600 text-[10px] font-black uppercase">Delete</button>
                </div>
            </div>
        `).join('');
    });

    gallery.innerHTML = html;
}

filterStaff.addEventListener('change', refreshUI);

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
