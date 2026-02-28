<?php
session_start();
include '../includes/connection.php';
include '../includes/navbar.php';



if (!isset($_SESSION['user_id'])) {
        $_SESSION['toast'] = [
            'message' => 'Login is required to communicate and inquiry.',
            'type' => 'invalid'
        ];
        header("Location: ../login/login.php");
        exit();

        
    }

$user_id  = $_SESSION['user_id'];
$room_id  = (int)($_GET['room_id'] ?? 0);
$hotel_id = (int)($_GET['hotel_id'] ?? 0);

/* FETCH USER */
$userQ = mysqli_query($con,"SELECT email, phone FROM users WHERE user_id=$user_id");
$user = mysqli_fetch_assoc($userQ);

/* FETCH ROOM + HOTEL ADMIN */
$dataQ = mysqli_query($con,"
    SELECT hr.*, h.hotel_admin_id
    FROM hotel_rooms hr
    JOIN hotels h ON hr.hotel_id = h.hotel_id
    WHERE hr.room_id=$room_id AND h.hotel_id=$hotel_id
");
if(!$dataQ) die(mysqli_error($con));
$data = mysqli_fetch_assoc($dataQ);
$admin_id = (int)$data['hotel_admin_id'];

$room_img = (!empty($data['room_image']) && file_exists("../uploads/rooms/".$data['room_image']))
    ? "../uploads/rooms/".$data['room_image']
    : "https://via.placeholder.com/400x250";

/* ---------------- SEND MESSAGE ---------------- */
if (isset($_POST['ajax']) && $_POST['ajax'] === 'send') {
    $msg = trim($_POST['message']);
    if($msg==='') exit;

    $msg = mysqli_real_escape_string($con,$msg);

    mysqli_query($con,"
        INSERT INTO messages (sender_id, receiver_id, hotel_id, room_id, message)
        VALUES ($user_id, $admin_id, $hotel_id, $room_id, '$msg')
    ");
    exit;
}

/* ---------------- FETCH CHAT + AUTO REPLY ---------------- */
if (isset($_GET['ajax']) && $_GET['ajax'] === 'fetch') {

    // Get last user message
    $lastUserMsgQ = mysqli_query($con,"
        SELECT message_id, created_at
        FROM messages
        WHERE sender_id=$user_id AND receiver_id=$admin_id AND hotel_id=$hotel_id AND room_id=$room_id
        ORDER BY created_at DESC LIMIT 1
    ");

    if(mysqli_num_rows($lastUserMsgQ)){
        $last = mysqli_fetch_assoc($lastUserMsgQ);
        $lastMsgId = $last['message_id'];

        // Has admin replied after this message?
        $adminReplyQ = mysqli_query($con,"
            SELECT message_id FROM messages
            WHERE sender_id=$admin_id AND receiver_id=$user_id AND hotel_id=$hotel_id AND room_id=$room_id
              AND created_at > (SELECT created_at FROM messages WHERE message_id=$lastMsgId)
            LIMIT 1
        ");

        // Check 10s passed
        $timeDiffQ = mysqli_query($con,"
            SELECT TIMESTAMPDIFF(SECOND,(SELECT created_at FROM messages WHERE message_id=$lastMsgId),NOW()) AS diff
        ");
        $diff = mysqli_fetch_assoc($timeDiffQ)['diff'];

        // Send auto reply if needed
        if(mysqli_num_rows($adminReplyQ) === 0 && $diff >= 10){
            mysqli_query($con,"
                INSERT INTO messages (sender_id, receiver_id, hotel_id, room_id, message)
                VALUES ($admin_id, $user_id, $hotel_id, $room_id,
                'Thank you for your enquiry. Our team is busy right now. We will reply soon.')
            ");
        }
    }

    // Fetch all messages
    $q = mysqli_query($con,"
        SELECT * FROM messages
        WHERE hotel_id=$hotel_id AND room_id=$room_id
        ORDER BY created_at ASC
    ");
    while ($m = mysqli_fetch_assoc($q)){
        $cls = ($m['sender_id'] == $user_id) ? 'me' : 'admin';
        echo "<div class='msg $cls'>".htmlspecialchars($m['message'])."</div>";
    }
    exit;
}
?>


<!DOCTYPE html>
<html>
<head>
<title>Enquiry Chat</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
    body{
        font-family:Arial;
        background:#eef2f5;
        margin:0;padding:0;
    }
    .container{
        max-width:900px;
        margin:20px auto;
        display:flex;
        gap:20px;
        padding:0 10px;
        flex-wrap:wrap
    }
    .left{
        flex:1;
        min-width:280px;
        background:#fff;
        padding:15px;
        border-radius:8px
    }
    .right{
        flex:2;
        min-width:280px;
        background:#fff;
        padding:20px;
        border-radius:8px
    }
    .left img{
        width:100%;
        border-radius:6px
    }
    .info p{
        margin:6px 0;
        font-size:14px
    }
    .info input{
        width:100%;
        padding:8px;
        margin:3px 0;
        border:1px solid #ccc;
        border-radius:4px;
        background:#eee;
        cursor:not-allowed;
    }
    .chat{
        height:300px;
        overflow-y:auto;
        border:1px solid #ddd;
        padding:10px;
        margin-bottom:10px;
        background:#fff
    }
    .msg{
        margin:5px 0;
        padding:8px;
        border-radius:5px;
        max-width:70%;
        word-wrap:break-word;
    }
    .me{
        background:#d1f7d6;
        margin-left:auto;
        text-align:right
    }
    .admin{
        background:#f1f1f1;
        margin-right:auto;
        text-align:left
    }
    input,textarea,button{
        width:100%;
        padding:10px;
        margin:6px 0;
        box-sizing:border-box;
        border-radius:5px;
        border:1px solid #ccc
    }
    textarea{
        resize:none;
        height:70px;
    }
    button{
        background:#0d6efd;
        color:#fff;
        border:none;
        cursor:pointer
    }
    .price{
        color:#198754;
        font-weight:bold
    }
    .edit-btn{
        background:#198754;
        color:#fff;
        margin-top:5px;
        border:none;
        padding:8px;
        border-radius:5px;
        cursor:pointer;
    }
    @media(max-width:768px){
    .container{flex-direction:column}
    .chat{height:220px}
    }
</style>
</head>
<body>

<div class="container">

<div class="left">
    <h3>Room Info</h3>
    <img src="<?=$room_img?>" alt="Room Image">
    <div class="info">
        <p><b>Room No:</b> <?=htmlspecialchars($data['room_number'])?></p>
        <p><b>Type:</b> <?=htmlspecialchars($data['room_type'])?></p>
        <p class="price">$<?=number_format($data['room_price'],2)?></p>
        <hr>
        <h3>Your Details:-</h3>
        <p><b>Your Email:</b> <input value="<?=htmlspecialchars($user['email'] ?? '')?>" readonly></p>
        <p><b>Your Phone:</b> <input value="<?=htmlspecialchars($user['phone'] ?? '')?>" readonly></p>
        <!-- <button class="edit-btn" onclick="window.location.href='edit_profile.php'">Edit Details</button> -->
    </div>
</div>

<div class="right">
    <h3>Enquiry / Chat</h3>
    <div class="chat" id="chat"></div>
    <textarea id="message" placeholder="Write your enquiry..."></textarea>
    <button onclick="sendMsg()">Send Enquiry</button>
</div>

</div>

<script>
const chat = document.getElementById("chat");
let autoScroll = true;

chat.addEventListener("scroll", ()=>{
    autoScroll = (chat.scrollTop + chat.clientHeight + 20 >= chat.scrollHeight);
});

function loadChat(){
    fetch("?ajax=fetch&room_id=<?=$room_id?>&hotel_id=<?=$hotel_id?>&user_id=<?=$user_id?>")
    .then(r=>r.text())
    .then(d=>{
        chat.innerHTML = d;
        if(autoScroll) chat.scrollTop = chat.scrollHeight;
    });
}

loadChat();
setInterval(loadChat,3000);

function sendMsg(){
    const msgInput = document.getElementById('message');
    if(msgInput.value.trim()==='') return;

    let fd = new FormData();
    fd.append("ajax","send");
    fd.append("message", msgInput.value);

    fetch("",{method:"POST",body:fd})
    .then(()=>{
        msgInput.value="";
        autoScroll = true;
        loadChat();
    });
}
</script>

</body>
</html>
