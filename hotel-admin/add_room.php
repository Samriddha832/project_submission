<?php
session_start();
include '../includes/connection.php';
include '../includes/functions.php';
include '../includes/navbar.php';

if ($_SESSION['level'] != 'hoteladmin') {
    header("Location: ../login/login.php");
    exit();
}

$hotel_admin_id = $_SESSION['user_id'];

// Get hotel_id
$hotelResult = mysqli_query($con, "SELECT hotel_id FROM hotels WHERE hotel_admin_id = $hotel_admin_id");
$hotel = mysqli_fetch_assoc($hotelResult);
$hotel_id = $hotel['hotel_id'] ?? 0;

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $room_type   = mysqli_real_escape_string($con, trim($_POST['room_type']));
    $room_price  = (float)$_POST['room_price'];
    $room_number = mysqli_real_escape_string($con, trim($_POST['room_number']));
    $room_image  = '';

    // ===== Image Upload =====
    if (isset($_FILES['room_image']) && $_FILES['room_image']['error'] == 0) {

        $target_dir = "../uploads/rooms/";

        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $filename = basename($_FILES["room_image"]["name"]);
        $newname  = time() . "_" . $filename;
        $target_file = $target_dir . $newname;

        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'webp', 'avif'];

        if (in_array($imageFileType, $allowed_types)) {

            if (move_uploaded_file($_FILES["room_image"]["tmp_name"], $target_file)) {
                $room_image = $newname;
            } else {
                $message = "Image upload failed. Check folder permission.";
            }

        } else {
            $message = "Invalid image format. Allowed: jpg, jpeg, png, webp, avif.";
        }
    } else {
        $message = "Please select an image.";
    }

    // ===== Validation =====
    if (empty($room_type) || $room_price <= 0 || empty($room_number)) {
        $message = "All fields required and price must be greater than 0.";
    }

    // ===== Insert Only If No Error =====
    if (empty($message)) {

        $insertRoom = "INSERT INTO hotel_rooms 
        (hotel_id, room_type, room_price, room_number, room_image)
        VALUES 
        ($hotel_id, '$room_type', $room_price, '$room_number', '$room_image')";

        if (mysqli_query($con, $insertRoom)) {
            $_SESSION['toast'] = [
                "message" => "Room Added Successfully!",
                "type" => "success"
            ];
            header('Location: admin_dashboard.php');
            exit();
        } else {
            $message = "Database error: " . mysqli_error($con);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Room</title>
<link rel="stylesheet" href="../style/formstyle.css">
</head>
<body>

    <div class="container">
        <form method="POST" enctype="multipart/form-data">
            <div class="form-box">
                <h2><?= $message ?></h2>
                <h2>Add New Room</h2>
                <div>
                    <label class="form-row">Room Type:</label>
                    <select name="room_type">
                        <option value="Standard Room">Standard Room</option>
                        <option value="Deluxe Room">Deluxe Room</option>
                        <option value="Super Deluxe Room">Super Deluxe Room</option>
                        <option value="Family Room">Family Room</option>
                        <option value="Suite Room">Suite Room</option>
                        <option value="Executive Suite">Executive Suite</option>
                        <option value="Twin Bedroom">Twin Bedroom</option>
                        <option value="Luxury Room">Luxury Room</option>
                        <option value="Honeymoon Suite">Honeymoon Suite</option>
                    </select>
                </div>

                <div>
                    <label class="form-row">Room Price (per night)</label>
                    <input type="number" name="room_price" min="1" required>
                </div>

                <div>
                    <label class="form-row">Room Number</label>
                    <input type="text" name="room_number" required>
                </div>

                <div>
                    <label class="form-row">Room Image </label>
                    <input type="file" name="room_image" accept=".jpg,.jpeg,.png" required>
                </div>

                    <button type="submit">Add Room</button>

            </div>
        </form>

    </div>

</body>
</html>
