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
const statsCount = document.getElementById('statsCount');

const ADMIN_PIN = "1234"; // Admin security
let allReports = [];

onSnapshot(query(collection(db, "reports"), orderBy("createdAt", "desc")), (snap) => {
    allReports = snap.docs.map(d => ({ id: d.id, ...d.data() }));
    refreshUI();
});

function refreshUI() {
    const unit = filter.value;
    const search = searchInput.value.toLowerCase();

    const filtered = allReports.filter(item => {
        const mUnit = unit === "All" || item.unit === unit;
        const mSearch = item.program.toLowerCase().includes(search) || item.name.toLowerCase().includes(search);
        return mUnit && mSearch;
    });

    statsCount.innerText = `Showing ${filtered.length} Reports`;

    gallery.innerHTML = filtered.map(item => `
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 flex flex-col hover:shadow-2xl transition-all duration-500 overflow-hidden">
            <img src="${item.imageUrls?.[0] || 'https://via.placeholder.com/400x250'}" class="w-full h-48 object-cover">
            <div class="p-5 flex-grow">
                <span class="text-[10px] font-black text-blue-600 bg-blue-50 px-2 py-1 rounded-md uppercase">${item.unit}</span>
                <h3 onclick="window.location.href='view-report.html?id=${item.id}'" class="font-bold text-xl text-slate-800 cursor-pointer hover:text-blue-600 transition leading-tight mt-2 line-clamp-2 italic underline decoration-blue-100">
                    ${item.program}
                </h3>
                <p class="text-xs text-gray-400 mt-2">${item.date} | ${item.name}</p>
            </div>
            <div class="p-4 bg-gray-50 border-t flex justify-end">
                <button onclick="deleteReport('${item.id}')" class="text-red-400 hover:text-red-600 text-[10px] font-black uppercase">Delete</button>
            </div>
        </div>
    `).join('');
}

filter.addEventListener('change', refreshUI);
searchInput.addEventListener('input', refreshUI);

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
