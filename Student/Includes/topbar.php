<?php 
  $stmt = $conn->prepare("SELECT * FROM tblstudents WHERE Id = ?");
  $stmt->bind_param("i", $_SESSION['userId']);
  $stmt->execute();
  $result = $stmt->get_result();
  $rows = $result->fetch_assoc();

  // Get attendance alerts
  $stmt = $conn->prepare("SELECT COUNT(*) as absent_count FROM tblattendance WHERE admissionNo = ? AND status = '0' AND dateTimeTaken >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
  $stmt->bind_param("s", $_SESSION['admissionNumber']);
  $stmt->execute();
  $absent_result = $stmt->get_result()->fetch_assoc();
  $recent_absences = $absent_result['absent_count'];
?>
<!-- Topbar -->
<nav class="navbar navbar-expand navbar-light bg-navbar topbar mb-4 static-top">
  <!-- Sidebar Toggle (Topbar) -->
  <button id="sidebarToggleTop" class="btn btn-link rounded-circle mr-3">
    <i class="fa fa-bars"></i>
  </button>

  <!-- Topbar Navbar -->
  <ul class="navbar-nav ml-auto">
    <!-- Nav Item - Alerts -->
    <li class="nav-item dropdown no-arrow mx-1">
      <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-bell fa-fw"></i>
        <?php if ($recent_absences > 0): ?>
        <span class="badge badge-danger badge-counter"><?php echo $recent_absences; ?></span>
        <?php endif; ?>
      </a>
      <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
        <h6 class="dropdown-header">
          Attendance Alerts
        </h6>
        <?php if ($recent_absences > 0): ?>
          <a class="dropdown-item d-flex align-items-center" href="view_attendance.php">
            <div class="mr-3">
              <div class="icon-circle bg-warning">
                <i class="fas fa-exclamation-triangle text-white"></i>
              </div>
            </div>
            <div>
              <div class="small text-gray-500">Last 7 Days</div>
              <span class="font-weight-bold">You have been absent <?php echo $recent_absences; ?> time<?php echo $recent_absences > 1 ? 's' : ''; ?></span>
            </div>
          </a>
        <?php else: ?>
          <a class="dropdown-item text-center small text-gray-500" href="#">No recent absences</a>
        <?php endif; ?>
      </div>
    </li>

    <div class="topbar-divider d-none d-sm-block"></div>

    <!-- Nav Item - User Information -->
    <li class="nav-item dropdown no-arrow">
      <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-user-circle fa-fw"></i>
      </a>
      <!-- Dropdown - User Information -->
      <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
        <a class="dropdown-item" href="profile.php">
          <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
          Profile
        </a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" href="logout.php">
          <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
          Logout
        </a>
      </div>
    </li>
  </ul>
</nav>