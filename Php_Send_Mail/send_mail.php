<?php
// require 'send_main_function.php';

$email = "sumanthapa302702@gmail.com";
$subject = "Booking Confirmation";
$body = "<h3>Your booking is confirmed</h3>";

$filePath = "../uploads/rooms/Room1.jpg"; // image / pdf / doc

$result = send_mail($email, $subject, $body, $filePath);

if ($result === true) {
    echo "Mail sent with attachment";
} else {
    echo $result;
}





?>