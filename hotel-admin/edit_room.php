<?php
session_start();
include '../includes/connection.php';
include '../includes/functions.php';
include '../includes/navbar.php';

if ($_SESSION['level'] != 'hoteladmin') {
    header("Location: ../login/login.php");
    exit();
}

$id = $_GET['id'] ?? 0;
$result = mysqli_query($con, "SELECT * FROM hotel_rooms WHERE room_id='$id'");
$room = mysqli_fetch_assoc($result);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $room_number = sanitize($con, $_POST['room_number']);
    $room_type = sanitize($con, $_POST['room_type']);
    
    $price_per_room = sanitize($con, $_POST['price_per_room']);
    $about = sanitize($con,$_POST['description']);
    $image_sql = "";

    if (isset($_FILES['room_image']) && $_FILES['room_image']['error'] == 0) {
        $uploadDir = "uploads/rooms/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $room_image = basename($_FILES['room_image']['name']);
        $targetFile = $uploadDir . $hotel_image;
        move_uploaded_file($_FILES['room_image']['tmp_name'], $targetFile);

        $image_sql = ", room_image='$room_image'";
    }

    $update = "UPDATE hotel_rooms 
               SET room_number='$room_number',about_rooms='$about', room_type='$room_type',  room_price=$price_per_room $image_sql 
               WHERE room_id='$id'";

    mysqli_query($con, $update);
    $_SESSION['room_msg'] =["text" =>"Room Edited Succesfully!" ,"type" =>"success"];
    header("Location: admin_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
     <link rel="stylesheet" href="../style/formstyle.css">
    <title>Edit Room</title>
    
</head>
<body>
    
    <div class="container">
        

            <form method="POST" enctype="multipart/form-data">
                
                <div class="form-box">
                <h2>Edit Room</h2>
                <div>
                    <label>Room Number:</label>
                    <input type="text" name="room_number" value="<?= htmlspecialchars($room['room_number']); ?>" required>
                </div>

                <div>
                    <label>Room Type:</label>
                    <select name="room_type" >
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
                    <label>Price per Room:</label>
                    <input type="number" name="price_per_room" value="<?= htmlspecialchars($room['room_price']); ?>" required>
                </div>

                <div>
                    <label>Room Image:</label>
                    <input type="file" name="room_image" accept="image/*">
                </div>

                <div>
                        <label for="discription">About</label>
                        <textarea name="description" id="discription" placeholder="About Room"><?= $room['about_rooms'] ?></textarea>
                </div>

                <button type="submit">Update Room</button>
        
            </form>

        </div>
    </div>


    </body>
    </html>