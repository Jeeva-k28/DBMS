<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../Includes/dbcon.php';

// Validate that user is an Administrator
validate_session('Administrator');

//------------------------SAVE--------------------------------------------------

$statusMsg = ""; // Always define before use

if(isset($_POST['save'])){
    // Uncomment this line to see what is being posted:
    // echo "<pre>"; print_r($_POST); echo "</pre>";

    $firstName = isset($_POST['firstName']) ? $_POST['firstName'] : '';
    $lastName = isset($_POST['lastName']) ? $_POST['lastName'] : '';
    $otherName = isset($_POST['otherName']) ? $_POST['otherName'] : '';
    $admissionNumber = isset($_POST['admissionNumber']) ? $_POST['admissionNumber'] : '';
    $student_id = isset($_POST['student_id']) ? $_POST['student_id'] : '';
    $classId = isset($_POST['classId']) ? $_POST['classId'] : '';
    $classArmId = isset($_POST['classArmId']) ? $_POST['classArmId'] : '';
    $dateCreated = date("Y-m-d");

    // Check for empty required fields
    if($firstName == '' || $lastName == '' || $admissionNumber == '' || $student_id == '' || $classId == '' || $classArmId == '') {
        $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>Please fill all required fields.</div>";
    } else {
        $query = mysqli_query($conn,"SELECT * FROM tblstudents WHERE admissionNumber ='$admissionNumber'");
        $ret = mysqli_fetch_array($query);

        if($ret > 0){ 
            $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>This Admission Number Already Exists!</div>";
        }
        else{
            // Use prepared statement for security and reliability
            $sql = "INSERT INTO tblstudents (firstName, lastName, otherName, admissionNumber, student_id, password, classId, classArmId, dateCreated)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $password = '12345'; // Default password
            $stmt->bind_param("ssssssiss", $firstName, $lastName, $otherName, $admissionNumber, $student_id, $password, $classId, $classArmId, $dateCreated);

            if ($stmt->execute()) {
                $statusMsg = "<div class='alert alert-success'  style='margin-right:700px;'>Created Successfully!</div>";
            } else {
                $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred!<br>".$stmt->error."<br>SQL: $sql</div>";
            }
            $stmt->close();
        }
    }
}

//---------------------------------------EDIT-------------------------------------------------------------

if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "edit")
{
    $Id= $_GET['Id'];

    $query=mysqli_query($conn,"select * from tblstudents where Id ='$Id'");
    $row=mysqli_fetch_array($query);

    //------------UPDATE-----------------------------

    if(isset($_POST['update'])){
        $firstName=$_POST['firstName'];
        $lastName=$_POST['lastName'];
        $otherName=$_POST['otherName'];
        $admissionNumber=$_POST['admissionNumber'];
        $student_id=$_POST['student_id'];
        $classId=$_POST['classId'];
        $classArmId=$_POST['classArmId'];
        $dateCreated = date("Y-m-d");

        $query=mysqli_query($conn,"update tblstudents set firstName='$firstName', lastName='$lastName',
        otherName='$otherName', admissionNumber='$admissionNumber', student_id='$student_id', classId='$classId',classArmId='$classArmId'
        where Id='$Id'");
        if ($query) {
            echo "<script type = \"text/javascript\">
            window.location = (\"createStudents.php\")
            </script>"; 
        }
        else
        {
            $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred!<br>".mysqli_error($conn)."</div>";
        }
    }
}

//--------------------------------DELETE------------------------------------------------------------------

if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "delete")
{
    $Id= $_GET['Id'];
    $classArmId= isset($_GET['classArmId']) ? $_GET['classArmId'] : '';

    $query = mysqli_query($conn,"DELETE FROM tblstudents WHERE Id='$Id'");

    if ($query == TRUE) {
        echo "<script type = \"text/javascript\">
        window.location = (\"createStudents.php\")
        </script>";
    }
    else{
        $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred!<br>".mysqli_error($conn)."</div>"; 
    }
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
<?php include 'includes/title.php';?>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="css/ruang-admin.min.css" rel="stylesheet">

   <script>
    function classArmDropdown(str) {
    if (str == "") {
        document.getElementById("txtHint").innerHTML = "";
        return;
    } else { 
        if (window.XMLHttpRequest) {
            xmlhttp = new XMLHttpRequest();
        } else {
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("txtHint").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET","ajaxClassArms2.php?cid="+str,true);
        xmlhttp.send();
    }
}
</script>
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
            <h1 class="h3 mb-0 text-gray-800">Create Students</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Create Students</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <!-- Form Basic -->
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Create Students</h6>
                    <?php echo $statusMsg; ?>
                </div>
                <div class="card-body">
                  <form method="post">
                   <div class="form-group row mb-3">
                        <div class="col-xl-6">
                        <label class="form-control-label">Firstname<span class="text-danger ml-2">*</span></label>
                        <input type="text" class="form-control" name="firstName" value="<?php echo isset($row['firstName']) ? $row['firstName'] : ''; ?>" id="exampleInputFirstName" >
                        </div>
                        <div class="col-xl-6">
                        <label class="form-control-label">Lastname<span class="text-danger ml-2">*</span></label>
                      <input type="text" class="form-control" name="lastName" value="<?php echo isset($row['lastName']) ? $row['lastName'] : ''; ?>" id="exampleInputFirstName" >
                        </div>
                    </div>
                    <div class="form-group row mb-3">
                        <div class="col-xl-6">
                        <label class="form-control-label">Other Name<span class="text-danger ml-2">*</span></label>
                        <input type="text" class="form-control" name="otherName" value="<?php echo isset($row['otherName']) ? $row['otherName'] : ''; ?>" id="exampleInputFirstName" >
                        </div>
                        <div class="col-xl-6">
                        <label class="form-control-label">Admission Number<span class="text-danger ml-2">*</span></label>
                      <input type="text" class="form-control" required name="admissionNumber" value="<?php echo isset($row['admissionNumber']) ? $row['admissionNumber'] : ''; ?>" id="exampleInputFirstName" >
                        </div>
                    </div>
                    <div class="form-group row mb-3">
                        <div class="col-xl-6">
                        <label class="form-control-label">Student ID<span class="text-danger ml-2">*</span></label>
                        <input type="text" class="form-control" name="student_id" value="<?php echo isset($row['student_id']) ? $row['student_id'] : ''; ?>" id="exampleInputStudentId" >
                        </div>
                        <div class="col-xl-6">
                        <label class="form-control-label">Select Class<span class="text-danger ml-2">*</span></label>
                         <?php
                        $qry= "SELECT * FROM tblclass ORDER BY className ASC";
                        $result = $conn->query($qry);
                        $num = $result->num_rows;		
                        if ($num > 0){
                          echo ' <select required name="classId" onchange="classArmDropdown(this.value)" class="form-control mb-3">';
                          echo'<option value="">--Select Class--</option>';
                          while ($rows = $result->fetch_assoc()){
                          echo'<option value="'.$rows['Id'].'" >'.$rows['className'].'</option>';
                              }
                                  echo '</select>';
                              }
                            ?>  
                        </div>
                        <div class="col-xl-6">
                        <label class="form-control-label">Class Section<span class="text-danger ml-2">*</span></label>
                            <?php
                                echo"<div id='txtHint'></div>";
                            ?>
                        </div>
                    </div>
                      <?php
                    if (isset($Id))
                    {
                    ?>
                    <button type="submit" name="update" class="btn btn-warning">Update</button>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php
                    } else {           
                    ?>
                    <button type="submit" name="save" class="btn btn-primary">Save</button>
                    <?php
                    }         
                    ?>
                  </form>
                </div>
              </div>

              <!-- Input Group -->
                 <div class="row">
              <div class="col-lg-12">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">All Student</h6>
                </div>
                <div class="table-responsive p-3">
                  <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                    <thead class="thead-light">
                      <tr>
                        <th>#</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Other Name</th>
                        <th>Admission No</th>
                        <th>Student ID</th>
                        <th>Class</th>
                        <th>Class Arm</th>
                        <th>Date Created</th>
                         <th>Edit</th>
                        <th>Delete</th>
                      </tr>
                    </thead>
                
                    <tbody>

                  <?php
                      $query = "SELECT tblstudents.Id,tblclass.className,tblclassarms.classArmName,tblclassarms.Id AS classArmId,tblstudents.firstName,
                      tblstudents.lastName,tblstudents.otherName,tblstudents.admissionNumber,tblstudents.student_id,tblstudents.dateCreated
                      FROM tblstudents
                      INNER JOIN tblclass ON tblclass.Id = tblstudents.classId
                      INNER JOIN tblclassarms ON tblclassarms.Id = tblstudents.classArmId";
                      $rs = $conn->query($query);
                      $num = $rs->num_rows;
                      $sn=0;
                      $status="";
                      if($num > 0)
                      { 
                        while ($rows = $rs->fetch_assoc())
                          {
                             $sn = $sn + 1;
                            echo"
                              <tr>
                                <td>".$sn."</td>
                                <td>".$rows['firstName']."</td>
                                <td>".$rows['lastName']."</td>
                                <td>".$rows['otherName']."</td>
                                <td>".$rows['admissionNumber']."</td>
                                <td>".$rows['student_id']."</td>
                                <td>".$rows['className']."</td>
                                <td>".$rows['classArmName']."</td>
                                 <td>".$rows['dateCreated']."</td>
                                <td><a href='?action=edit&Id=".$rows['Id']."'><i class='fas fa-fw fa-edit'></i></a></td>
                                <td><a href='?action=delete&Id=".$rows['Id']."'><i class='fas fa-fw fa-trash'></i></a></td>
                              </tr>";
                          }
                      }
                      else
                      {
                           echo   
                           "<div class='alert alert-danger' role='alert'>
                            No Record Found!
                            </div>";
                      }
                      
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            </div>
          </div>
          <!--Row-->

        </div>
        <!---Container Fluid-->
      </div>
      <!-- Footer -->
       <?php include "Includes/footer.php";?>
      <!-- Footer -->
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
      $('#dataTable').DataTable(); // ID From dataTable 
      $('#dataTableHover').DataTable(); // ID From dataTable with Hover
    });
  </script>
</body>

</html>