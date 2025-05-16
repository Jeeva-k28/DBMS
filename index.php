<?php 
include 'Includes/dbcon.php';
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
    <title> SSA - Login</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="css/ruang-admin.min.css" rel="stylesheet">

</head>

<body class="bg-gradient-login" style="background-image: url('img/logo/loral1.jpe00g');">
    <!-- Login Content -->
    <div class="container-login">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card shadow-sm my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="login-form">
                                    <h5 align="center">STUDENT ATTENDANCE SYSTEM</h5>
                                    <div class="text-center">
                                        <img src="img/logo/attnlg.jpg" style="width:100px;height:100px">
                                        <br><br>
                                        <h1 class="h4 text-gray-900 mb-4">Login Panel</h1>
                                    </div>
                                    <form class="user" method="Post" action="">
                                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                        <div class="form-group">
                                            <select required name="userType" class="form-control mb-3">
                                                <option value="">--Select User Roles--</option>
                                                <option value="Administrator">Administrator</option>
                                                <option value="ClassTeacher">ClassTeacher</option>
                                                <option value="Student">Student</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <input type="text" class="form-control" required name="username" id="exampleInputEmail" placeholder="Enter Email Address / Admission Number">
                                        </div>
                                        <div class="form-group">
                                            <input type="password" name="password" required class="form-control" id="exampleInputPassword" placeholder="Enter Password">
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox small" style="line-height: 1.5rem;">
                                                <input type="checkbox" class="custom-control-input" id="customCheck">
                                                <!-- <label class="custom-control-label" for="customCheck">Remember
                          Me</label> -->
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <input type="submit" class="btn btn-success btn-block" value="Login" name="login" />
                                        </div>
                                    </form>
                                    <div class="text-center">
                                        <a href="forgotPassword.php" class="font-weight-bold small">Forgot Password?</a>
                                    </div>

                                    <?php

  if(isset($_POST['login'])){
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        echo "<div class='alert alert-danger' role='alert'>Invalid request! Please try again.</div>";
        exit;
    }

    $userType = sanitize_input($conn, $_POST['userType']);
    $username = sanitize_input($conn, $_POST['username']);
    $password = trim($_POST['password']);

    if($userType == "Administrator"){
      $user = verify_login($conn, "tbladmin", "emailAddress", $username, $password);
      if($user) {
        // Regenerate session ID to prevent session fixation
        regenerate_session();
        
        $_SESSION['userId'] = $user['Id'];
        $_SESSION['firstName'] = $user['firstName'];
        $_SESSION['lastName'] = $user['lastName'];
        $_SESSION['emailAddress'] = $user['emailAddress'];
        $_SESSION['user_type'] = 'Administrator';
        $_SESSION['last_login'] = time();

        echo "<script type = \"text/javascript\">
        window.location = (\"Admin/index.php\")
        </script>";
      }

      else{

        echo "<div class='alert alert-danger' role='alert'>
        Invalid Username/Password!
        </div>";

      }
    }
    else if($userType == "ClassTeacher"){
      $user = verify_login($conn, "tblclassteacher", "emailAddress", $username, $password);
      if($user) {
        // Regenerate session ID to prevent session fixation
        regenerate_session();
        
        $_SESSION['userId'] = $user['Id'];
        $_SESSION['firstName'] = $user['firstName'];
        $_SESSION['lastName'] = $user['lastName'];
        $_SESSION['emailAddress'] = $user['emailAddress'];
        $_SESSION['classId'] = $user['classId'];
        $_SESSION['classArmId'] = $user['classArmId'];
        $_SESSION['user_type'] = 'ClassTeacher';
        $_SESSION['last_login'] = time();

        echo "<script type = \"text/javascript\">
        window.location = (\"ClassTeacher/index.php\")
        </script>";
      }

      else{

        echo "<div class='alert alert-danger' role='alert'>
        Invalid Username/Password!
        </div>";

      }
    }
    else if($userType == "Student"){
        $query = "SELECT tblstudents.*, tblclass.className, tblclassarms.classArmName 
                FROM tblstudents
                INNER JOIN tblclass ON tblclass.Id = tblstudents.classId
                INNER JOIN tblclassarms ON tblclassarms.Id = tblstudents.classArmId 
                WHERE admissionNumber = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if ($user && verify_password($password, $user['password'])) {
            // Regenerate session ID to prevent session fixation
            regenerate_session();
            
            $_SESSION['userId'] = $user['Id'];
            $_SESSION['firstName'] = $user['firstName'];
            $_SESSION['lastName'] = $user['lastName'];
            $_SESSION['emailAddress'] = $user['emailAddress'];
            $_SESSION['admissionNumber'] = $user['admissionNumber'];
            $_SESSION['classId'] = $user['classId'];
            $_SESSION['classArmId'] = $user['classArmId'];
            $_SESSION['className'] = $user['className'];
            $_SESSION['classArmName'] = $user['classArmName'];
            $_SESSION['user_type'] = 'Student';
            $_SESSION['last_login'] = time();

            echo "<script type = \"text/javascript\">
            window.location = (\"Student/index.php\")
            </script>";
        }
        else{
            echo "<div class='alert alert-danger' role='alert'>
            Invalid Username/Password!
            </div>";
        }
    }
    else{

        echo "<div class='alert alert-danger' role='alert'>
        Invalid Username/Password!
        </div>";

    }
}
?>

                                    <!-- <hr>
                    <a href="index.html" class="btn btn-google btn-block">
                      <i class="fab fa-google fa-fw"></i> Login with Google
                    </a>
                    <a href="index.html" class="btn btn-facebook btn-block">
                      <i class="fab fa-facebook-f fa-fw"></i> Login with Facebook
                    </a> -->


                                    <div class="text-center">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Login Content -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/ruang-admin.min.js"></script>
    <script>
        // Fix any potential script loading issues
        if (typeof jQuery === 'undefined') {
            document.write('<script src="https://code.jquery.com/jquery-3.6.0.min.js"><\/script>');
        }
    </script>
</body>

</html>