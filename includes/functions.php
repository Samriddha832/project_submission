<?php
include 'connection.php';

function sanitize($con, $data) {
    return mysqli_real_escape_string($con, trim($data));
}

function check_login() {
    
    if (!isset($_SESSION['user_id'])) {
        session_start();
        $_SESSION['toast'] = [
            'message' => 'Please login first to book hotel.',
            'type' => 'error'
        ];
        header("Location: ../login/login.php");
        exit();

        
    }
}
?>
