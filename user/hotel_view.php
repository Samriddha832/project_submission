<?php
session_start();
include 'connection.php';
include 'navbar.php';

if (!isset($_GET['id'])) {
    die("Hotel not selected!");
}

$hotel_id = (int)$_GET['id'];

$query = "SELECT * FROM hotels WHERE hotel_id='$hotel_id'";
$result = mysqli_query($con, $query);

if ($row = mysqli_fetch_assoc($result)) {
    echo "<h2>" . $row['hotel_name'] . "</h2>";
    echo "<p>" . $row['location'] ."</p>";
    echo "<p>Available Rooms: " . $row['available_rooms'] . "</p>";
    
    if (!empty($row['hotel_image'])) {
        echo "<img src='" . $row['hotel_image'] . "' width='300'><br>";
    }
    ?>

    <form method="POST" action="book_process.php">
        <input type="hidden" name="hotel_id" value="<?php echo $hotel_id; ?>">
        Check-in: <input type="date" name="check_in" required><br>
        Check-out: <input type="date" name="check_out" required><br>
        Rooms: <input type="number" name="rooms" min="1" required><br>
        <button type="submit">Book Now</button>
    </form>

    <?php
} else {
    echo "Hotel not found!";
}
include '../includes/footer.php';

?>
