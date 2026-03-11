import { auth } from "./firebase-config.js";
import { signInWithEmailAndPassword, onAuthStateChanged } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-auth.js";

const loginForm = document.getElementById('loginForm');
const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');
const errorMsg = document.getElementById('errorMsg');
const loginBtn = document.getElementById('loginBtn');

// Redirect user if they are already logged in
onAuthStateChanged(auth, (user) => {
    if (user) {
        window.location.href = "dashboard.html";
    }
});

loginForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const email = emailInput.value.trim();
    const password = passwordInput.value;

    errorMsg.classList.add('hidden');
    loginBtn.disabled = true;
    loginBtn.innerText = "Authenticating...";

    try {
        await signInWithEmailAndPassword(auth, email, password);
        // Successful login will trigger the onAuthStateChanged observer above
    } catch (error) {
        console.error("Login failed:", error.message);
        errorMsg.classList.remove('hidden');
        loginBtn.disabled = false;
        loginBtn.innerText = "Log In";

        switch (error.code) {
            case 'auth/invalid-email':
            case 'auth/user-not-found':
            case 'auth/wrong-password':
            case 'auth/invalid-credential':
                errorMsg.innerText = "Invalid email or password";
                break;
            default:
                errorMsg.innerText = "An error occurred. Please try again.";
        }
    }
});
