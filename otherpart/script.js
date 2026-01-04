/* --- ETHIO FOOD - Corrected Authentication Logic --- */

document.addEventListener("DOMContentLoaded", () => {
  // --- 1. Element Selectors ---
  const toggleBtns = document.querySelectorAll(".toggle-btn");
  const authForms = document.querySelectorAll(".auth-form");
  const loginForm = document.getElementById("login-form");
  const signupForm = document.getElementById("signup-form");

  // --- 2. Form Switching Logic (Visual Only) ---
  const showForm = (target) => {
    toggleBtns.forEach((b) => b.classList.remove("active"));
    authForms.forEach((f) => f.classList.remove("active"));

    const activeBtn = document.querySelector(`[data-form="${target}"]`);
    const activeForm = document.getElementById(`${target}-form`);

    if (activeBtn) activeBtn.classList.add("active");
    if (activeForm) activeForm.classList.add("active");
  };

  toggleBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
      showForm(btn.getAttribute("data-form"));
    });
  });

  // --- 3. Form Validation (Before PHP takes over) ---
  // STRESS TEST: We removed e.preventDefault() so the data actually reaches PHP!

  signupForm.addEventListener("submit", (e) => {
    const password = document.getElementById("signup-password").value;
    const confirmPassword = document.getElementById("signup-confirm").value;

    if (password !== confirmPassword) {
      e.preventDefault(); // ONLY stop if there is a mistake
      alert("Error: Passwords do not match!");
      return;
    }
  });

  // --- 4. URL Parameter Handler (Handling PHP responses) ---
  const urlParams = new URLSearchParams(window.location.search);

  if (urlParams.has("error")) {
    const error = urlParams.get("error");
    if (error === "emailexists") {
      alert("This email is already registered. Try logging in!");
      showForm("signup");
    } else if (error === "wrongpass") {
      alert("Incorrect password. Please try again.");
      showForm("login");
    } else if (error === "nouser") {
      alert("No account found with this email.");
      showForm("login");
    }
  }

  if (urlParams.get("signup") === "success") {
    alert("Account created successfully! Please log in.");
    showForm("login");
  }

  // --- 5. Social Login (Future Phase) ---
  document.querySelectorAll(".social-btn").forEach((btn) => {
    btn.addEventListener("click", (e) => {
      e.preventDefault();
      alert("Social login will be integrated soon via Google/Facebook APIs.");
    });
  });
});
