<?php
include '../includes/connection.php';
session_start();
// if(!(isset($_SESSION['otp-verified']) || $_SESSION['password-verified'])){
//     header('location:forget_password.php');
// }
$email = $_SESSION['forget_email'];
$msg_error = "";
$sucess_msg = "";

if(isset($_POST['change_password'])){
    $new_password = $_POST['new_password'];
    $conform_password = $_POST['conform_password'];
    

        $hashed_password = password_hash($new_password,PASSWORD_DEFAULT);
        $updatequery = "update users set password ='$hashed_password' where(email ='$email')";
        if(mysqli_query($con,$updatequery)){
            $sucess_msg = "Password update sucessfully"."<a href='login.php'>Login here</a>";
            unset($_SESSION['otp-verified']);
            unset($_SESSION['forget_email']);
            header('location:login.php');
        }
        else
        {
            $msg_error =  "Problem in Password update";
        }
    

}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/formstyle.css">
    <title>Change Password</title>
</head>
<body>
    <div class="container">
        <form method="post" id="Change_Password">
            <div class="form-box">
                <h2>Change Password</h2>
                
                    <p><b>Your Email:</b><b style="color: blue"><?=  $email?></b></p>
                
                <div>
                    <div class="password-icon">
                        <input type="password" name="new_password" id="password" placeholder="Enter the New Password " class="password_input" required>
                        <img src="../uploads/login_icon/hide.png" alt="Hide Icon" class="hide_icon">
                    </div>
                </div>
                <div>
                    <div class="password-icon">
                        <input type="password" name="conform_password" id="confirm_password" placeholder="Enter the Conform Password" class="password_input" required>
                        <img src="../uploads/login_icon/hide.png" alt="Hide Image" class="hide_icon" >
                    </div>
                    <p class="error" id="confirm_password_error"><?= $msg_error ?></p>
                </div>
                <button type="submit" name="change_password">Change Password</button>
                <p class="success"><?= $sucess_msg?></p>
            </div>
        </form>
    </div>
</body>
</html>

<script src="../script/form_icon.js"></script>
<script src="../script/register_validate.js"></script>
