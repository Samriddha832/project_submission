<?php
if (session_status() == PHP_SESSION_NONE) session_start();
include 'includes/connection.php';
include 'includes/functions.php';
include 'includes/navbar.php';

if (!isset($_SESSION['user_id'])) {
    die("You must <a href='login.php'>login</a> to view profile.");
}

$user_id = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name  = sanitize($con, $_POST['name']);
    $email = sanitize($con, $_POST['email']);

    // Handle image upload
    $image_sql = "";
    if (isset($_FILES['user_image']) && $_FILES['user_image']['error'] == 0) {
        $target_file = "uploads/user/" . $_FILES['user_image']['name'];
        move_uploaded_file($_FILES['user_image']['tmp_name'], $target_file);
        $image_sql = ", user_image='" . $_FILES['user_image']['name'] . "'";
    }

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $update_query = "UPDATE users SET name='$name', email='$email', password='$password' $image_sql WHERE user_id='$user_id'";

    } else {
        $update_query = "UPDATE users SET name='$name', email='$email' $image_sql WHERE user_id='$user_id'";
    }

    
    if(mysqli_query($con, $update_query)){
    $_SESSION['toast'] = [
        'message' => 'Profile Edited Successfully',
        'status'  => 'success'
    ];
    header('location:view_profile.php');
    exit();   // ✅ VERY IMPORTANT
}


}

// Fetch current user data
$result = mysqli_query($con, "SELECT * FROM users WHERE user_id='$user_id'");
$row = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/formstyle.css">
    <title>Edit Profile</title>
</head>
<body>
    

<div class="wrapper">
    <div class="content">
        
        <div class="profile-card">
            <h2>View / Edit Profile</h2>
            <?php
            $img_path = "uploads/user/" . $row['user_image'];
            if (!empty($row['user_image']) && file_exists($img_path)) {
                echo "<img src='" . str_replace('\\','/',$img_path) . "' class='profile-img'>";
            } else {
                echo "<img src='uploads/user/default.png' class='profile-img'>";
            }
            ?>

            <form method="POST" action="" enctype="multipart/form-data" class="profile-form">
                <div class="form-row">
                    <label>Name:</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required>
                </div>

                <div class="form-row">
                    <label>Email:</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required>
                </div>

                <div class="form-row">
                    <label>Password:</label>
                    <input type="password" name="password" placeholder="Leave blank to keep current">
                </div>

                <div class="form-row">
                    <label>Upload Image:</label>
                    <input type="file" name="user_image" accept="image/*">
                </div>

                <div class="form-row submit-row">
                    <button type="submit">Update Profile</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php' ?>

</body>
</html>
