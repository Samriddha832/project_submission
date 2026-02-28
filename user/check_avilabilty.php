<?php
session_start();
include '../includes/connection.php';
include '../includes/functions.php';
check_login();



// Get POST data
$room_id   = (int)$_POST['room_id'] ?? 0;
$check_in  = $_POST['check_in'] ?? '';
$check_out = $_POST['check_out'] ?? '';
$rootype = $_POST['room_type'] ?? '';

$_SESSION['old_check_in'] = $check_in;
$_SESSION['old_check_out'] = $check_out;
$_SESSION['old_room_type'] = $rootype;


// Fetch room details
$roomQuery = mysqli_query($con, "
    SELECT hr.*, h.hotel_name, h.location, h.hotel_image
    FROM hotel_rooms hr
    JOIN hotels h ON hr.hotel_id = h.hotel_id
    WHERE hr.room_id = $room_id
");

if (!$roomQuery || mysqli_num_rows($roomQuery) == 0) {
    $_SESSION['tost'] = ['message'=>'Room not Available for Selected Date','type'=>'error'];
    header("Location: ../index.php");
    exit;
}

$room = mysqli_fetch_assoc($roomQuery);

// Check if room is already booked for the selected dates
$overlap = mysqli_query($con, "
    SELECT 1 FROM bookings 
    WHERE room_id = $room_id 
      AND status='booked'
      AND check_in < '$check_out'
      AND check_out > '$check_in'
    LIMIT 1
");

if (mysqli_num_rows($overlap) > 0) {
    $_SESSION['tost'] = ['message'=>'Room is already booked for the selected date,Please Select the Room for Another  Date and Try','type'=>'error'];
    header("Location: ../index.php");
    exit;
}

// Calculate nights and total price
$nights = (new DateTime($check_in))->diff(new DateTime($check_out))->days;
$total_price = $room['room_price'] * $nights;

// Store booking data in session for confirm_booking.php
$_SESSION['booking_data'] = [
    'room_id'    => $room_id,
    'hotel_id'   => $room['hotel_id'],
    'check_in'   => $check_in,
    'check_out'  => $check_out,
    'nights'     => $nights,
    'total_price'=> $total_price
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Room Availability</title>
<style>
.wrapper {
    max-width: 900px;
    margin: 90px auto 20px auto; /* push below navbar */
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    background: #fff;
}
.room-header { 
    text-align: center; 
    margin-bottom: 20px; }
.room-header h2 { 
    margin: 0; 
    font-size: 1.8rem; 
}
.room-info { 
    display: flex; 
    flex-wrap: wrap; 
    gap: 20px; 
}
.room-image { 
    flex: 1 1 300px; 
}
.room-image img { 
    width: 100%; 
    height: 220px; 
    object-fit: cover; 
    border-radius: 8px; 
}
.room-details { 
    flex: 1 1 300px; 
}
.room-details p { 
    margin: 8px 0; 
    font-size: 1rem; 
}
.room-details .price { 
    font-size: 1.2rem; font-weight: bold; color: #27ae60; }
.book-now-btn {
    display: inline-block;
    margin-top: 20px;
    padding: 12px 25px;
    background: #27ae60;
    color: #fff;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 1rem;
    text-decoration: none;
}
.book-now-btn:hover { background: #219150; }
</style>
</head>
<body>

<div class="wrapper">
    <div class="room-header">
        <h2><?= htmlspecialchars($room['hotel_name']) ?> - <?= htmlspecialchars($room['room_type']) ?></h2>
        <p><?= htmlspecialchars($room['location']) ?></p>
    </div>

    <div class="room-info">
        <div class="room-image">
            <?php 
            $image = !empty($room['room_image']) 
                        ? "../uploads/rooms/" . $room['room_image'] 
                        : "https://via.placeholder.com/450x300?text=No+Image"; 
            ?>
            <img src="<?= $image ?>" alt="<?= htmlspecialchars($room['room_type']) ?>">
        </div>

        <div class="room-details">
            <p><b>Room Type:</b> <?= htmlspecialchars($room['room_type']) ?></p>
            <p><b>Price per Night:</b> NPR <?= number_format($room['room_price'], 2) ?></p>
            <p><b>Check-in:</b> <?= $check_in ?></p>
            <p><b>Check-out:</b> <?= $check_out ?></p>
            <p><b>Nights:</b> <?= $nights ?></p>
            <p class="price"><b>Total Price:</b> NPR <?= number_format($total_price, 2) ?></p>

            <a href="confirm_booking.php" class="book-now-btn">Book Now</a>
        </div>
    </div>
</div>

</body>
</html>
