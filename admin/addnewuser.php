<?php
session_start();
include '../includes/connection.php';
include '../includes/navbar.php';

$sucessmsg = '';
$name = '';
$email = '';
$password = '';

if (isset($_POST['submit'])) {

    $name = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $role = trim($_POST['select-user']);

    // Upload image
    $user_image = "";
    if (!empty($_FILES['user_image']['name'])) {

        $uploadDir = "../uploads/users/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $user_image = time() . "_" . basename($_FILES['user_image']['name']);
        $targetFile = $uploadDir . $user_image;
        move_uploaded_file($_FILES['user_image']['tmp_name'], $targetFile);
    }

    // Insert user
    $query = "INSERT INTO users (name, email, password, level, user_image)
              VALUES ('$name', '$email', '$hashed_password', '$role', '$user_image')";

    if (mysqli_query($con, $query)) {

        $newUserId = mysqli_insert_id($con);

        if ($role == 'hoteladmin') {
            header("Location: addhotel.php?admin_id=" . $newUserId);
            exit();
        }

        $sucessmsg = "User created successfully!";
    } 
    else {
        $sucessmsg = "Problem in user creation: " . mysqli_error($con);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add new user</title>
    <link rel="stylesheet" href="../style/formstyle.css">
</head>
<body>

<div class="container">
    
        
        <form method="post" enctype="multipart/form-data">
            <div class="form-container">
                <h2>Add User</h2>

                <div>
                    <label>User Name</label>
                    <input type="text" name="username" value="<?= $name ?>" required>
                </div>

                <div>
                    <label>Password</label>
                    <input type="password" name="password" value="<?= $password ?>" required>
                </div>

                <div>
                    <label>Email</label>
                    <input type="text" name="email" value="<?= $email ?>" required>
                </div>

                <div>
                    <label>User Role</label>
                    <select name="select-user">
                        <option value="hoteladmin">Hotel Admin</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <div>
                    <label>User Image</label>
                    <input type="file" name="user_image" accept=".jpg,.jpeg,.png" >
                </div>

                <button type="submit" name="submit">Submit</button>

                <p style="color:red;"><?= $sucessmsg ?></p>

            </div>
        </form>

    
</div>

</body>
</html>
