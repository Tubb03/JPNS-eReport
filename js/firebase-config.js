import { initializeApp } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js";
import { getFirestore } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-firestore.js";
import { getStorage } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-storage.js";
import { getAuth } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-auth.js";

const firebaseConfig = {
    apiKey: "AIzaSyCR9EPwlakRDXN1v6Rm19ro7XvowvBnY2k",
    authDomain: "jpns-ereport.firebaseapp.com",
    projectId: "jpns-ereport",
    storageBucket: "jpns-ereport.firebasestorage.app",
    messagingSenderId: "928703430086",
    appId: "1:928703430086:web:273fc8a67f22ce1b9e86c7"
};

export const app = initializeApp(firebaseConfig);
export const db = getFirestore(app);
export const storage = getStorage(app);
export const auth = getAuth(app);
