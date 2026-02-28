<?php
include '../includes/connection.php';
session_start();
$email = $_SESSION['forget_email'];
$max_attempt = 5;
$error_msg = "";

if(isset($_POST['submit'])){
    $otp = mysqli_real_escape_string($con, $_POST['otp']);
    $result = mysqli_query($con, " SELECT * FROM otp WHERE email='$email'   and created_at >= now() - INTERVAL 5 MINUTE LIMIT 1");

    if(mysqli_num_rows($result) == 0){
        mysqli_query($con, "delete from otp where(email = '$email')");
        echo "<p style = 'color:red'>OTP Expired Please Resend the New Otp <br><br><button><a href='forget_password.php'>Hya Bata</button></a></p>";
        exit();
    }
    $row = mysqli_fetch_assoc($result);

    if($row['attempt'] >=$max_attempt){
        mysqli_query($con, "delete from otp where(email = '$email')");
        echo "<h2 style='color:red'>You reached maximum attempts.</h2>";
        
        echo "<a href='forget_password.php'>Resend OTP</a>";
        exit();
    }
    if($row['otp'] ==$otp){
        $_SESSION['otp-verified'] = true;
        mysqli_query($con, "delete from otp where(email = '$email')");
        header('location:reset_password.php');
    }
    else
    {
        $id = $row['id'];
        mysqli_query($con,"update otp set attempt = attempt +1 where id = {$row['id']} and email = '$email'");
        $remaning_attempt = $max_attempt - ($row['attempt'] + 1);
        $error_msg = "Invalid OTP. Remaining attempts: ".$remaning_attempt;
        
    }


}


?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Otp</title>
    <link rel="stylesheet" href="../style/formstyle.css">
</head>
<body>
     <script>
        // Send the OTP email in the background
        fetch('send_forget_code.php') 
            .then(response => response.text())
            .then(data => console.log("Mail sent:", data))
            .catch(error => console.error("Error:", error));
    </script>
    <div class="container">
        <form action="" method="post">
        <div class="form-box">
            <h2>Verify OTP</h2>
            <div>
                <label for="otp">Enter Otp:</label>
                <input type="number" name="otp" id="otp" placeholder="Chek the Otp in your gmail" required>
                <p class="error"><?= $error_msg ?></p>
            </div>
            <button type="submit" name="submit">Verify Otp</button>
        </div>
    </form>
    </div>
</body>
</html>
