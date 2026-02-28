<?php
    session_start();
    include '../includes/connection.php';
    include '../includes/navbar.php';

    if ($_SESSION['level'] != 'hoteladmin') {
        header("Location: ../login/login.php");
        exit();
    }

    $admin_id = $_SESSION['user_id'];

    $query = "
        SELECT 
            b.booking_id, b.user_id, b.hotel_id, b.rooms_booked, 
            b.check_in, b.check_out, b.status, 
            h.hotel_name, u.name,
            hr.room_number, hr.room_price, hr.room_image
        FROM bookings b
        JOIN hotels h ON b.hotel_id = h.hotel_id
        JOIN users u ON b.user_id = u.user_id
        JOIN hotel_rooms hr ON b.room_id = hr.room_id
        WHERE b.status = 'cancel_requested'
        AND h.hotel_admin_id = $admin_id
        ORDER BY b.booking_id DESC
    ";


$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Cancellation Requests</title>
<link rel="stylesheet" href="../style/adminstyle.css">
</head>
<body>

<div class="All-Contain">
    <h2>Pending Cancellation Requests</h2>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="request-card">
                <b>User:</b> <?= htmlspecialchars($row['name']); ?><br>
                <b>Hotel:</b> <?= htmlspecialchars($row['hotel_name']); ?><br>
                <b>Room Number:</b> <?= htmlspecialchars($row['room_number']); ?><br>
                <b>Room Price:</b> Rs. <?= number_format($row['room_price'], 2); ?><br>
                <b>Rooms Booked:</b> <?= $row['rooms_booked']; ?><br>
                <b>Check-in:</b> <?= $row['check_in']; ?><br>
                <b>Check-out:</b> <?= $row['check_out']; ?><br>
                <b>Status:</b> <?= $row['status']; ?><br>

                <form method="POST" action="approve_cancel.php">
                    <input type="hidden" name="booking_id" value="<?= $row['booking_id']; ?>">
                    <button type="submit">Approve Cancellation</button>
                </form>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="no-requests">No pending cancellation requests.</p>
    <?php endif; ?>
</div>

</body>
</html>
