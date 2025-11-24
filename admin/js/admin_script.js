// <!-- admin/js/admin_script.js content goes here -->

// Sidebar toggle functionality
document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.getElementById("toggleSidebar");
    const sidebar = document.querySelector(".sidebar");
    const content = document.querySelector(".content");

    toggleBtn.addEventListener("click", () => {
        sidebar.classList.toggle("collapsed");
        content.classList.toggle("collapsed");
    });
});

