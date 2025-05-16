<?php
// Get student info with prepared statement for security
$stmt = $conn->prepare("SELECT * FROM tblstudents WHERE Id = ?");
$stmt->bind_param("i", $_SESSION['userId']);
$stmt->execute();
$result = $stmt->get_result();
$rows = $result->fetch_assoc();

// Get current page name for menu highlighting
$current_page = basename($_SERVER['PHP_SELF']);

// Get absent count for notification badge
$stmt = $conn->prepare("SELECT COUNT(*) as absent_count FROM tblattendance WHERE admissionNo = ? AND status = '0' AND dateTimeTaken >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
$stmt->bind_param("s", $_SESSION['admissionNumber']);
$stmt->execute();
$absent_result = $stmt->get_result()->fetch_assoc();
$recent_absences = $absent_result['absent_count'];

// Get active session and term
$stmt = $conn->prepare("SELECT sessionName, termName FROM tblsessionterm 
                       INNER JOIN tblterm ON tblterm.Id = tblsessionterm.termId
                       WHERE tblsessionterm.isActive = '1'");
$stmt->execute();
$session_result = $stmt->get_result()->fetch_assoc();
?>

<!-- Sidebar -->
<ul class="navbar-nav sidebar sidebar-light accordion" id="accordionSidebar">
    <!-- Sidebar Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center bg-gradient-primary" href="index.php">
        <div class="sidebar-brand-icon">
            <img src="../img/logo/attnlg.jpg" alt="Logo">
        </div>
        <div class="sidebar-brand-text mx-3">Student Portal</div>
        <button onclick="w3_close()" class="close-btn d-md-none">&times;</button>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Dashboard -->
    <li class="nav-item <?php echo ($current_page == 'enhanced_dashboard.php' || $current_page == 'index.php') ? 'active' : ''; ?>">
        <a class="nav-link" href="enhanced_dashboard.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Attendance
    </div>

    <!-- Attendance Menu -->
    <li class="nav-item">
        <a class="nav-link <?php echo ($current_page == 'view_attendance.php' || $current_page == 'attendance_report.php') ? '' : 'collapsed'; ?>" 
           href="#" data-toggle="collapse" data-target="#attendanceSubmenu">
            <i class="fas fa-fw fa-calendar-check"></i>
            <span>My Attendance</span>
            <?php if($recent_absences > 0): ?>
            <span class="badge badge-danger badge-counter ml-2"><?php echo $recent_absences; ?></span>
            <?php endif; ?>
        </a>
        <div id="attendanceSubmenu" class="collapse <?php echo ($current_page == 'view_attendance.php' || $current_page == 'attendance_report.php') ? 'show' : ''; ?>">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item <?php echo ($current_page == 'view_attendance.php') ? 'active' : ''; ?>" href="view_attendance.php">
                    View Attendance
                </a>
                <a class="collapse-item <?php echo ($current_page == 'attendance_report.php') ? 'active' : ''; ?>" href="attendance_report.php">
                    Attendance Report
                </a>
            </div>
        </div>
    </li>

    <!-- Performance -->
    <li class="nav-item <?php echo ($current_page == 'view_performance.php') ? 'active' : ''; ?>">
        <a class="nav-link" href="view_performance.php">
            <i class="fas fa-fw fa-chart-line"></i>
            <span>Performance Analytics</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Account Settings -->
    <div class="sidebar-heading">
        Settings
    </div>

    <!-- Logout -->
    <li class="nav-item">
        <a class="nav-link" href="#" data-toggle="modal" data-target="#logoutModal">
            <i class="fas fa-fw fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>

<!-- Mobile Toggle Button -->
<button class="w3-button w3-teal w3-xlarge d-md-none mobile-toggle" onclick="w3_open()">☰</button>

<!-- Logout Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <a class="btn btn-primary" href="logout.php">Logout</a>
            </div>
        </div>
    </div>
</div>

<style>
.sidebar-card {
    padding: 1.5rem;
    margin: 0 1rem;
    border-radius: 0.75rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.nav-item .nav-link {
    border-radius: 0.5rem;
    margin: 0 1rem;
}

.nav-item .nav-link:hover {
    background-color: #f8f9fc;
}

.nav-item.active .nav-link {
    background-color: #eaecf4;
    color: #4e73df;
    font-weight: 600;
}

.nav-item .badge-counter {
    transform: scale(0.8);
}

.sidebar .nav-item .collapse-inner .collapse-item.active {
    background-color: #eaecf4;
    color: #4e73df;
    font-weight: 600;
}

.sidebar-heading {
    font-size: 0.8rem;
    font-weight: 600;
    color: #b7b9cc;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin: 0 1rem 0.5rem;
}

.sidebar-brand {
    height: 4.375rem;
    padding: 1.5rem 1rem;
    text-align: center;
}

.sidebar-brand .sidebar-brand-icon img {
    height: 2rem;
    width: auto;
}

.img-profile {
    border: 3px solid #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
</style>