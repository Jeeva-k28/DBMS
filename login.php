<?php
session_start();
require_once('db_connection.php');

function login($username, $password, $conn) {
    $sql = "SELECT * FROM admins WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row["password"])) {
            return "admin";
        }
    }

    $sql = "SELECT * FROM teachers WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
         if (password_verify($password, $row["password"])) {
            return "teacher";
        }
    }
    return "invalid";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $userType = login($username, $password, $conn);

    if ($userType == "admin") {
        $_SESSION["loggedIn"] = true;
        header("Location: adminhome.html");
        exit;
    } else if ($userType == "teacher") {
        $_SESSION["loggedIn"] = true;
        header("Location: teacherhome.html");
        exit;
    } else {
        echo "<script>alert('Invalid username or password'); window.location.href='login.html';</script>";
    }
}

$conn->close();
?>
