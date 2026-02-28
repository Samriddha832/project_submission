<?php
session_start();
include '../include/connection.php';
include '../includes/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = sanitize($con, $_GET['id']);

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        mysqli_begin_transaction($con);

        $deleteQuery = "DELETE FROM users WHERE user_id='$id'";
        mysqli_query($con, $deleteQuery);

        mysqli_commit($con);

        $_SESSION['hotel_msg'] = [
            "text" => "user deleted successfully!",
            "type" => "success"
        ];
    } 
    catch (mysqli_sql_exception $e) {
    mysqli_rollback($con);

    // Friendly message for the user
    $_SESSION['hotel_msg'] = [
        "text" => "Cannot delete user! It has bookings linked to it.",
        "type" => "error"
    ];

    
}


    header("Location: index.php");
    exit();
}
?>
