<?php
if (session_status() == PHP_SESSION_NONE) 
session_start();
include 'connection.php';
$base_url = "/Hotel-Booking/";

$level = null;
$user_id = $_SESSION['user_id'] ?? null;

if ($user_id) {
    $user = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM users WHERE user_id='$user_id'"));
    $level = $user['level'];
}
?>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<nav class="navbar">
    <div class="nav-left">
        <a class="logo" href="<?php echo ($level === 'hoteladmin') ? $base_url.'hotel-admin/admin_dashboard.php' :(($level == 'admin')?$base_url.'admin/index.php': $base_url.'index.php'); ?>">
            HotelBooking
        </a>

        <?php if ($user_id && $level=='user'): ?>
            <a href="<?php echo $base_url; ?>user/my_bookings.php">My Bookings</a>
        <?php endif; ?>

        <?php if ($user_id && $level=='hoteladmin'): ?>
            <a href="<?php echo $base_url; ?>hotel-admin/view_hotel.php">Your Hotel</a>
            <a href="<?php echo $base_url; ?>hotel-admin/admin_invoices.php">Invoices</a>
            <a href="<?php echo $base_url; ?>hotel-admin/admin_pending_cancellations.php">Cancellations</a>
        <?php endif; ?>
    </div>

    <div class="nav-right">
        <?php if ($user_id): ?>
            <a href="<?php echo $base_url; ?>view_profile.php">Profile</a>
            <a class="logout-btn" href="<?php echo $base_url; ?>login/logout.php">Logout</a>
        <?php else: ?>
            <a href="<?php echo $base_url; ?>login/login.php">Login</a>
            <a class="register-btn" href="<?php echo $base_url; ?>login/register.php">Register</a>
        <?php endif; ?>
    </div>
</nav>

<style>
body {
    font-family: "Poppins", sans-serif;
    margin: 0;       /* remove default margin */
    padding: 0;      /* remove default padding */
}

/* NAVBAR BASE */
.navbar {
    position: fixed;
    top: 0;
    left: 0;
    
    width: 100%;
    z-index: 999;
    width: 100vw;                 /* full width */
    background: #003b95;
    color: white;
    padding: 14px 20px;           /* reduce side padding */
    display: flex;
    justify-content: space-between; /* left/right alignment */
    align-items: center;
    box-sizing: border-box;
}

.nav-left a,
.nav-right a {
    color: white;
    text-decoration: none;
    margin-right: 20px;
    font-size: 15px;
    font-weight: 400;
}

.logo {
    font-size: 22px;
    font-weight: 600;
    margin-right: 25px;
}

/* Align logout button nicely on the far right */
.nav-right, .nav-left {
    display: flex;
    align-items: center;
    gap: 15px; /* space between links */
}

.nav-right a:last-child {
    margin-right: 0;
}

.navbar a:hover {
    opacity: 0.8;
    text-decoration: underline
}

/* BUTTONS */
.register-btn,
.logout-btn {
    background: #1a73e8;
    padding: 6px 14px; /* smaller button */
    border-radius: 6px;
    font-weight: 500;
    font-size: 14px;
}

.register-btn:hover,
.logout-btn:hover {
    background: #0057c2;
}

/* Responsive fix for mobile */
@media (max-width: 768px) {
    .navbar {
        flex-direction: column;
        align-items: flex-start;
        padding: 10px 20px;
    }
    .nav-left,
    .nav-right {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        width: 100%;
    }
}

</style>
