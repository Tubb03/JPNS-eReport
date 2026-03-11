import { initializeApp } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js";
import { getFirestore, collection, addDoc } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-firestore.js";
import { getStorage, ref, uploadBytesResumable, getDownloadURL } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-storage.js";

const firebaseConfig = {
    apiKey: "AIzaSyCR9EPwlakRDXN1v6Rm19ro7XvowvBnY2k",
    authDomain: "jpns-ereport.firebaseapp.com",
    projectId: "jpns-ereport",
    storageBucket: "jpns-ereport.firebasestorage.app",
    messagingSenderId: "928703430086",
    appId: "1:928703430086:web:273fc8a67f22ce1b9e86c7"
};

const app = initializeApp(firebaseConfig);
const db = getFirestore(app);
const storage = getStorage(app);

const imageUpload = document.getElementById('imageUpload');
const previewGallery = document.getElementById('previewGallery');
let selectedFiles = [];

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
    const files = Array.from(e.target.files);
    selectedFiles = [...selectedFiles, ...files];
    updatePreviews();
    imageUpload.value = "";
});

window.removeFile = (index) => {
    selectedFiles.splice(index, 1);
    updatePreviews();
};

function updatePreviews() {
    previewGallery.innerHTML = "";
    selectedFiles.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = (e) => {
            const div = document.createElement('div');
            div.className = "relative group";
            div.innerHTML = `
                <img src="${e.target.result}" class="w-full h-20 object-cover rounded shadow-sm border">
                <button type="button" onclick="removeFile(${index})" class="absolute -top-1 -right-1 bg-red-600 text-white rounded-full w-5 h-5 text-[10px] font-bold shadow-lg hover:bg-red-700">✕</button>
            `;
            previewGallery.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
}

document.getElementById('reportForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('btn');
    const progContainer = document.getElementById('progressContainer');
    const progBar = document.getElementById('progressBar');

    if (selectedFiles.length === 0) return alert("Select at least one photo!");

    btn.disabled = true;
    btn.innerText = "UPLOADING...";
    progContainer.classList.remove('hidden');

    let urls = [];
    try {
        for (let i = 0; i < selectedFiles.length; i++) {
            const file = selectedFiles[i];
            const sRef = ref(storage, `images/${Date.now()}_${file.name}`);
            const uploadTask = uploadBytesResumable(sRef, file);

            await new Promise((resolve, reject) => {
                uploadTask.on('state_changed',
                    (snap) => {
                        const progress = (snap.bytesTransferred / snap.totalBytes) * 100;
                        progBar.style.width = progress + "%";
                        progBar.innerText = `PHOTO ${i + 1}/${selectedFiles.length}: ${Math.round(progress)}%`;
                    },
                    (err) => reject(err),
                    async () => {
                        const dUrl = await getDownloadURL(uploadTask.snapshot.ref);
                        urls.push(dUrl);
                        resolve();
                    }
                );
            });
        }

        await addDoc(collection(db, "reports"), {
            name: document.getElementById('userName').value,
            program: document.getElementById('programName').value,
            unit: document.getElementById('unit').value,
            date: document.getElementById('programDate').value,
            description: document.getElementById('description').value,
            objective: document.getElementById('objective').value,
            fullReport: document.getElementById('fullReport').value,
            imageUrls: urls,
            createdAt: new Date()
        });

        alert("Submitted successfully!");
        location.href = "dashboard.html";
    } catch (err) {
        console.error(err);
        alert("Upload failed.");
        btn.disabled = false;
    }
});
