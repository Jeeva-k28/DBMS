function switchModule(module) {
    document.getElementById("module-display").textContent = module + " Login";
    document.getElementById("teacher-btn").classList.remove("active");
    document.getElementById("admin-btn").classList.remove("active");
    document.getElementById(module.toLowerCase() + "-btn").classList.add("active");
}

function redirectToDashboard() {
    var module = document.getElementById("module-display").textContent;
    
    if (module.includes("Admin")) {
        sessionStorage.setItem("loggedIn", "true"); // Store session status
        window.location.href = "adminhome.html"; // Redirect to Admin Home Page
    } else if (module.includes("Teacher")) {
        sessionStorage.setItem("loggedIn", "true"); // Store session status
        window.location.href = "teacherhome.html"; // Redirect to Teacher Home Page
    }
}