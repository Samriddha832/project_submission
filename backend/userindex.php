<?php


if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'includes/navbar.php';
include 'includes/connection.php';
include 'includes/functions.php';

// Search
$search = $_GET['search'] ?? ($_SESSION['old_search'] ?? '');


if (isset($_GET['check_in'])) {
    $check_in = sanitize($con, $_GET['check_in']);
} 

else {

    $check_in = $_SESSION['old_check_in'] ?? date('Y-m-d');
}



if (isset($_GET['check_out'])) {
    $check_out = sanitize($con, $_GET['check_out']);
} else {
    $check_out = $_SESSION['old_check_out'] ?? date('Y-m-d', strtotime('+1 day'));
}



if (isset($_GET['room_type'])) {
    $room_type = sanitize($con, $_GET['room_type']);
} 
else {
    $room_type = $_SESSION['old_room_type'] ?? '';
}



$min_price = $_GET['min_price'] ?? ($_SESSION['old_min_price'] ?? '');
$max_price = $_GET['max_price'] ?? ($_SESSION['old_max_price'] ?? '');


$_SESSION['old_search']    = $search;
$_SESSION['old_check_in']  = $check_in;
$_SESSION['old_check_out'] = $check_out;
$_SESSION['old_room_type'] = $room_type;
$_SESSION['old_min_price'] = $min_price;
$_SESSION['old_max_price'] = $max_price;


$query = "
    SELECT hr.*, h.hotel_name, h.location
    FROM hotel_rooms hr
    JOIN hotels h ON hr.hotel_id = h.hotel_id
    WHERE NOT EXISTS (
        SELECT 1
        FROM bookings b
        WHERE b.room_id = hr.room_id
        AND b.status = 'booked'
        AND b.check_in < '$check_out'
        AND b.check_out > '$check_in'
    )
";



if (!empty($search)) {
    $s = mysqli_real_escape_string($con, $search);
    $query .= " AND (h.hotel_name LIKE '%$s%' OR h.location LIKE '%$s%') ";
}


if ($room_type !== '') {
    // Only apply filter if room_type has a value
    $query .= " AND hr.room_type = '$room_type' ";
}



if (!empty($min_price)) {
    $query .= " AND hr.room_price >= $min_price ";
}
if (!empty($max_price)) {
    $query .= " AND hr.room_price <= $max_price ";
}

$result = mysqli_query($con, $query);
if (!$result) {
    die("SQL ERROR: " . mysqli_error($con));
}






?>