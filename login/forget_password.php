<?php

include '../includes/connection.php';
session_start();
$error_msg = '';

if(isset($_POST['submit'])){
    $email = mysqli_real_escape_string($con,$_POST['email']);
    $result = mysqli_query($con,"select *from users where(email = '$email') limit 1");
    if(mysqli_num_rows($result)==1){
        $_SESSION['forget_email'] = $email;
        ?>
        <script>
            fetch('send_forget_code.php')
            .then(response => response.text())  
            .then(data => console.log("Mail sent:", data)) 
            .catch(error => console.error("Error:", error)); 
        </script>

        <?php
        header('location:verify_otp.php');

    }
    else
    {
        $error_msg =  "Invalid email please register";
    }
   
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forget Password</title>
    <link rel="stylesheet" href="../style/formstyle.css">
</head>
<body>
    <div class="container">
    <form method="post">
        <div class="form-box">
            <h2>Forget Password</h2>
            <div>
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" placeholder="Enter your Email Address" required>
                <p class="error"><?= $error_msg ?></p>
            </div>
            
                <button type="submit" name="submit">Forget password</button>
           
        </div>
        
    </form>
    </div>
    
</body>
</html>



