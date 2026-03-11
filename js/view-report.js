import { doc, getDoc } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-firestore.js";
import { onAuthStateChanged } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-auth.js";
import { db, auth } from "./firebase-config.js";

// Protected Route Logic
onAuthStateChanged(auth, (user) => {
    if (!user) {
        window.location.replace("login.html");
    }
});

const urlParams = new URLSearchParams(window.location.search);
const reportId = urlParams.get('id');

if (reportId) {
    const docSnap = await getDoc(doc(db, "reports", reportId));
    if (docSnap.exists()) {
        const data = docSnap.data();
        document.getElementById('vProgram').innerText = data.program;
        document.getElementById('vUnit').innerText = data.unit;
        document.getElementById('vDate').innerText = data.date;
        document.getElementById('vName').innerText = data.name;
        document.getElementById('vObjective').innerText = data.objective || "N/A";
        document.getElementById('vFull').innerText = data.fullReport || "N/A";

        const gallery = document.getElementById('vGallery');
        const images = data.imageUrls || [];

        // Set columns based on count
        const cols = images.length > 4 ? 4 : images.length;
        gallery.style.gridTemplateColumns = `repeat(${cols}, minmax(0, 1fr))`;

        images.forEach(url => {
            gallery.innerHTML += `
                <div class="img-container overflow-hidden shadow-sm">
                    <img src="${url}" class="w-full h-full object-cover">
                </div>`;
        });

        // Auto-fit text logic to ensure it stays on one page without cutting off manually
        setTimeout(() => {
            const reportBox = document.querySelector('.report-box');
            const contentContainer = document.getElementById('vFull').parentElement;
            const content = document.getElementById('vFull');
            let currentSize = 14; // Start at 14px text

            // Reduce font size until the total content fits within standard A4 height
            const footer = reportBox.querySelector('.mt-auto');

            while (footer.getBoundingClientRect().bottom > reportBox.getBoundingClientRect().bottom && currentSize > 9) {
                currentSize -= 0.5;
                content.style.fontSize = currentSize + 'px';
                content.style.lineHeight = '1.4';
            }
        }, 800); // Increased timeout to wait for images to paint
    }
}
