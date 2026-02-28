<?php
session_start();
include '../includes/connection.php';
include '../includes/functions.php';
include '../includes/navbar.php';



$id = $_GET['id'] ?? 0;
$result = mysqli_query($con, "SELECT * FROM users WHERE user_id='$id'");
$user = mysqli_fetch_assoc($result);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($con, $_POST['user_name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $role = $_POST['role'];

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $update_query = "UPDATE users SET name='$name', email='$email', password='$password',level = '$role' WHERE user_id='$id'";
    } else {
        $update_query = "UPDATE users SET name='$name', email='$email', level = '$role' WHERE user_id='$id'";
    }

    
    if(mysqli_query($con, $update_query)){

        header("Location: index.php");
        exit();
    }
    echo mysqli_error($con);
}
?>

<!DOCTYPE html>
<html>
<head>
     <link rel="stylesheet" href="../style/formstyle.css">
    <title>Edit User</title>
    
</head>
<body>
    
    <div class="edit-user-container">
    
        <form method="POST" enctype="multipart/form-data">
            
            <div class="form-container">
            <h2>Edit User</h2>
        <div>
            <label>User Name:</label>
            <input type="text" name="user_name" value="<?= htmlspecialchars($user['name']); ?>" required>
        </div>

        <div>
            <label>Email:</label>
            <input type="text" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>
        </div>

        <div>
            <label>Password:</label>
            <input type="password" name="password" placeholder="Leave blank to keep current">
        </div>

        <div>
            <label>Role</label>
            <select name="role">
                <option value="hoteladmin"<?php if($user['level']=='hoteladmin') echo "selected"?>>Hotel-Admin</option>
                <option value="admin"<?php if($user['level']=='admin') echo "selected"?>>Admin</option>
            </select>
        </div>


        <button type="submit">Update User</button>
        </div>
      </div>
      </div>
    </form>


    </body>
    </html>