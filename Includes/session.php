<?php
require_once 'dbcon.php';

function validate_session($required_type = null) {
    session_start();
    
    // Session expiry after 30 mins of inactivity
    $expiry = 1800;
    
    if (!isset($_SESSION['userId']) || !isset($_SESSION['user_type'])) {
        session_unset();
        session_destroy();
        header("Location: ../index.php");
        exit();
    }

    // Check if the user has the required type
    if ($required_type !== null && $_SESSION['user_type'] !== $required_type) {
        session_unset();
        session_destroy();
        header("Location: ../index.php?error=unauthorized");
        exit();
    }

    // Check session timeout
    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $expiry)) {
        session_unset();
        session_destroy();
        header("Location: ../index.php?error=timeout");
        exit();
    }
    
    $_SESSION['LAST_ACTIVITY'] = time();
}

validate_session();
?>