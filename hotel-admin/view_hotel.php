<?php
session_start();
include '../includes/navbar.php';
include '../includes/connection.php';
include '../includes/functions.php';
$msg = '';

// Only hotel admins
if ($_SESSION['level'] != 'hoteladmin') {
    header("Location: ../login/login.php");
    exit();
}

// Fetch the hotel for this admin
$hotel_admin_id = $_SESSION['user_id'];
$query = "SELECT * FROM hotels WHERE hotel_admin_id = $hotel_admin_id LIMIT 1";
$result = mysqli_query($con, $query);
$hotel = mysqli_fetch_assoc($result);

// Success message from add/edit pages

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<title>Hotel Admin Profile</title>
<style>
body {
    font-family: "Poppins", sans-serif;
    background: #f0f2f5;
    margin: 0;
    padding: 0;
}

.container {
    max-width: 900px;
    margin: 100px auto;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
    padding: 30px;
}

h1 {
    text-align: center;
    color: #003b95;
    margin-bottom: 30px;
}

.profile {
    display: flex;
    gap: 40px;
    flex-wrap: wrap;
}

.profile-image {
    flex: 1;
    min-width: 250px;
}

.profile-image img {
    width: 100%;
    border-radius: 12px;
    object-fit: cover;
    max-height: 300px;
}

.profile-details {
    flex: 2;
    min-width: 300px;
}

.profile-details h2 {
    margin-top: 0;
    color: #1a73e8;
}

.profile-details p {
    font-size: 16px;
    margin: 8px 0;
    color: #555;
}

.actions {
    margin-top: 20px;
}

.actions a {
    display: inline-block;
    padding: 10px 18px;
    background: #1a73e8;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    margin-right: 12px;
}

.actions a.add-room {
    background: #28a745;
}

.actions a.delete {
    background: #e84118;
}

.actions a:hover {
    opacity: 0.9;
}

.msg.success {
    padding: 10px;
    background: #4cd137;
    color: white;
    border-radius: 5px;
    margin-bottom: 20px;
    text-align: center;
}

.msg.error {
    padding: 10px;
    background: #e84118;
    color: white;
    border-radius: 5px;
    margin-bottom: 20px;
    text-align: center;
}

@media(max-width:768px){
    .profile {
        flex-direction: column;
    }
}
</style>
</head>
<body>
    <div id="tostBox"></div>
    <link rel="stylesheet" href="../Tost_Message/style.css">
    <script src="../Tost_Message/script.js"></script>

<div class="container">
    <?php 
    if(!empty($_SESSION['toast'])){
        $toast = $_SESSION['toast']; ?>
<script>
    showTost("<?= $toast['message'] ?>","<?= $toast['type'] ?>");
</script>

  <?php  }
  unset($_SESSION['toast']);
    
    ?>
    <h1>Your Hotel Profile</h1>

    <?php if ($msg != ""): ?>
        <div class="msg <?php echo $msgType; ?>"><?php echo $msg; ?></div>
    <?php endif; ?>

    <?php if($hotel): ?>
        <div class="profile">
            <div class="profile-image">
                <img src="../uploads/hotels/<?php echo htmlspecialchars($hotel['hotel_image']); ?>" alt="Hotel Image">
            </div>
            <div class="profile-details">
                <h2><?php echo htmlspecialchars($hotel['hotel_name']); ?></h2>
                <p><strong>Location:</strong> <?php echo htmlspecialchars($hotel['location']); ?></p>
                <p><strong>About:</strong> <?php echo htmlspecialchars($hotel['about']); ?></p>

                <div class="actions">
                    <a href="edit_hotel.php?id=<?php echo $hotel['hotel_id']; ?>">Edit Hotel</a>
                    <a class="add-room" href="add_room.php?hotel_id=<?php echo $hotel['hotel_id']; ?>">Add Room</a>
                    
                </div>
            </div>
        </div>
    <?php else: ?>
        <p>No hotel assigned to you yet.</p>
    <?php endif; ?>
</div>

</body>
</html>
