<?php
session_start();
include '../includes/navbar.php';
include '../includes/connection.php';

if (!isset($_SESSION['user_id'])) {
    die("You must <a href='../login/login.php'>login</a> to view bookings.");
}

$user_id = $_SESSION['user_id'];

?>
<div id="tostBox"></div>
    <link rel="stylesheet" href="../Tost_Message/style.css">
    <script src="../Tost_Message/script.js"></script>

<?php

        if(!empty($_SESSION['toast'])){
        $tost = $_SESSION['toast'];

?>

<script>showTost("<?= $tost['message'] ?>","<?= $tost['status'] ?>")</script>

<?php
unset($_SESSION['toast']);

}




$query = "
    SELECT b.booking_id, b.hotel_id, b.rooms_booked, b.check_in, b.check_out, b.status, h.hotel_name
    FROM bookings b
    JOIN hotels h ON b.hotel_id = h.hotel_id
    WHERE b.user_id='$user_id'
    ORDER BY b.booking_id DESC
";

$result = mysqli_query($con, $query);

if (!$result) {
    die('Query Failed: ' . mysqli_error($con));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <title>My Bookings</title>

        <link rel="stylesheet" href="../style/mybooking.css">


    </style>
</head>
<body>

    <div class="wrapper">
        <div class="mybookingh2">
            <h2>My Bookings</h2>
        </div>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="booking-card-container">
                <?php while ($row = mysqli_fetch_assoc($result)): 
                    $check_in = date("d M Y", strtotime($row['check_in']));
                    $check_out = date("d M Y", strtotime($row['check_out']));
                ?>
                    <div class="booking-card">
                        <b><?= htmlspecialchars($row['hotel_name']); ?></b>

                        <div class="booking-info">Rooms: <?= $row['rooms_booked']; ?></div>
                        <div class="booking-info">Check-in: <?= $check_in; ?></div>
                        <div class="booking-info">Check-out: <?= $check_out; ?></div>

                        <div class="booking-info">Status: 
                            <span class="status-<?= $row['status']; ?>">
                                <?= $row['status']; ?>
                            </span>
                        </div>


                        <?php if ($row['status'] == "booked"): ?>
                            <div class="action-group">

                                <form method="POST" action="cancel_request.php">
                                    <input type="hidden" name="booking_id" value="<?= $row['booking_id']; ?>">
                                        <button type="submit" class="btn-cancel" 
                                                onclick="return confirm('Are you sure you want to cancel this booking?')">
                                            Request Cancel
                                        </button>
                                </form>

                                <form method="GET" action="invoice.php">
                                    <input type="hidden" name="booking_id" value="<?= $row['booking_id']; ?>">
                                    <button type="submit" class="btn-invoice">View Invoice</button>
                                </form>

                            </div>

                        <?php elseif ($row['status'] == "cancel_requested"): ?>
                            <i style="color:#e67e22;">Cancellation pending approval...</i>

                        <?php elseif ($row['status'] == "canceled"): ?>
                            <i style="color:#c0392b;">Booking canceled</i>
                        <?php endif; ?>
                    </div>
                    
            <?php endwhile; ?>
            </div>

        <?php else: ?>
            <p class="no-bookings">
                You have no bookings yet. Start by 
                <a href="../index.php">booking a hotel</a>!
            </p>
        <?php endif; ?>
    </div>
    <?php include '../includes/footer.php'; ?>

</body>
</html>
