<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../Includes/dbcon.php';

// Validate that user is a Student
validate_session('Student');

// Get student basic info using prepared statement
$query = "SELECT tblstudents.*, tblclass.className, tblclassarms.classArmName
          FROM tblstudents
          INNER JOIN tblclass ON tblclass.Id = tblstudents.classId
          INNER JOIN tblclassarms ON tblclassarms.Id = tblstudents.classArmId
          WHERE tblstudents.Id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION['userId']);
$stmt->execute();
$student_info = $stmt->get_result()->fetch_assoc();

if (!$student_info) {
    die("Error: Student information not found.");
}

// Get attendance statistics using prepared statements
$stmt = $conn->prepare("SELECT * FROM tblattendance WHERE admissionNo = ?");
$stmt->bind_param("s", $_SESSION['admissionNumber']);
$stmt->execute();
$totalAttendance = $stmt->get_result()->num_rows;

$stmt = $conn->prepare("SELECT * FROM tblattendance WHERE admissionNo = ? AND status = '1'");
$stmt->bind_param("s", $_SESSION['admissionNumber']);
$stmt->execute();
$presentCount = $stmt->get_result()->num_rows;

// Calculate Attendance Percentage
$attendance_percentage = ($totalAttendance > 0) ? ($presentCount / $totalAttendance) * 100 : 0;

// Get recent attendance records using prepared statement
$recent_query = "SELECT a.dateTimeTaken as date, c.className, a.status 
    FROM tblattendance a
    JOIN tblclass c ON c.Id = a.classId
    WHERE a.admissionNo = ?
    ORDER BY a.dateTimeTaken DESC LIMIT 5";
$stmt = $conn->prepare($recent_query);
$stmt->bind_param("s", $_SESSION['admissionNumber']);
$stmt->execute();
$recent_attendance = $stmt->get_result();

// Get monthly attendance data for chart
$monthly_query = "SELECT 
    MONTH(dateTimeTaken) as month,
    COUNT(*) as total_days,
    SUM(CASE WHEN status = '1' THEN 1 ELSE 0 END) as present_days
    FROM tblattendance 
    WHERE admissionNo = ? 
    AND YEAR(dateTimeTaken) = YEAR(CURRENT_DATE)
    GROUP BY MONTH(dateTimeTaken)
    ORDER BY MONTH(dateTimeTaken)";
$stmt = $conn->prepare($monthly_query);
$stmt->bind_param("s", $_SESSION['admissionNumber']);
$stmt->execute();
$monthly_result = $stmt->get_result();

$months = [];
$attendance_data = [];
while($row = $monthly_result->fetch_assoc()) {
    $month_name = date("F", mktime(0, 0, 0, $row['month'], 10));
    $months[] = $month_name;
    $attendance_data[] = ($row['present_days'] / $row['total_days']) * 100;
}

$months_json = json_encode($months);
$attendance_json = json_encode($attendance_data);

// Get weekly attendance breakdown
$weekly_query = "SELECT 
    DAYNAME(dateTimeTaken) as day_name,
    COUNT(*) as total_days,
    SUM(CASE WHEN status = '1' THEN 1 ELSE 0 END) as present_days
    FROM tblattendance 
    WHERE admissionNo = ? 
    AND dateTimeTaken >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    GROUP BY DAYNAME(dateTimeTaken)
    ORDER BY DAYOFWEEK(dateTimeTaken)";
$stmt = $conn->prepare($weekly_query);
$stmt->bind_param("s", $_SESSION['admissionNumber']);
$stmt->execute();
$weekly_result = $stmt->get_result();

$days = [];
$weekly_data = [];
while($row = $weekly_result->fetch_assoc()) {
    $days[] = substr($row['day_name'], 0, 3);
    $weekly_data[] = ($row['present_days'] / $row['total_days']) * 100;
}

$days_json = json_encode($days);
$weekly_json = json_encode($weekly_data);

// Get Class Average
$classQuery = "SELECT a.dateTimeTaken, 
               COUNT(DISTINCT a.admissionNo) as total_students,
               SUM(CASE WHEN a.status = '1' THEN 1 ELSE 0 END) as present_students
               FROM tblattendance a
               WHERE a.classId = ? AND a.classArmId = ?
               GROUP BY a.dateTimeTaken";
$stmt = $conn->prepare($classQuery);
$stmt->bind_param("ss", $_SESSION['classId'], $_SESSION['classArmId']);
$stmt->execute();
$classResult = $stmt->get_result();
$totalAttendanceClass = 0;
$totalDaysClass = 0;

while($row = $classResult->fetch_assoc()) {
    $totalAttendanceClass += $row['present_students'];
    $totalDaysClass += $row['total_students'];
}

$classAverage = ($totalDaysClass > 0) ? ($totalAttendanceClass / $totalDaysClass) * 100 : 0;

// Get attendance ranking
$rankQuery = "SELECT admissionNo, 
             (SUM(CASE WHEN status = '1' THEN 1 ELSE 0 END) * 100.0 / COUNT(*)) as attendance_rate
             FROM tblattendance 
             WHERE classId = ? AND classArmId = ?
             GROUP BY admissionNo
             ORDER BY attendance_rate DESC";
$stmt = $conn->prepare($rankQuery);
$stmt->bind_param("ss", $_SESSION['classId'], $_SESSION['classArmId']);
$stmt->execute();
$rankResult = $stmt->get_result();
$rank = 1;
$totalStudents = $rankResult->num_rows;
$studentRank = 0;

while($row = $rankResult->fetch_assoc()) {
    if($row['admissionNo'] == $_SESSION['admissionNumber']) {
        $studentRank = $rank;
        break;
    }
    $rank++;
}

// Get monthly class average for comparison chart
$monthlyClassQuery = "SELECT 
    MONTH(dateTimeTaken) as month,
    COUNT(DISTINCT admissionNo) as total_students,
    SUM(CASE WHEN status = '1' THEN 1 ELSE 0 END) as present_count,
    COUNT(*) as total_count
    FROM tblattendance 
    WHERE classId = ? AND classArmId = ?
    AND YEAR(dateTimeTaken) = YEAR(CURRENT_DATE)
    GROUP BY MONTH(dateTimeTaken)
    ORDER BY MONTH(dateTimeTaken)";
$stmt = $conn->prepare($monthlyClassQuery);
$stmt->bind_param("ss", $_SESSION['classId'], $_SESSION['classArmId']);
$stmt->execute();
$monthlyClassResult = $stmt->get_result();

$class_monthly_data = [];
while($row = $monthlyClassResult->fetch_assoc()) {
    $class_monthly_data[$row['month']] = ($row['present_count'] / $row['total_count']) * 100;
}

// Prepare comparison data
$comparison_months = [];
$student_data = [];
$class_data = [];
foreach($months as $index => $month) {
    $month_num = date('n', strtotime($month));
    $comparison_months[] = $month;
    $student_data[] = $attendance_data[$index];
    $class_data[] = isset($class_monthly_data[$month_num]) ? $class_monthly_data[$month_num] : null;
}

$comparison_months_json = json_encode($comparison_months);
$student_data_json = json_encode($student_data);
$class_data_json = json_encode($class_data);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="css/ruang-admin.min.css" rel="stylesheet">
    <script src="../vendor/chart.js/Chart.min.js"></script>
    <style>
        body {
            background: linear-gradient(135deg, #f5f7ff 0%, #ffffff 100%);
        }
        #content-wrapper {
            background: transparent;
        }
        .attendance-progress {
            height: 25px;
            border-radius: 20px;
            background-color: rgba(78, 115, 223, 0.1);
        }
        .attendance-progress .progress-bar {
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
            line-height: 25px;
            background: linear-gradient(45deg, #4e73df 0%, #6f8de3 100%);
        }
        .card {
            transition: transform 0.2s, box-shadow 0.2s;
            border: none;
            border-radius: 15px;
            background: #ffffff;
            box-shadow: 0 4px 20px rgba(78, 115, 223, 0.1);
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(78, 115, 223, 0.15);
        }
        .recent-table {
            max-height: 300px;
            overflow-y: auto;
            border-radius: 10px;
        }
        .card-header {
            border-top-left-radius: 15px !important;
            border-top-right-radius: 15px !important;
            background: linear-gradient(45deg, #4e73df 0%, #6f8de3 100%);
            color: white !important;
            border-bottom: none;
        }
        .card-header h6 {
            color: white !important;
        }
        .badge {
            font-size: 1rem;
            padding: 0.5rem 1rem;
            background: rgba(78, 115, 223, 0.1);
            color: #4e73df;
        }
        .table th {
            border-top: none;
            font-weight: 600;
            color: #4e73df;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(78, 115, 223, 0.05);
        }
        .container-fluid {
            padding: 1.5rem;
        }
        .progress-bar.bg-light {
            background: linear-gradient(45deg, #4e73df 0%, #6f8de3 100%) !important;
            color: white;
        }
        .attendance-chart {
            height: 300px;
            padding: 1rem;
        }
        .performance-card {
            border-radius: 15px;
            background: #ffffff;
            box-shadow: 0 4px 20px rgba(78, 115, 223, 0.1);
        }
        .btn-outline-primary {
            border-color: #4e73df;
            color: #4e73df;
        }
        .btn-outline-primary:hover, 
        .btn-outline-primary.active {
            background: linear-gradient(45deg, #4e73df 0%, #6f8de3 100%);
            border-color: transparent;
        }
        .text-primary {
            color: #4e73df !important;
        }
        .bg-primary {
            background: linear-gradient(45deg, #4e73df 0%, #6f8de3 100%) !important;
        }
        .bg-success {
            background: linear-gradient(45deg, #1cc88a 0%, #2dce9d 100%) !important;
        }
        .bg-danger {
            background: linear-gradient(45deg, #e74a3b 0%, #ea6a5e 100%) !important;
        }
    </style>
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
                
                <div class="container-fluid" id="container-wrapper">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <div>
                            <h2 class="mb-0">Welcome, <?php echo htmlspecialchars($student_info['firstName']); ?>!</h2>
                            <p class="text-muted mb-0">Here's your attendance overview</p>
                        </div>
                        <div class="badge badge-primary">
                            <i class="fas fa-id-card mr-2"></i>
                            <?php echo htmlspecialchars($student_info['admissionNumber']); ?>
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card text-white bg-primary h-100">
                                <div class="card-body">
                                    <h5 class="card-title font-weight-bold">Attendance Rate</h5>
                                    <div class="progress attendance-progress mt-3">
                                        <div class="progress-bar bg-light" role="progressbar" 
                                            style="width: <?php echo $attendance_percentage; ?>%" 
                                            aria-valuenow="<?php echo $attendance_percentage; ?>" 
                                            aria-valuemin="0" aria-valuemax="100">
                                            <?php echo round($attendance_percentage, 1); ?>%
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-white bg-success h-100">
                                <div class="card-body">
                                    <h5 class="card-title font-weight-bold">Present Days</h5>
                                    <div class="d-flex align-items-center mt-3">
                                        <h2 class="mb-0"><?php echo $presentCount; ?></h2>
                                        <p class="mb-0 ml-2">of <?php echo $totalAttendance; ?> total</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-white bg-danger h-100">
                                <div class="card-body">
                                    <h5 class="card-title font-weight-bold">Absent Days</h5>
                                    <h2 class="mt-3 mb-0"><?php echo $totalAttendance - $presentCount; ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light h-100">
                                <div class="card-body">
                                    <h5 class="card-title font-weight-bold">Current Status</h5>
                                    <div class="mt-3">
                                        <h2 class="<?php echo $attendance_percentage >= 75 ? 'text-success' : 'text-danger'; ?> mb-0">
                                            <?php echo $attendance_percentage >= 75 ? 'Good Standing' : 'Needs Improvement'; ?>
                                        </h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Performance Analysis -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="card performance-card">
                                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                    <h6 class="m-0 font-weight-bold text-primary">Attendance Comparison</h6>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-outline-primary active" onclick="showChart('monthly')">Monthly</button>
                                        <button type="button" class="btn btn-outline-primary" onclick="showChart('weekly')">Weekly</button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="attendance-chart" id="monthlyChart">
                                        <canvas id="comparisonChart"></canvas>
                                    </div>
                                    <div class="attendance-chart" id="weeklyChart" style="display: none;">
                                        <canvas id="weeklyPatternChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card performance-card">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Performance Overview</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-4">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-sm font-weight-bold">Your Attendance</span>
                                            <span class="text-sm text-primary"><?php echo round($attendance_percentage, 1); ?>%</span>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo $attendance_percentage; ?>%"></div>
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-sm font-weight-bold">Class Average</span>
                                            <span class="text-sm text-info"><?php echo round($classAverage, 1); ?>%</span>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-info" role="progressbar" style="width: <?php echo $classAverage; ?>%"></div>
                                        </div>
                                    </div>
                                    <div class="text-center mt-4">
                                        <div class="mb-3">
                                            <h3 class="font-weight-bold text-primary mb-0">#<?php echo $studentRank; ?></h3>
                                            <small class="text-muted">Class Rank</small>
                                        </div>
                                        <div class="d-flex justify-content-around">
                                            <div>
                                                <h5 class="font-weight-bold mb-0"><?php echo $totalStudents; ?></h5>
                                                <small class="text-muted">Total Students</small>
                                            </div>
                                            <div>
                                                <h5 class="font-weight-bold mb-0"><?php echo round(($totalStudents - $studentRank + 1) / $totalStudents * 100); ?>%</h5>
                                                <small class="text-muted">Percentile</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Attendance -->
                    <div class="card mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Recent Attendance History</h6>
                        </div>
                        <div class="card-body recent-table">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Class</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    if($recent_attendance->num_rows > 0):
                                        while($row = $recent_attendance->fetch_assoc()): 
                                    ?>
                                    <tr>
                                        <td><?php echo date('F j, Y', strtotime($row['date'])); ?></td>
                                        <td><?php echo htmlspecialchars($row['className']); ?></td>
                                        <td>
                                            <?php if($row['status'] == '1'): ?>
                                                <span class="text-success">
                                                    <i class="fas fa-check-circle mr-1"></i>Present
                                                </span>
                                            <?php else: ?>
                                                <span class="text-danger">
                                                    <i class="fas fa-times-circle mr-1"></i>Absent
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php 
                                        endwhile;
                                    else:
                                    ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No recent attendance records found</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
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
        // Attendance Trend Chart
        var ctx = document.getElementById('attendanceChart').getContext('2d');
        var attendanceChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo $months_json; ?>,
                datasets: [{
                    label: 'Attendance Rate %',
                    data: <?php echo $attendance_json; ?>,
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointBorderColor: '#fff',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
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
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Attendance: ' + Math.round(context.raw) + '%';
                            }
                        }
                    }
                }
            }
        });

        // Weekly Pattern Chart
        var weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
        var weeklyChart = new Chart(weeklyCtx, {
            type: 'bar',
            data: {
                labels: <?php echo $days_json; ?>,
                datasets: [{
                    label: 'Attendance Rate %',
                    data: <?php echo $weekly_json; ?>,
                    backgroundColor: 'rgba(78, 115, 223, 0.8)',
                    borderWidth: 0,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
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
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Attendance: ' + Math.round(context.raw) + '%';
                            }
                        }
                    }
                }
            }
        });

        // Comparison Chart
        var compCtx = document.getElementById('comparisonChart').getContext('2d');
        var comparisonChart = new Chart(compCtx, {
            type: 'line',
            data: {
                labels: <?php echo $comparison_months_json; ?>,
                datasets: [{
                    label: 'Your Attendance',
                    data: <?php echo $student_data_json; ?>,
                    borderColor: 'rgba(78, 115, 223, 1)',
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }, {
                    label: 'Class Average',
                    data: <?php echo $class_data_json; ?>,
                    borderColor: 'rgba(28, 200, 138, 1)',
                    backgroundColor: 'rgba(28, 200, 138, 0.05)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
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
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + Math.round(context.raw) + '%';
                            }
                        }
                    }
                }
            }
        });

        // Weekly Pattern Chart (existing code)
        var weeklyCtx = document.getElementById('weeklyPatternChart').getContext('2d');
        var weeklyChart = new Chart(weeklyCtx, {
            type: 'bar',
            data: {
                labels: <?php echo $days_json; ?>,
                datasets: [{
                    label: 'Attendance Rate %',
                    data: <?php echo $weekly_json; ?>,
                    backgroundColor: 'rgba(78, 115, 223, 0.8)',
                    borderWidth: 0,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
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
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Attendance: ' + Math.round(context.raw) + '%';
                            }
                        }
                    }
                }
            }
        });

        // Chart Toggle Function
        function showChart(type) {
            document.getElementById('monthlyChart').style.display = type === 'monthly' ? 'block' : 'none';
            document.getElementById('weeklyChart').style.display = type === 'weekly' ? 'block' : 'none';
            
            // Update button states
            document.querySelectorAll('.btn-group .btn').forEach(btn => {
                btn.classList.toggle('active', btn.textContent.toLowerCase().includes(type));
            });
        }
    </script>
</body>
</html>