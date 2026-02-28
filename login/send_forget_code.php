<?php
require '../Php_Send_Mail/send_mails.php';
include '../includes/connection.php';
session_start();

$email = $_SESSION['forget_email'];

$result = mysqli_query($con,"SELECT * FROM users WHERE email='$email' LIMIT 1");
if(mysqli_num_rows($result) != 1){
    echo "<script>console.log('Email not found!');</script>";
    exit;
}

$otp_result = mysqli_query($con, "SELECT * FROM otp WHERE email='$email' AND created_at >= NOW() - INTERVAL 5 MINUTE LIMIT 1");

if(mysqli_num_rows($otp_result) > 0){
    $row = mysqli_fetch_assoc($otp_result);
    $otp = $row['otp'];

    if($row['email_sent'] == 0){
        $subject = 'Verify Your OTP';
        $body = "<p>Reset your password using the OTP:</p><h2>$otp</h2><p>This OTP expires in 5 minutes.</p>";
        if(send_mail($email, $subject, $body)){
            mysqli_query($con, "UPDATE otp SET email_sent=1 WHERE id={$row['id']}");
        }
    }
} else {
    $otp = rand(100000, 999999);
    mysqli_query($con, "INSERT INTO otp(email, otp, created_at, email_sent) VALUES('$email', '$otp', NOW(), 0)");

    // Send the email
    $subject = 'Verify Your OTP';
    $body = "<p>Reset your password using the OTP:</p><h2>$otp</h2><p>This OTP expires in 5 minutes.</p>";
    if(send_mail($email, $subject, $body)){
        // Mark email as sent
        $last_id = mysqli_insert_id($con);
        mysqli_query($con, "UPDATE otp SET email_sent=1 WHERE id=$last_id");
    }
}

echo "OTP processing done";
?>
