<?php
session_start();
include '../includes/connection.php';
include '../includes/functions.php';
include '../includes/navbar.php';

if (!isset($_GET['room_id'])) {
    die("Invalid room.");
}

$check_in = $_SESSION['old_check_in'] ?? '';
$check_out = $_SESSION['old_check_out'] ?? '';
$room_id = (int)$_GET['room_id'];

$sql = "
    SELECT 
        hr.*, 
        h.hotel_name, 
        h.location, 
        h.about AS hotel_description,
        h.hotel_address_link,
        hr.about_rooms
    FROM hotel_rooms hr
    JOIN hotels h ON hr.hotel_id = h.hotel_id
    WHERE hr.room_id = $room_id
";

$result = mysqli_query($con, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    die("Room not found.");
}

$room = mysqli_fetch_assoc($result);

$image = !empty($room['room_image']) 
    ? "../uploads/rooms/" . $room['room_image'] 
    : "https://via.placeholder.com/500x350?text=No+Image";
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($room['hotel_name']) ?> - Room Detail</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../style/room_detail.css?v=<?=time()?>">
</head>
<body>

<div class="container">

    <!-- LEFT SIDE (Image + About Sections) -->
    <div class="left-content">
        <div class="room-img">
            <img src="<?= $image ?>" alt="Room Image">
        </div>

        <div>
            <div class="section-title">About Hotel</div>
            <div class="long-text">
                <?= nl2br(htmlspecialchars($room['hotel_description'])) ?>
            </div>
        </div>

        <div>
            <div class="section-title">About Room</div>
            <div class="long-text">
                <?= nl2br(htmlspecialchars($room['about_rooms'])) ?>
            </div>
        </div>
    </div>

    <!-- RIGHT SIDE (Title, Price, Map, Booking Form) -->
    <div class="details">
        <h1><?= htmlspecialchars($room['hotel_name']) ?></h1>
        <p><b>Location:</b> <?= htmlspecialchars($room['location']) ?></p>
        <p class="price">NPR <?= number_format($room['room_price'], 2) ?></p>

        <div class="section-title">Hotel Location Map</div>
        <a href="<?= htmlspecialchars($room['hotel_address_link']) ?>" target="_blank" class="map-link">
            View on Google Maps → <i class="fa-solid fa-location-dot"></i>
        </a>

        <!-- BOOKING FORM -->
        <form action="check_avilabilty.php" method="POST">
            <input type="hidden" name="room_id" value="<?= $room['room_id'] ?>">

            <label><b>Check-in:</b></label>
            <input type="date" name="check_in" value="<?= $check_in ?>" class="check_in" required>

            <label><b>Check-out:</b></label>
            <input type="date" name="check_out" value="<?= $check_out ?>" class="check_out" required>

            <button type="submit" class="btn">
                <i class="fa fa-calendar-check"></i> Book Now
            </button>
        </form>

        <!-- CHAT / ENQUIRY BUTTON (Normal link) -->
        <a href="enquiry_chat.php?room_id=<?= $room['room_id'] ?>&hotel_id=<?= $room['hotel_id'] ?>" class="btn">
            <i class="fa fa-comments"></i> Enquiry / Chat
        </a>

    </div>

</div>

<script src="../script/date_validate.js"></script>
</body>
</html>
