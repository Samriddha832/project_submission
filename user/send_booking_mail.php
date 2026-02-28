<?php
require '../includes/connection.php';
require '../Php_Send_Mail/send_mails.php';

$booking_id = (int)($_GET['booking_id'] ?? 0);
if ($booking_id < 1) exit;

$q = mysqli_query($con, "
    SELECT b.email_send, u.email, b.check_in, b.check_out,h.hotel_image,hr.room_image
    FROM bookings b
    JOIN hotels h on b.hotel_id = h.hotel_id
    JOIN hotel_rooms hr on b.room_id = hr.room_id
    JOIN users u ON b.user_id = u.user_id
    WHERE b.booking_id = $booking_id
    LIMIT 1
");

$row = mysqli_fetch_assoc($q);

if (!$row || $row['email_sent'] == 1) exit;

$check_in  = (new DateTime($row['check_in']))->format('Y-m-d');
$check_out = (new DateTime($row['check_out']))->format('Y-m-d');

// sending the Mail

    $subject = "Booking Confirmation";
    $body = "
    <table width='100%' cellpadding='12' style='font-family:Arial,sans-serif;'>
    <tr>
    <td>
        <h2>Thank you for booking with us!</h2>

        <p>Your booking has been successfully confirmed.</p>

        <p><b>Check-in:</b> $check_in</p>
        <p><b>Check-out:</b> $check_out</p>

        <p>
            We look forward to hosting you.<br><br>
            <strong>Hotel Booking System</strong><br>
             © ".date('Y')." Hotel Booking System · Powered by <b>Suman</b>
        </p>
    </td>
    </tr>
    </table>
";


    $attachments = [
        "../uploads/rooms/".$row['room_image'],
        "../uploads/hotels/" . $row['hotel_image']
    ];
    if(send_mail($row['email'], $subject, $body, $attachments)){
        mysqli_query($con,
        "UPDATE bookings SET email_send = 1 WHERE booking_id = $booking_id"
    );

    }


?>

