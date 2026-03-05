<?php
session_start();
include '../includes/navbar.php';
include '../includes/connection.php';

if ($_SESSION['level'] != 'hoteladmin') {
    header("Location: ../login/login.php");
    exit();
}

$hotel_admin_id = $_SESSION['user_id']; // current hotel admin ID

// Fetch bookings with room info
$sql = "SELECT 
            b.*,
            u.user_id, u.name, u.email,u.phone,
            h.hotel_id, h.hotel_name,
            hr.room_id, hr.room_number,hr.room_type, hr.room_price, hr.room_image
        
        FROM bookings b
        JOIN users u ON b.user_id = u.user_id
        JOIN hotels h ON b.hotel_id = h.hotel_id
        JOIN hotel_rooms hr ON b.room_id = hr.room_id
        WHERE h.hotel_admin_id = $hotel_admin_id and b.status = 'booked'
        ORDER BY b.booking_id DESC";

$result = mysqli_query($con, $sql);

if (!$result) {
    die("SQL ERROR: " . mysqli_error($con));
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../style/adminstyle.css">
    <title>Hotel Admin - My Bookings</title>
</head>
<body>
<div class="All-Contain">
    <h2>Hotel Admin - Bookings  Hotels</h2>

    <div class="table-container">
        <table>
            <tr>
                
                <th>User Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Room No</th>
                <th>Room Type</th>
                <th>Room Image</th>
                <th>Price / Room</th>
                <th>Check-In</th>
                <th>Check-Out</th>
                <th>Rooms Status</th>
                <th>Total Amount</th>
                
            </tr>

            <?php 
            if (mysqli_num_rows($result) == 0) {
                echo "<tr><td colspan='13'>No bookings found for your hotels.</td></tr>";
            } else {
                while ($row = mysqli_fetch_assoc($result)) { 
                    $check_in = new DateTime($row['check_in']);
                    $check_out = new DateTime($row['check_out']);
                    $nights = max(1, $check_in->diff($check_out)->days);

                    // Total price
                    $total_price = $row['rooms_booked'] * $row['room_price'] * $nights;
                    $imagePath = "../uploads/rooms/" . $row['room_image'];
                    if (empty($row['room_image']) || !file_exists($imagePath)) {
                        $imagePath = "https://via.placeholder.com/200x150?text=No+Image";
                    }
            ?>
                    <tr>
                        
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['phone']) ?></td>
                        <td><?= htmlspecialchars($row['room_number']) ?></td>
                        <td><?= htmlspecialchars($row['room_type']) ?></td>
                        <td>
                            <a href="<?= $imagePath ?>" target="_blank">
                                <img src="<?= $imagePath ?>" alt="Room Image" width="100">
                            </a>
                        </td>
                        <td>NPR <?= number_format($row['room_price'], 2) ?></td>
                        <td><?= $row['check_in'] ?></td>
                        <td><?= $row['check_out'] ?></td>
                        <td><?= $row['status'] ?></td>
                        <td>NPR <?= $total_price ?></td>
                        
                    </tr>
            <?php 
                } 
            } 
            ?>
        </table>
    </div>
</div>
</body>
</html>
