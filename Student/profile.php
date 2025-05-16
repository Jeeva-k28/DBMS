<?php 
error_reporting(0);
include '../Includes/dbcon.php';

// Validate that user is a Student
validate_session('Student');

$query = "SELECT tblstudents.*, tblclass.className, tblclassarms.classArmName 
          FROM tblstudents
          INNER JOIN tblclass ON tblclass.Id = tblstudents.classId
          INNER JOIN tblclassarms ON tblclassarms.Id = tblstudents.classArmId
          WHERE tblstudents.Id = '$_SESSION[userId]'";
$rs = $conn->query($query);
$num = $rs->num_rows;
$rows = $rs->fetch_assoc();

$statusMsg = "";

// Update Profile
if(isset($_POST['update'])){
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $otherName = $_POST['otherName'];
    $emailAddress = $_POST['emailAddress'];
    $phoneNo = $_POST['phoneNo'];
    
    $query = mysqli_query($conn,"update tblstudents set firstName='$firstName', lastName='$lastName', 
            otherName='$otherName', emailAddress='$emailAddress', phoneNo='$phoneNo' where Id='$_SESSION[userId]'");
    if($query) {
        $statusMsg = "<div class='alert alert-success' style='margin-right:700px;'>Profile Updated Successfully!</div>";
    }
    else {
        $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred!</div>";
    }
}

// Change Password
if(isset($_POST['changePassword'])){
    $oldPassword = $_POST['oldPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];
    
    $query = mysqli_query($conn,"select * from tblstudents where password='$oldPassword' and Id='$_SESSION[userId]'");
    $num = mysqli_fetch_array($query);
    
    if($num > 0) {
        if($newPassword === $confirmPassword) {
            $query = mysqli_query($conn,"update tblstudents set password='$newPassword' where Id='$_SESSION[userId]'");
            if($query) {
                $statusMsg = "<div class='alert alert-success' style='margin-right:700px;'>Password Changed Successfully!</div>";
            }
            else {
                $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred!</div>";
            }
        }
        else {
            $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>New Password and Confirm Password do not match!</div>";
        }
    }
    else {
        $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>Old Password is incorrect!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link href="../img/logo/attnlg.jpg" rel="icon">
  <title>Student Profile</title>
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
            <h1 class="h3 mb-0 text-gray-800">Profile Settings</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Profile</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <!-- Profile Details -->
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-primary">
                  <h6 class="m-0 font-weight-bold text-white">Personal Information</h6>
                </div>
                <div class="card-body">
                  <?php echo $statusMsg; ?>
                  <form method="post">
                    <div class="form-group row mb-3">
                      <div class="col-xl-6">
                        <label class="form-control-label">First Name<span class="text-danger ml-2">*</span></label>
                        <input type="text" class="form-control" name="firstName" value="<?php echo $rows['firstName'];?>" readonly>
                      </div>
                      <div class="col-xl-6">
                        <label class="form-control-label">Last Name<span class="text-danger ml-2">*</span></label>
                        <input type="text" class="form-control" name="lastName" value="<?php echo $rows['lastName'];?>" readonly>
                      </div>
                    </div>
                    <div class="form-group row mb-3">
                      <div class="col-xl-6">
                        <label class="form-control-label">Admission Number<span class="text-danger ml-2">*</span></label>
                        <input type="text" class="form-control" name="admissionNumber" value="<?php echo $rows['admissionNumber'];?>" readonly>
                      </div>
                      <div class="col-xl-6">
                        <label class="form-control-label">Class<span class="text-danger ml-2">*</span></label>
                        <input type="text" class="form-control" value="<?php echo $rows['className'].' - '.$rows['classArmName'];?>" readonly>
                      </div>
                    </div>
                  </form>
                </div>
              </div>

              <!-- Change Password -->
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-primary">
                  <h6 class="m-0 font-weight-bold text-white">Change Password</h6>
                </div>
                <div class="card-body">
                  <form method="post">
                    <div class="form-group row mb-3">
                      <div class="col-xl-6">
                        <label class="form-control-label">Current Password<span class="text-danger ml-2">*</span></label>
                        <input type="password" class="form-control" name="oldPassword" required>
                      </div>
                    </div>
                    <div class="form-group row mb-3">
                      <div class="col-xl-6">
                        <label class="form-control-label">New Password<span class="text-danger ml-2">*</span></label>
                        <input type="password" class="form-control" name="password" required>
                      </div>
                      <div class="col-xl-6">
                        <label class="form-control-label">Confirm New Password<span class="text-danger ml-2">*</span></label>
                        <input type="password" class="form-control" name="confirmPassword" required>
                      </div>
                    </div>
                    <div class="form-group row">
                      <div class="col-xl-12">
                        <button type="submit" name="changePassword" class="btn btn-primary">Update Password</button>
                      </div>
                    </div>
                  </form>
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
</body>
</html>