<?php
session_start();
include '../includes/connection.php';
include '../includes/functions.php';
include '../includes/navbar.php';

?>
    <div id="tostBox"></div>
    <link rel="stylesheet" href="../Tost_Message/style.css">
    <script src="../Tost_Message/script.js"></script>

<?php
// Ensure user is logged in

$user_id = $_SESSION['user_id'] ?? 0;
if (!$user_id) {
    $_SESSION['toast'] = ['message'=>'You Must Login to view Invoice','type'=>'error'];
    header('location:../login/login.php');
    exit();
}



if(!empty($_SESSION['booking_success'])){
    $tost = $_SESSION['booking_success'];

    ?>
    <script>
        showTost("<?= $tost['message'] ?>","<?= $tost['type']?>");
    </script>


<?php
unset($_SESSION['booking_success']);
}


if(!empty($_SESSION['toast'])){
    $tost = $_SESSION['toast'];

    ?>
    <script>
        showTost("<?= $tost['message'] ?>","<?= $tost['type']?>");
    </script>


<?php
unset($_SESSION['toast']);
}

// Get booking ID from GET
$booking_id = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;
if ($booking_id < 1) {
    die("Invalid Booking ID.");
}

// Query invoice details
$query = "
    SELECT 
        b.*, 
        u.name, u.email,
        h.hotel_name, h.location, h.hotel_image,
        hr.room_number, hr.room_type, hr.room_price, 
        hr.about_rooms, hr.room_image,
        p.payment_status, p.amount
        FROM bookings b
        JOIN users u ON b.user_id = u.user_id
        JOIN hotels h ON b.hotel_id = h.hotel_id
        JOIN hotel_rooms hr ON b.room_id = hr.room_id
        LEFT JOIN payments p ON b.booking_id = p.booking_id
        WHERE b.booking_id = $booking_id
        AND b.user_id = $user_id
        LIMIT 1
";

$result = mysqli_query($con, $query);
if (!$result) {
    die("Query failed: " . mysqli_error($con));
}

$row = mysqli_fetch_assoc($result);
if (!$row) {
    die("<p style='text-align:center;'>Invoice not found or you don't have permission to view it.</p>");
}

// Calculate nights
$check_in = new DateTime($row['check_in']);
$check_out = new DateTime($row['check_out']);
$nights = max(1, $check_in->diff($check_out)->days);

// Total price
$total_price = $row['rooms_booked'] * $row['room_price'] * $nights;

// Room image with fallback
$imagePath = "../uploads/rooms/" . $row['room_image'];
if (!file_exists($imagePath) || empty($row['room_image'])) {
    $imagePath = "https://via.placeholder.com/450x350?text=No+Image";
}




?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<title>Invoice</title>
<link rel="stylesheet" href="../style/userinvoice.css">
</head>
<body>
    

<div class="invoice-container">

    <!-- Left Side -->
    <div class="invoice-left">
        <h2>Hotel Booking Invoice</h2>
        <hr>

        <p><b>Invoice No:</b> <?= $row['booking_id']; ?></p>
        <p><b>Date:</b> <?= date("Y-m-d"); ?></p>

        <h3>Customer Details</h3>
        <p>
            <b>Name:</b> <?= htmlspecialchars($row['name']); ?><br>
            <b>Email:</b> <?= htmlspecialchars($row['email']); ?>
        </p>

        <h3>Hotel & Room Details</h3>
        <p>
            <b>Hotel:</b> <?= htmlspecialchars($row['hotel_name']); ?><br>
            <b>Location:</b> <?= htmlspecialchars($row['location']); ?><br>
            <b>Room No:</b> <?= htmlspecialchars($row['room_number']); ?><br>
            <b>Room Type:</b> <?= htmlspecialchars($row['room_type']); ?><br>
            <!-- <b>Room Info:</b> <?= htmlspecialchars($row['about_rooms']); ?><br> -->
            <b>Price per Room:</b> Rs. <?= number_format($row['room_price'], 2); ?>
        </p>

        <h3>Booking Details</h3>
        <p>
            <b>Check-in:</b> <?= $row['check_in']; ?><br>
            <b>Check-out:</b> <?= $row['check_out']; ?><br>
            <b>Nights:</b> <?= $nights; ?><br>
            <b>Rooms Booked:</b> <?= $row['rooms_booked']; ?><br>
            <b>Total Price:</b> <b style="color:green;">Rs. <?= number_format($total_price, 2); ?></b><br>

            <b>Payment Status:</b>
            <span style="color:<?= ($row['payment_status'] == 'paid' ? 'green' : 'red'); ?>;">
                <?= ucfirst($row['payment_status'] ?? 'unpaid'); ?>
            </span>
        </p>

       
      

    <!-- Right Side -->
    <div class="invoice-right">
        <img src="<?= $imagePath; ?>" alt="Room Image">
    </div>

</div>

<div class="footer-section">
    <hr>
    <p>Thank you for booking with us!</p>
    <button class="print-btn" onclick="window.print()">🖨️ Print Invoice</button>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>

<script>
fetch('send_booking_mail.php?booking_id=<?= $booking_id ?>')
    .then(response => response.text())  
    .then(data => console.log("Mail sent:", data)) 
    .catch(error => console.error("Error:", error)); 
</script>

