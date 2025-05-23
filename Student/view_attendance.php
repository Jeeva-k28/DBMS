<?php 
error_reporting(0);
include '../Includes/dbcon.php';

// Validate that user is a Student
validate_session('Student');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <link href="../img/logo/attnlg.jpg" rel="icon">
  <title>Student - View Attendance</title>
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
            <h1 class="h3 mb-0 text-gray-800">View My Attendance</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">View Attendance</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <!-- Form Basic -->
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">View Attendance Records</h6>
                </div>
                <div class="card-body">
                  <form method="post">
                    <div class="form-group row mb-3">
                      <div class="col-xl-6">
                        <label class="form-control-label">Select Type<span class="text-danger ml-2">*</span></label>
                        <select required name="type" class="form-control mb-3" onchange="typeDropDown(this.value)">
                          <option value="">--Select--</option>
                          <option value="1">All</option>
                          <option value="2">By Single Date</option>
                          <option value="3">By Date Range</option>
                        </select>
                      </div>
                    </div>
                    <?php echo "<div id='txtHint'></div>"?>
                    <button type="submit" name="view" class="btn btn-primary">View Attendance</button>
                  </form>
                </div>
              </div>

              <!-- Attendance Records -->
              <div class="row">
                <div class="col-lg-12">
                  <div class="card mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                      <h6 class="m-0 font-weight-bold text-primary">Attendance Records</h6>
                    </div>
                    <div class="table-responsive p-3">
                      <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                        <thead class="thead-light">
                          <tr>
                            <th>#</th>
                            <th>Class</th>
                            <th>Class Arm</th>
                            <th>Student ID</th>
                            <th>Session</th>
                            <th>Term</th>
                            <th>Status</th>
                            <th>Date</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          if(isset($_POST['view'])){
                            $admissionNumber = $_SESSION['admissionNumber'];
                            $type = $_POST['type'];

                            if($type == "1"){ //All Attendance
                              $query = "SELECT tblattendance.Id,tblattendance.status,tblattendance.dateTimeTaken,tblclass.className,
                              tblclassarms.classArmName,tblsessionterm.sessionName,tblsessionterm.termId,tblterm.termName,
                              tblstudents.student_id
                              FROM tblattendance
                              INNER JOIN tblclass ON tblclass.Id = tblattendance.classId
                              INNER JOIN tblclassarms ON tblclassarms.Id = tblattendance.classArmId
                              INNER JOIN tblsessionterm ON tblsessionterm.Id = tblattendance.sessionTermId
                              INNER JOIN tblterm ON tblterm.Id = tblsessionterm.termId
                              INNER JOIN tblstudents ON tblstudents.admissionNumber = tblattendance.admissionNo
                              WHERE tblattendance.admissionNo = '$admissionNumber'
                              ORDER BY tblattendance.dateTimeTaken DESC";
                            }
                            else if($type == "2"){ //Single Date
                              $singleDate = $_POST['singleDate'];
                              $query = "SELECT tblattendance.Id,tblattendance.status,tblattendance.dateTimeTaken,tblclass.className,
                              tblclassarms.classArmName,tblsessionterm.sessionName,tblsessionterm.termId,tblterm.termName,
                              tblstudents.student_id
                              FROM tblattendance
                              INNER JOIN tblclass ON tblclass.Id = tblattendance.classId
                              INNER JOIN tblclassarms ON tblclassarms.Id = tblattendance.classArmId
                              INNER JOIN tblsessionterm ON tblsessionterm.Id = tblattendance.sessionTermId
                              INNER JOIN tblterm ON tblterm.Id = tblsessionterm.termId
                              INNER JOIN tblstudents ON tblstudents.admissionNumber = tblattendance.admissionNo
                              WHERE tblattendance.dateTimeTaken = '$singleDate' AND tblattendance.admissionNo = '$admissionNumber'";
                            }
                            else if($type == "3"){ //Date Range
                              $fromDate = $_POST['fromDate'];
                              $toDate = $_POST['toDate'];
                              $query = "SELECT tblattendance.Id,tblattendance.status,tblattendance.dateTimeTaken,tblclass.className,
                              tblclassarms.classArmName,tblsessionterm.sessionName,tblsessionterm.termId,tblterm.termName,
                              tblstudents.student_id
                              FROM tblattendance
                              INNER JOIN tblclass ON tblclass.Id = tblattendance.classId
                              INNER JOIN tblclassarms ON tblclassarms.Id = tblattendance.classArmId
                              INNER JOIN tblsessionterm ON tblsessionterm.Id = tblattendance.sessionTermId
                              INNER JOIN tblterm ON tblterm.Id = tblsessionterm.termId
                              INNER JOIN tblstudents ON tblstudents.admissionNumber = tblattendance.admissionNo
                              WHERE tblattendance.dateTimeTaken BETWEEN '$fromDate' AND '$toDate' 
                              AND tblattendance.admissionNo = '$admissionNumber'";
                            }

                            $rs = $conn->query($query);
                            $num = $rs->num_rows;
                            $sn=0;
                            if($num > 0) {
                              while ($rows = $rs->fetch_assoc()) {
                                $sn = $sn + 1;
                                echo"
                                  <tr>
                                    <td>".$sn."</td>
                                    <td>".$rows['className']."</td>
                                    <td>".$rows['classArmName']."</td>
                                    <td>".$rows['student_id']."</td>
                                    <td>".$rows['sessionName']."</td>
                                    <td>".$rows['termName']."</td>
                                    <td>".($rows['status'] == '1' ? '<span class="badge badge-success">Present</span>' : '<span class="badge badge-danger">Absent</span>')."</td>
                                    <td>".$rows['dateTimeTaken']."</td>
                                  </tr>";
                              }
                            }
                            else {
                              echo "<tr><td colspan='8' class='text-center'>No attendance record found</td></tr>";
                            }
                          }
                          ?>
                        </tbody>
                      </table>
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
  <!-- Page level plugins -->
  <script src="../vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>

  <!-- Page level custom scripts -->
  <script>
    $(document).ready(function () {
      $('#dataTableHover').DataTable();
    });

    function typeDropDown(str) {
      if (str == "") {
        document.getElementById("txtHint").innerHTML = "";
        return;
      }
      else {
        if (window.XMLHttpRequest) {
          xmlhttp = new XMLHttpRequest();
        }
        else {
          xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            document.getElementById("txtHint").innerHTML = this.responseText;
          }
        };
        xmlhttp.open("GET","ajaxCallTypes.php?tid="+str,true);
        xmlhttp.send();
      }
    }
  </script>
</body>
</html>