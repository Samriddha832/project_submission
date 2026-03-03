<?php
session_start();
include '../includes/connection.php';
include '../includes/navbar.php';

if (!isset($_GET['admin_id'])) {
    die("No admin ID provided!");
}

$hotel_admin_id = intval($_GET['admin_id']);
$sucessmsg = "";

if (isset($_POST['submit'])) {

    $hotelName = mysqli_real_escape_string($con, $_POST['hotel_name']);
    $location = mysqli_real_escape_string($con, $_POST['location']);
    $description = mysqli_real_escape_string($con, $_POST['description']);

    // Upload hotel image
    $hotel_image = "";
    if (!empty($_FILES['hotel_image']['name'])) {

        $uploadDir = "../uploads/hotels/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $hotel_image = time() . "_" . basename($_FILES['hotel_image']['name']);
        $targetFile = $uploadDir . $hotel_image;
        move_uploaded_file($_FILES['hotel_image']['tmp_name'], $targetFile);
    }

    // Insert hotel
    $q = "INSERT INTO hotels (hotel_name, location, hotel_admin_id, about, hotel_image)
          VALUES ('$hotelName', '$location', $hotel_admin_id, '$description', '$hotel_image')";

    if (mysqli_query($con, $q)) {
        $new_hotel_id = mysqli_insert_id($con);
        $hotel_admin_id_query = "select hotel_admin_id from hotels where (hotel_id =$newHotelId)) limit 1" ;
        $_SESSION['hotel_admin_id'] = $hotel_admin_id;
        header("location:index.php");
        exit();
    } 
    else {
        $sucessmsg = "Error inserting hotel: " . mysqli_error($con);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../style/formstyle.css">
    <title>Add Hotel</title>
</head>
<body>

<div class="container">

        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-box">
                
            <h2>Add Hotel</h2>

                <div>
                    <label class="form-row">Hotel Name:</label>
                    <input type="text" name="hotel_name" placeholder="Enter Hotel Name" required>
                </div>

                <div>
                    <label class="form-row">Location:</label>
                    <input type="text" name="location" placeholder="Enter Hotel Location" required>
                </div>

                <div>
                    <label class="form-row">Hotel Image:</label>
                    <input type="file" name="hotel_image" accept=".jpg,.jpeg,.png" >
                </div>

                <div>
                    <label class="form-row">About</label>
                    <textarea name="description" placeholder="About Hotel"></textarea>
                </div>

                <button type="submit" name="submit">Add Hotel</button>

                <p style="color:red;"><?= $sucessmsg ?></p>

            </div>
        </form>

    
</div>

</body>
</html>
