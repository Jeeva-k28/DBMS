<?php 
error_reporting(0);
include '../Includes/dbcon.php';

// Validate that user is a Student
validate_session('Student');

// Get Student's Attendance Data
$admissionNumber = $_SESSION['admissionNumber'];
$query = "SELECT MONTH(dateTimeTaken) as month, 
          COUNT(*) as total_days,
          SUM(CASE WHEN status = '1' THEN 1 ELSE 0 END) as present_days
          FROM tblattendance 
          WHERE admissionNo = '$admissionNumber'
          AND YEAR(dateTimeTaken) = YEAR(CURRENT_DATE)
          GROUP BY MONTH(dateTimeTaken)
          ORDER BY MONTH(dateTimeTaken)";

$result = $conn->query($query);
$monthlyData = array();
$months = array();
$attendance = array();

while($row = $result->fetch_assoc()) {
    $monthName = date("F", mktime(0, 0, 0, $row['month'], 10));
    $months[] = $monthName;
    $percentage = ($row['present_days'] / $row['total_days']) * 100;
    $attendance[] = round($percentage, 2);
}

// Get Class Average
$classQuery = "SELECT a.dateTimeTaken, 
               COUNT(DISTINCT a.admissionNo) as total_students,
               SUM(CASE WHEN a.status = '1' THEN 1 ELSE 0 END) as present_students
               FROM tblattendance a
               WHERE a.classId = '$_SESSION[classId]'
               AND a.classArmId = '$_SESSION[classArmId]'
               GROUP BY a.dateTimeTaken";

$classResult = $conn->query($classQuery);
$totalAttendance = 0;
$totalDays = 0;

while($row = $classResult->fetch_assoc()) {
    $totalAttendance += $row['present_students'];
    $totalDays += $row['total_students'];
}

$classAverage = ($totalDays > 0) ? ($totalAttendance / $totalDays) * 100 : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link href="../img/logo/attnlg.jpg" rel="icon">
  <title>Performance Insights</title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="css/ruang-admin.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            <h1 class="h3 mb-0 text-gray-800">Performance Insights</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Performance</li>
            </ol>
          </div>

          <div class="row">
            <!-- Monthly Attendance Trends -->
            <div class="col-lg-8">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Monthly Attendance Trends</h6>
                </div>
                <div class="card-body">
                  <canvas id="attendanceChart"></canvas>
                </div>
              </div>
            </div>

            <!-- Class Average Comparison -->
            <div class="col-lg-4">
              <div class="card mb-4">
                <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold text-primary">Class Average Comparison</h6>
                </div>
                <div class="card-body">
                  <div class="mb-3">
                    <div class="small text-gray-500">Your Average Attendance
                      <div class="small float-right">
                        <b><?php echo number_format(array_sum($attendance)/count($attendance), 2); ?>%</b>
                      </div>
                    </div>
                    <div class="progress" style="height: 12px;">
                      <div class="progress-bar bg-warning" role="progressbar" style="width: <?php echo array_sum($attendance)/count($attendance); ?>%" aria-valuenow="<?php echo array_sum($attendance)/count($attendance); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                  <div class="mb-3">
                    <div class="small text-gray-500">Class Average
                      <div class="small float-right">
                        <b><?php echo number_format($classAverage, 2); ?>%</b>
                      </div>
                    </div>
                    <div class="progress" style="height: 12px;">
                      <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $classAverage; ?>%" aria-valuenow="<?php echo $classAverage; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
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
  
  <script>
    // Monthly Attendance Chart
    var ctx = document.getElementById('attendanceChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($months); ?>,
            datasets: [{
                label: 'Attendance Percentage',
                data: <?php echo json_encode($attendance); ?>,
                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                borderColor: 'rgba(78, 115, 223, 1)',
                borderWidth: 2,
                pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4
            }]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
  </script>
</body>
</html>