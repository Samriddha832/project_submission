<?php
include '../includes/connection.php';
session_start();

// Only process the form
if(isset($_POST['submit'])){
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $result = mysqli_query($con, "SELECT * FROM users WHERE email = '$email' LIMIT 1");

    if(mysqli_num_rows($result) == 1){
        // User exists → just proceed to OTP page
        $_SESSION['forget_email'] = $email;
        $_SESSION['toast']= ["message"=>"Otp sucessfully send to your email","type"=>"success"];
        header('Location: verify_otp.php');
        exit();
    } else {
        // User not registered → show toast
        $_SESSION['toast'] = ["message" => "Email is not registered in our system,Please try with diffrent email", "type" => "error"];
        header('Location:forget_password.php'); // reload same page to show toast
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<title>Forget Password</title>
<link rel="stylesheet" href="../style/formstyle.css">
<link rel="stylesheet" href="../Tost_Message/style.css">
<script src="../Tost_Message/script.js"></script>
</head>
<body>

<div id="tostBox"></div>

<?php
// Only show toast if it exists
if(!empty($_SESSION['toast'])){
    $toast = $_SESSION['toast'];
    echo "<script>showTost('{$toast['message']}','{$toast['type']}');</script>";
    unset($_SESSION['toast']);
}
?>

<div class="container">
    <form method="post">
        <div class="form-box">
            <h2>Forget Password</h2>
            <div class="form-row">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" placeholder="Enter your Email Address" required>
            </div>
            <button type="submit" name="submit">Forget password</button>
        </div>
    </form>
</div>

</body>
</html>