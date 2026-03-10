<?php
include 'backend/userindex.php';

// Ensure check-in/check-out are valid
$today = date('Y-m-d');
if ($check_in < $today) {
    $check_in = $today;
}
if ($check_out <= $check_in) {
    $check_out = date('Y-m-d', strtotime($check_in . ' +1 day'));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<title>Hotel Booking System</title>
<link rel="stylesheet" href="style/userindexstyle.css">
</head>
<body>
    <div id="tostBox"></div>
    <link rel="stylesheet" href="Tost_Message/style.css">
    <script src="Tost_Message/script.js"></script>

   

<?php



if(!empty($_SESSION['toast'])){
    $toast = $_SESSION['toast']; ?>

<script>
        showTost("<?= $toast['message'] ?>","<?= $toast['type'] ?>");
    </script>
<?php
unset($_SESSION['toast']);

}


?>

<div class="hero-section" id="filter">
    <h1 class="text-search-h2">Find Hotel By Any Filter</h1>
    <form method="GET" class="search-box" action="#hotel_info">
        <input type="text" name="search" placeholder="Search hotel or location..." value="<?= htmlspecialchars($search) ?>">
        <input type="date" name="check_in" value="<?= $check_in ?>" class="check_in" required>
        <input type="date" name="check_out" value="<?= $check_out ?>" class="check_out" required>

        <select name="room_type">
            <option value="" <?= $room_type == '' ? 'selected' : '' ?>>All Types</option>
            <option value="Standard Room" <?= $room_type == 'Standard Room' ? 'selected' : '' ?>>Standard Room</option>
            <option value="Deluxe Room" <?= $room_type == 'Deluxe Room' ? 'selected' : '' ?>>Deluxe Room</option>
            <option value="Super Deluxe Room" <?= $room_type == 'Super Deluxe Room' ? 'selected' : '' ?>>Super Deluxe Room</option>
            <option value="Family Room" <?= $room_type == 'Family Room' ? 'selected' : '' ?>>Family Room</option>
            <option value="Suite Room" <?= $room_type == 'Suite Room' ? 'selected' : '' ?>>Suite Room</option>
            <option value="Executive Suite" <?= $room_type == 'Executive Suite' ? 'selected' : '' ?>>Executive Suite</option>
            <option value="Twin Bedroom" <?= $room_type == 'Twin Bedroom' ? 'selected' : '' ?>>Twin Bedroom</option>
            <option value="Luxury Room" <?= $room_type == 'Luxury Room' ? 'selected' : '' ?>>Luxury Room</option>
            <option value="Honeymoon Suite" <?= $room_type == 'Honeymoon Suite' ? 'selected' : '' ?>>Honeymoon Suite</option>
        </select>

        <input type="number" name="min_price" placeholder="Min Price" value="<?= $min_price ?>">
        <input type="number" name="max_price" placeholder="Max Price" value="<?= $max_price ?>">
        <button type="submit">Search</button>
    </form>
</div>

<div class="wrapper" id="hotel_info">
    <div class="hotels-container">
        <?php
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $image = !empty($row['room_image']) ? "uploads/rooms/" . $row['room_image'] : "https://via.placeholder.com/260x180?text=No+Image";
        ?>
        <div class="hotel-card">
            <div class="hotel-image">
                <img src="<?= $image ?>" alt="Room Image">
            </div>
            <div class="hotel-info">
                <h2 class="hotel-title"><?= htmlspecialchars($row['hotel_name']) ?></h2>
                <p class="hotel-location"><?= htmlspecialchars($row['location']) ?></p>
                <p class="hotel-type"><b>Room Type:</b> <?= htmlspecialchars($row['room_type']) ?></p>
                <p class="hotel-price">NPR <?= number_format($row['room_price'], 2) ?></p>

                <form method="POST" action="user/check_avilabilty.php" class="book-form">
                    <input type="hidden" name="room_id" value="<?= $row['room_id'] ?>">
                    <input type="hidden" name="room_type" value="<?= $row['room_type'] ?>">
                    <label>Check-in</label>
                    <input type="date" name="check_in" value="<?= $check_in ?>" class="check_in" required>
                    <label>Check-out</label>
                    <input type="date" name="check_out" value="<?= $check_out ?>" class="check_out" required>
                    <button type="submit">Book Now</button>
                    <button type="button" onclick="window.location='user/view_room_detail.php?room_id=<?= $row['room_id'] ?>'">View Detail</button>
                </form>
            </div>
        </div>
        <?php
            }
        } else {
            echo "<div class='custom-alert'><p>No rooms available for the selected dates and Price.</p><h2>Please Change Search Filter and try again</h2><a href='#filter'><button>Go to Filter</button></a></div>";
        }
        ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<script src="script/date_validate.js"></script>
</body>
</html>
