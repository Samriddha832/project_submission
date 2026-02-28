

<?php
if (session_status() == PHP_SESSION_NONE) session_start();
include 'includes/connection.php';
include 'includes/functions.php';
include 'includes/navbar.php';

if (!isset($_SESSION['user_id'])) {
    die("You must <a href='login.php'>login</a> to view profile.");
}



$user_id = $_SESSION['user_id'];

// Fetch current user data
$result = mysqli_query($con, "SELECT * FROM users WHERE user_id='$user_id'");
$row = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style/formstyle.css">
</head>
<body>
    <div id="tostBox"></div>
    <link rel="stylesheet" href="Tost_Message/style.css">
    <script src="Tost_Message/script.js"></script>

    <?php
    if(!empty($_SESSION['toast'])){
        $tost = $_SESSION['toast'];

?>

<script>showTost("<?= $tost['message'] ?>","<?= $tost['status'] ?>")</script>

<?php
unset($_SESSION['toast']);

}





?>
    
</body>
</html>
<div class="wrapper">
    <div class="content">
        <h2>My Profile</h2>

        <div class="profile-card">
            <?php
            // Display current user image
            $img_path = "uploads/user/" . $row['user_image'];
            if (!empty($row['user_image']) && file_exists($img_path)) {
                echo "<img src='" . str_replace('\\','/',$img_path) . "' class='profile-img'>";
            } else {
                echo "<img src='uploads/user/default.png' class='profile-img'>";
            }
            ?>

            <p><b>Name:</b> <?php echo htmlspecialchars($row['name']); ?></p>
            <p><b>Email:</b> <?php echo htmlspecialchars($row['email']); ?></p>

           <a href="edit_profile.php" class="edit-btn"> <button>Edit Profile</button></a>
        </div>
    </div>
</div>

    <?php include 'includes/footer.php' ?>

