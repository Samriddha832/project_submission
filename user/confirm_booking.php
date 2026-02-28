<?php
session_start();
include '../includes/connection.php';
include '../includes/functions.php';
// require '../Php_Send_Mail/send_mails.php';
check_login();

if(!isset($_SESSION['booking_data'])) die("No booking selected.");

$data = $_SESSION['booking_data'];
$room_id = $data['room_id'];
$user_id = $_SESSION['user_id']; // you were missing this

$roomQuery = mysqli_query($con, "SELECT h.hotel_name,h.hotel_image, hr.room_type, hr.room_price,hr.room_image 
                                 FROM hotel_rooms hr 
                                 JOIN hotels h ON hr.hotel_id = h.hotel_id 
                                 WHERE hr.room_id=$room_id");
$room = mysqli_fetch_assoc($roomQuery);
$total_price = floatval($data['total_price']);
$check_in = $data['check_in'] ;
$check_out = $data['check_out'];
?>
<div class="confirm-box">
<h2>Confirm Booking</h2>
<p>Hotel: <?= htmlspecialchars($room['hotel_name']) ?></p>
<p>Room Type: <?= htmlspecialchars($room['room_type']) ?></p>
<p>Check-in: <?= $check_in ?></p>
<p>Check-out: <?= $check_out ?></p>
<p>Nights: <?= $data['nights'] ?></p>
<p>Total: NPR <?= $total_price ?></p>

<form method="POST" onsubmit="return confirm('Are you sure you want to book this room?')">
    <button type="submit">Confirm Booking</button>
</form>
</div>

<?php
if($_SERVER['REQUEST_METHOD'] == 'POST'){

    // Make sure we have rooms_booked, default to 1 if not set
    $rooms_booked = $data['rooms'] ?? 1;

    $insert = "INSERT INTO bookings 
        (user_id, hotel_id, room_id, rooms_booked, check_in, check_out, nights, total_price, status)
        VALUES
        ($user_id, {$data['hotel_id']}, {$data['room_id']}, $rooms_booked, '{$data['check_in']}', '{$data['check_out']}', {$data['nights']}, {$data['total_price']}, 'booked')";

    if (!mysqli_query($con, $insert)) {
        die("Insert Error: " . mysqli_error($con));
    }

    // After there was booking was completed and next step we send mail to user your booking is conformed
//     $email = $_SESSION['email'];
//     $subject = "Booking Confirmation";
//     $body = "
//     <table width='100%' cellpadding='12' style='font-family:Arial,sans-serif;'>
//     <tr>
//     <td>
//         <h2>Thank you for booking with us!</h2>

//         <p>Your booking has been successfully confirmed.</p>

//         <p><b>Check-in:</b> $check_in</p>
//         <p><b>Check-out:</b> $check_out</p>

//         <p>
//             We look forward to hosting you.<br><br>
//             <strong>Hotel Booking System</strong><br>
//              © ".date('Y')." Hotel Booking System · Powered by <b>Suman</b>
//         </p>
//     </td>
//     </tr>
//     </table>
// ";


    // $attachments = [
    //     "../uploads/rooms/".$room['room_image'],
    //     "../uploads/hotels/" . $room['hotel_image']
    // ];
    // $result = send_mail($email, $subject, $body, $attachments);

    $booking_id = mysqli_insert_id($con);

    $sql_payment = "INSERT INTO payments
        (booking_id, user_id,amount, payment_method, payment_status )
        VALUES ($booking_id, $user_id,$total_price, 'esewa', 'pending')";

    if (!mysqli_query($con, $sql_payment)) {

        die("PAYMENT SQL ERROR: " . mysqli_error($con));
    
    }

    unset($_SESSION['booking_data']);
    unset($_SESSION['old_search']);
    unset($_SESSION['old_check_in']);
    unset($_SESSION['old_check_out']);
    unset($_SESSION['old_room_type']);
    unset($_SESSION['old_min_price']);
    unset($_SESSION['old_max_price']);
    
    
    $_SESSION['booking_success'] = ['message'=>'Booking SucessFul Please Check Your Invoice','type' => 'success'];
    header("Location: invoice.php?booking_id=$booking_id");
    exit;
}
?>


<style>
body {
    font-family: Arial, sans-serif;
    background: #f4f7fb;
    margin: 0;
    padding: 0;
}

.confirm-box {
    max-width: 450px;
    margin: 50px auto;
    background: #fff;
    padding: 25px 30px;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.confirm-box h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #222;
    font-size: 26px;
    font-weight: 600;
}

.detail-item {
    margin-bottom: 12px;
    font-size: 16px;
}

.detail-item b {
    color: #555;
}

.total {
    font-size: 18px;
    font-weight: bold;
    padding: 15px;
    background: #f0f9ff;
    border-left: 5px solid #2196F3;
    border-radius: 6px;
    margin-top: 15px;
}

button {
    width: 100%;
    padding: 14px;
    font-size: 17px;
    background: #28a745;
    color: #fff;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    margin-top: 20px;
    transition: .3s;
}

button:hover {
    background: #218838;
}
</style>

</style>
