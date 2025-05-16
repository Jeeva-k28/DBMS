<?php
include('../Includes/dbcon.php');
include('../Includes/session.php');

// Get total attendance percentage
$query = "SELECT 
    COUNT(CASE WHEN status = '1' THEN 1 END) * 100.0 / COUNT(*) as attendance_percentage,
    COUNT(CASE WHEN status = '1' THEN 1 END) as classes_attended,
    COUNT(CASE WHEN status = '0' THEN 1 END) as classes_missed,
    COUNT(*) as total_classes
FROM tblattendance 
WHERE admissionNo = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $_SESSION['admissionNumber']);
$stmt->execute();
$result = $stmt->get_result();
$attendance_stats = $result->fetch_assoc();

// Get current term info
$query = "SELECT tblterm.termName, tblsessionterm.sessionName 
          FROM tblterm 
          INNER JOIN tblsessionterm ON tblterm.sessionTermId = tblsessionterm.Id 
          WHERE tblterm.isActive = 1";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$term_info = $result->fetch_assoc();

// Get student's class and subject info
$query = "SELECT c.className, ca.classArmName, COUNT(DISTINCT s.Id) as total_subjects
          FROM tblstudents st
          INNER JOIN tblclass c ON st.classId = c.Id
          INNER JOIN tblclassarms ca ON st.classArmId = ca.Id
          INNER JOIN tblsubjects s ON c.Id = s.classId
          WHERE st.admissionNumber = ?
          GROUP BY c.className, ca.classArmName";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $_SESSION['admissionNumber']);
$stmt->execute();
$result = $stmt->get_result();
$class_info = $result->fetch_assoc();

// Get weekly attendance data for the chart
$query = "SELECT 
    WEEK(dateTimeTaken) as week_num,
    COUNT(CASE WHEN status = '1' THEN 1 END) * 100.0 / COUNT(*) as attendance_rate
FROM tblattendance 
WHERE admissionNo = ? 
    AND dateTimeTaken >= DATE_SUB(CURDATE(), INTERVAL 4 WEEK)
GROUP BY WEEK(dateTimeTaken)
ORDER BY week_num";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $_SESSION['admissionNumber']);
$stmt->execute();
$result = $stmt->get_result();
$weekly_data = array();
$week_labels = array();
while($row = $result->fetch_assoc()) {
    $weekly_data[] = $row['attendance_rate'];
    $week_labels[] = 'Week ' . $row['week_num'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <link href="img/logo/attnlg.jpg" rel="icon">
  <title>Dashboard</title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="css/ruang-admin.min.css" rel="stylesheet">
</head>

<body id="page-top">
  <div id="wrapper">
    <!-- Sidebar -->
    <?php include "Includes/sidebar.php";?>
    <!-- Sidebar -->
    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <!-- TopBar -->
        <?php include "Includes/topbar.php";?>
        <!-- Topbar -->
        <!-- Container Fluid-->
        <div class="container-fluid" id="container-wrapper">
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Student Dashboard</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
            </ol>
          </div>

          <div class="row mb-3">
            <!-- Total Attendance Card -->
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card h-100">
                <div class="card-body">
                  <div class="row align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-uppercase mb-1">Total Attendance</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($attendance_stats['attendance_percentage'], 1); ?>%</div>
                      <div class="mt-2 mb-0 text-muted text-xs">
                        <span>This Term</span>
                      </div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-calendar fa-2x text-primary"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Classes Attended Card -->
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card h-100">
                <div class="card-body">
                  <div class="row align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-uppercase mb-1">Classes Attended</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $attendance_stats['classes_attended']; ?></div>
                      <div class="mt-2 mb-0 text-muted text-xs">
                        <span>Out of <?php echo $attendance_stats['total_classes']; ?> classes</span>
                      </div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-check-circle fa-2x text-success"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Current Term Card -->
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card h-100">
                <div class="card-body">
                  <div class="row align-items-center">
                    <div class="col mr-2">
+                      <div class="text-xs font-weight-bold text-uppercase mb-1">Current Term</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $term_info['termName']; ?></div>
                      <div class="mt-2 mb-0 text-muted text-xs">
                        <span><?php echo $term_info['sessionName']; ?> Session</span>
                      </div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-clock fa-2x text-info"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Class Info Card -->
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card h-100">
                <div class="card-body">
                  <div class="row align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-uppercase mb-1">Class</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $class_info['className']; ?> - <?php echo $class_info['classArmName']; ?></div>
                      <div class="mt-2 mb-0 text-muted text-xs">
                        <span><?php echo $class_info['total_subjects']; ?> Subjects</span>
                      </div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-graduation-cap fa-2x text-warning"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Attendance Chart -->
            <div class="col-xl-8 col-lg-7">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Attendance Overview</h6>
                </div>
                <div class="card-body">
                  <div class="chart-area" style="height: 300px;">
                    <canvas id="attendanceChart"></canvas>
                  </div>
                </div>
              </div>
            </div>

            <!-- Recent Attendance -->
            <div class="col-xl-4 col-lg-5">
              <div class="card">
                <div class="card-header py-4 bg-primary d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-white">Recent Attendance</h6>
                </div>
                <div class="table-responsive">
                  <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                      <tr>
                        <th>Date</th>
                        <th>Status</th>
                      </tr>
                    </thead>
                    <tbody>
                    <?php 
                      $query = "SELECT dateTimeTaken, status FROM tblattendance 
                               WHERE admissionNo = ? 
                               ORDER BY dateTimeTaken DESC LIMIT 5";
                      $stmt = $conn->prepare($query);
                      $stmt->bind_param("s", $_SESSION['admissionNumber']);
                      $stmt->execute();
                      $result = $stmt->get_result();
                      while($row = $result->fetch_assoc()):
                    ?>
                      <tr>
                        <td><?php echo date('M d, Y', strtotime($row['dateTimeTaken'])); ?></td>
                        <td>
                          <?php if($row['status'] == '1'): ?>
                            <span class="badge badge-success">Present</span>
                          <?php else: ?>
                            <span class="badge badge-danger">Absent</span>
                          <?php endif; ?>
                        </td>
                      </tr>
                    <?php endwhile; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!---Container Fluid-->
      </div>
    </div>
  </div>

  <!-- Scroll to top -->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="js/ruang-admin.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  
  <script>
    // Attendance Chart with dynamic data
    var ctx = document.getElementById('attendanceChart').getContext('2d');
    var myChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: <?php echo json_encode($week_labels); ?>,
        datasets: [{
          label: 'Attendance Rate',
          data: <?php echo json_encode($weekly_data); ?>,
          backgroundColor: 'rgba(78, 115, 223, 0.05)',
          borderColor: 'rgba(78, 115, 223, 1)',
          pointRadius: 3,
          pointBackgroundColor: 'rgba(78, 115, 223, 1)',
          pointBorderColor: 'rgba(78, 115, 223, 1)',
          pointHoverRadius: 3,
          pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
          pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
          pointHitRadius: 10,
          pointBorderWidth: 2,
          fill: true
        }]
      },
      options: {
        maintainAspectRatio: false,
        layout: {
          padding: {
            left: 10,
            right: 25,
            top: 25,
            bottom: 0
          }
        },
        scales: {
          xAxes: [{
            gridLines: {
              display: false,
              drawBorder: false
            },
            ticks: {
              maxTicksLimit: 7
            }
          }],
          yAxes: [{
            ticks: {
              maxTicksLimit: 5,
              padding: 10,
              callback: function(value) {
                return value + '%';
              }
            },
            gridLines: {
              color: "rgb(234, 236, 244)",
              zeroLineColor: "rgb(234, 236, 244)",
              drawBorder: false,
              borderDash: [2],
              zeroLineBorderDash: [2]
            }
          }],
        },
        legend: {
          display: false
        },
        tooltips: {
          backgroundColor: "rgb(255,255,255)",
          bodyFontColor: "#858796",
          titleMarginBottom: 10,
          titleFontColor: '#6e707e',
          titleFontSize: 14,
          borderColor: '#dddfeb',
          borderWidth: 1,
          xPadding: 15,
          yPadding: 15,
          displayColors: false,
          intersect: false,
          mode: 'index',
          caretPadding: 10,
          callbacks: {
            label: function(tooltipItem, chart) {
              var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
              return datasetLabel + ': ' + tooltipItem.yLabel + '%';
            }
          }
        }
      }
    });
  </script>
</body>
</html>