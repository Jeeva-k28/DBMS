window.onload = function() {
    setActive("home-link"); // Auto-select Home page on load
};

// Function to switch between modules while maintaining background transitions
function setActive(moduleId) {
    let modules = document.querySelectorAll(".navbar a");
    modules.forEach(module => module.classList.remove("active"));
    document.getElementById(moduleId).classList.add("active");

    document.getElementById("class-module").style.display = moduleId === "classes-link" ? "block" : "none";
    document.getElementById("welcome-section").style.display = moduleId === "home-link" ? "flex" : "none";

    // Background transitions remain intact when switching modules
    let background = document.getElementById("background");
    if (moduleId === "home-link") {
        background.style.backgroundImage = "url('images/adminhome-background.png')";
        background.style.opacity = "1";
    } else if (moduleId === "classes-link") {
        background.style.backgroundImage = "url('images/other-modules-background.jpg')";
        background.style.opacity = "1";
    }
}

// Smooth logout function that redirects to login page
function logout() {
    sessionStorage.removeItem("loggedIn");
    window.location.href = "login.html";
}

// Open popup with background fade effect
function openPopup() {
    document.getElementById("popup").style.display = "block";
    document.getElementById("background").style.opacity = "0.5"; // Background fades
}

// Close popup and restore background opacity
function closePopup() {
    document.getElementById("popup").style.display = "none";
    document.getElementById("background").style.opacity = "1"; // Restore background opacity
}

// Save class data dynamically to table inside the Classes module
function saveClass() {
    let className = document.getElementById("class-name").value.trim();
    let sectionName = document.getElementById("section-name").value.trim();

    if (className && sectionName) {
        let table = document.getElementById("class-list");
        let row = table.insertRow();
        row.innerHTML = `<td>${table.rows.length}</td>
                         <td>${className}</td>
                         <td>${sectionName}</td>
                         <td><button class='action-btn' onclick='editClass(this)'>Edit</button> 
                             <button class='action-btn' onclick='deleteClass(this)'>Delete</button></td>`;
        closePopup(); // Close popup and restore background after saving
    }
}

// Edit function for modifying class details
function editClass(button) {
    let row = button.parentNode.parentNode;
    let className = row.cells[1].textContent;
    let sectionName = row.cells[2].textContent;

    let newClassName = prompt("Edit Class Name:", className);
    let newSectionName = prompt("Edit Section Name:", sectionName);

    if (newClassName && newSectionName) {
        row.cells[1].textContent = newClassName;
        row.cells[2].textContent = newSectionName;
    }
}

// Delete function for removing a class entry
function deleteClass(button) {
    let row = button.parentNode.parentNode;
    row.remove();
}