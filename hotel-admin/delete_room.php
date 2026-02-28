<?php
session_start();
include '../include/connection.php';
include '../includes/functions.php';

if ($_SESSION['level'] != 'hoteladmin') {
    header("Location: ../login/login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = sanitize($con, $_GET['id']);

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        mysqli_begin_transaction($con);

        $deleteQuery = "DELETE FROM hotel_rooms WHERE room_id='$id'";
        mysqli_query($con, $deleteQuery);

        mysqli_commit($con);

        $_SESSION['room_msg'] = [
            "text" => "Room deleted successfully!",
            "type" => "success"
        ];
    } 
    catch (mysqli_sql_exception $e) {
    mysqli_rollback($con);

    // Friendly message for the user
    $_SESSION['room_msg'] = [
        "text" => "Cannot delete Room! It has bookings linked to it.",
        "type" => "error"
    ];

   
}


    header("Location: admin_dashboard.php");
    exit();
}
?>
