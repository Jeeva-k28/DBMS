<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db_connection.php'; // Include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = isset($_POST["name"]) ? mysqli_real_escape_string($conn, $_POST["name"]) : "";
    $email = isset($_POST["email"]) ? mysqli_real_escape_string($conn, $_POST["email"]) : "";
    $password = isset($_POST["password"]) ? $_POST["password"] : "";
    $confirm_password = isset($_POST["confirm_password"]) ? $_POST["confirm_password"] : "";
    $user_type = isset($_POST["user_type"]) ? mysqli_real_escape_string($conn, $_POST["user_type"]) : "";

    if ($password != $confirm_password) {
        echo "<script>alert('Passwords do not match.'); window.location.href='signup.html';</script>";
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    if ($user_type == "admin") {
        $table_name = "admins";
        $redirect_page = "adminhome.html";
    } else if ($user_type == "teacher") {
        $table_name = "teachers";
        $redirect_page = "teacherhome.html";
    } else {
        echo "<script>alert('Invalid user type.'); window.location.href='signup.html';</script>";
        exit;
    }

    // Check for duplicate email
    $sql_check_email = "SELECT email FROM $table_name WHERE email = ?";
    $stmt_check_email = $conn->prepare($sql_check_email);
    if ($stmt_check_email === false) {
        echo "<script>alert('Error preparing email check statement: " . $conn->error . "'); window.location.href='signup.html';</script>";
        exit;
    }
    $stmt_check_email->bind_param("s", $email);
    $stmt_check_email->execute();
    $stmt_check_email->store_result();
    if ($stmt_check_email->num_rows > 0) {
        echo "<script>alert('Email already exists. Please use a different email.'); window.location.href='signup.html';</script>";
        $stmt_check_email->close();
        exit;
    }
    $stmt_check_email->close();

    // Check for duplicate name
    $sql_check_name = "SELECT name FROM $table_name WHERE name = ?";
    $stmt_check_name = $conn->prepare($sql_check_name);
     if ($stmt_check_name === false) {
        echo "<script>alert('Error preparing name check statement: " . $conn->error . "'); window.location.href='signup.html';</script>";
        exit;
    }
    $stmt_check_name->bind_param("s", $name);
    $stmt_check_name->execute();
    $stmt_check_name->store_result();
    if ($stmt_check_name->num_rows > 0) {
        echo "<script>alert('Name already exists. Please use a different name.'); window.location.href='signup.html';</script>";
        $stmt_check_name->close();
        exit;
    }
    $stmt_check_name->close();

    $sql = "INSERT INTO $table_name (name, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        // Handle error.
        echo "<script>alert('Error preparing insert statement: " . $conn->error . "'); window.location.href='signup.html';</script>";
        exit;
    }
    $stmt->bind_param("sss", $name, $email, $hashed_password);

    if ($stmt->execute()) {
        //echo "New record created successfully";
        session_destroy();
        echo "<script> window.location.href='$redirect_page';</script>";
        exit();
    } else {
        echo "<script>alert('Error executing statement: " . $stmt->error . "'); window.location.href='signup.html';</script>";
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>
```