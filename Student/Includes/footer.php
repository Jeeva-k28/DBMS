<!-- Footer -->
<footer class="sticky-footer bg-white">
    <div class="container my-auto">
        <div class="copyright text-center my-auto">
            <span>copyright &copy; <script> document.write(new Date().getFullYear()); </script> - developed by
                <b><a href="#" target="_blank">Student Attendance System</a></b>
            </span>
        </div>
    </div>
</footer>
<!-- Footer -->

<!-- Scroll to top -->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/ruang-admin.min.js"></script>

<script>
// Mobile sidebar toggle functions
function w3_open() {
    document.getElementById("accordionSidebar").style.display = "block";
    document.getElementById("accordionSidebar").classList.add("mobile-sidebar-show");
}

function w3_close() {
    document.getElementById("accordionSidebar").style.display = "none";
    document.getElementById("accordionSidebar").classList.remove("mobile-sidebar-show");
}

// Handle mobile responsiveness
$(document).ready(function() {
    // Close sidebar when clicking outside on mobile
    $(document).on('click', function(e) {
        if ($(window).width() < 768) {
            if (!$(e.target).closest('#accordionSidebar, .mobile-toggle').length) {
                w3_close();
            }
        }
    });

    // Reset sidebar display when resizing window
    $(window).resize(function() {
        if ($(window).width() >= 768) {
            document.getElementById("accordionSidebar").style.display = "flex";
        }
    });
});
</script>

<style>
@media (max-width: 768px) {
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        z-index: 1050;
        display: none;
        width: 250px !important;
    }
    
    .mobile-sidebar-show {
        display: block !important;
    }
    
    .mobile-toggle {
        position: fixed;
        top: 1rem;
        left: 1rem;
        z-index: 1040;
        padding: 0.5rem 0.75rem;
        border-radius: 0.35rem;
        background-color: #4e73df;
        color: white;
    }
    
    .close-btn {
        position: absolute;
        top: 0.5rem;
        right: 1rem;
        background: transparent;
        border: none;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
    }
    
    #content-wrapper {
        margin-left: 0 !important;
    }
}
</style>