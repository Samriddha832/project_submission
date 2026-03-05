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
    $_SESSION['user_name'] = $name;
    $email = trim($_POST['email']);
    $_SESSION['email'] = $email;
    $password = trim($_POST['password']);
    $_SESSION['password'] = $password;
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $role = trim($_POST['select-user']);

    $chekemail = "SELECT * FROM users where(email = '$email') LIMIT 1";
    $result = mysqli_query($con,$chekemail);
    if(mysqli_num_rows($result) ==1){
        $_SESSION['toast'] = ["message"=>"Email Already Register,Use diffrent email","type"=>"error"];
        header('location:addnewuser.php');
        exit();
    }

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
            $_SESSION['toast'] = ["message"=>"User Created SuccessFully,Now Assign the Hotel for the User By filling this form","type"=>"success"];
            unset($_SESSION['user_name']);
            unset($_SESSION['password']);
            unset($_SESSION['email']);
            header("Location: addhotel.php?admin_id=" . $newUserId);
            exit();
        }

        
    } 
    else {
        $errormsg = "Problem in user creation: " . mysqli_error($con);
        $_SESSION['toast'] = ["message"=>$errormsg,"type"=>"error"];
        header('location:index.php');
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
    <div id="tostBox"></div>
    <link rel="stylesheet" href="../Tost_Message/style.css">
    <script src="../Tost_Message/script.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<div class="container">

    <?php 
    
    if(!empty($_SESSION['toast'])){ 
    $toast = $_SESSION['toast'];    ?>
    <script>showTost("<?= $toast['message'] ?>","<?= $toast['type'] ?>"); </script>  

   <?php
   unset($_SESSION['toast']);

    }
    
    ?>    
        
        <form method="post" enctype="multipart/form-data">
            <div class="form-box">
                <h2>Add User</h2>

                <div class="form-row">
                    <label>User Name</label>
                    <input type="text" name="username" value="<?= $_SESSION['user_name'] ?>" required>
                </div>

                <div class="form-row">
                    <label>Password</label>
                    <div class="password-icon">
                        <input type="password" name="password" class="password_input" required>
                        <img src="../uploads/login_icon/hide.png" class="hide_icon">
                    </div>
                </div>

                <div class="form-row">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= $_SESSION['email'] ?>" required>
                </div>

                <div class="form-row">
                    <label>User Role</label>
                    <select name="select-user">
                        <option value="hoteladmin">Hotel Admin</option>
                    </select>
                </div>

                <div class="form-row">
                    <label>User Image</label>
                    <input type="file" name="user_image" accept=".jpg,.jpeg,.png" required >
                </div>

                <button type="submit" name="submit">Submit</button>

                <p style="color:red;"><?= $sucessmsg ?></p>

            </div>
        </form>

    
</div>

</body>
</html>


<script src="../script/form_icon.js"></script>

