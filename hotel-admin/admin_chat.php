<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include '../includes/navbar.php';
include '../includes/connection.php';

if ($_SESSION['level'] != 'hoteladmin') {
    die("Access denied");
}

$admin_id = $_SESSION['user_id'];
$room_id  = (int)($_GET['room_id'] ?? 0);
$user_id  = (int)($_GET['user_id'] ?? 0);
$hotel_id = (int)($_GET['hotel_id'] ?? 0);

/* ROOM INFO */
$roomQ = mysqli_query($con,"
    SELECT room_image, room_number, room_type, room_price
    FROM hotel_rooms
    WHERE room_id=$room_id AND hotel_id=$hotel_id
");
if(!$roomQ) die(mysqli_error($con));
$room = mysqli_fetch_assoc($roomQ);

/* USER INFO */
$userQ = mysqli_query($con,"
    SELECT email, phone, user_image
    FROM users
    WHERE user_id=$user_id
");
$user = mysqli_fetch_assoc($userQ);

/* IMAGE */
$room_img = (!empty($room['room_image']) && file_exists("../uploads/rooms/".$room['room_image']))
    ? "../uploads/rooms/".$room['room_image']
    : "https://via.placeholder.com/400x250";

$user_img = (!empty($user['user_image']) && file_exists("../uploads/users/".$user['user_image']))
    ? "../uploads/users/".$user['user_image']
    : "https://via.placeholder.com/50";

/* ---------------- SEND ---------------- */
if (isset($_POST['ajax']) && $_POST['ajax'] === "send") {
    $msg = trim($_POST['message']);
    if ($msg === '') exit;

    $msg = mysqli_real_escape_string($con, $msg);
    mysqli_query($con,"
        INSERT INTO messages
        (sender_id, receiver_id, hotel_id, room_id, message)
        VALUES
        ($admin_id, $user_id, $hotel_id, $room_id, '$msg')
    ");
    exit;
}

/* ---------------- FETCH ---------------- */
if (isset($_GET['ajax']) && $_GET['ajax'] === "fetch") {
    $q = mysqli_query($con,"
        SELECT * FROM messages
        WHERE hotel_id=$hotel_id AND room_id=$room_id
        AND (
            (sender_id=$admin_id AND receiver_id=$user_id)
         OR (sender_id=$user_id AND receiver_id=$admin_id)
        )
        ORDER BY created_at ASC
    ");

    while($m=mysqli_fetch_assoc($q)){
        if($m['sender_id']==$admin_id){
            echo "<div class='msg me'>".htmlspecialchars($m['message'])."</div>";
        } else {
            echo "<div class='msg user'>
                    <img src='$user_img' alt='User Avatar'>
                    ".htmlspecialchars($m['message'])."
                  </div>";
        }
    }
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Room Enquiry</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body {
    font-family: Arial, sans-serif;
    background: #f0f4ff;
    margin: 0;
    padding-top: 70px; /* adjust based on your navbar height */
}

/* LEFT PANEL */
.left img {
    width: 100%;
    max-height: 250px; /* limit image height */
    object-fit: cover;
    border-radius: 8px;
}

/* CONTAINER */
.container{max-width:1100px;margin:0 auto;display:flex;gap:20px;flex-wrap:wrap}

/* LEFT PANEL */
.left{flex:1 1 35%;background:#fff;border-radius:10px;padding:20px;box-shadow:0 4px 8px rgba(0,0,0,0.05)}
.left img{width:100%;border-radius:8px;object-fit:cover}
.info p{margin:8px 0;font-size:14px;color:#333}
.price{color:#0056ff;font-weight:bold;font-size:16px}
h3{margin-bottom:15px;color:#0056ff}

/* RIGHT PANEL */
.right{flex:1 1 60%;background:#fff;border-radius:10px;padding:20px;display:flex;flex-direction:column;box-shadow:0 4px 8px rgba(0,0,0,0.05)}
.chat{flex:1;overflow-y:auto;border:1px solid #ddd;border-radius:8px;padding:10px;background:#f9faff;margin-bottom:10px}
.msg{margin:8px 0;padding:10px 14px;border-radius:20px;max-width:70%;word-wrap:break-word;position:relative;display:flex;align-items:center;gap:10px;font-size:14px}
.me{background:#cce5ff;margin-left:auto;text-align:right;color:#003366}
.user{background:#e6f0ff;margin-right:auto;color:#003366}
.user img{width:35px;height:35px;border-radius:50%;object-fit:cover;flex-shrink:0;border:2px solid #0056ff}

/* INPUT */
textarea{width:100%;padding:10px;border-radius:8px;border:1px solid #ccc;resize:none;margin-bottom:10px}
button{width:100%;padding:12px;background:#0056ff;color:#fff;font-weight:600;border:none;border-radius:8px;font-size:16px;cursor:pointer;transition:0.25s ease}
button:hover{background:#003ec2;transform:translateY(-2px)}

@media(max-width:768px){
    .container{flex-direction:column}
    .left,.right{flex:1 1 100%}
    .msg{max-width:90%}
}
</style>
</head>
<body>

<div class="container">

<!-- LEFT -->
<div class="left">
    <h3>Room Enquiry</h3>
    <img src="<?=$room_img?>" alt="Room Image">
    <div class="info">
        <p><b>Room No:</b> <?=htmlspecialchars($room['room_number'])?></p>
        <p><b>Type:</b> <?=htmlspecialchars($room['room_type'])?></p>
        <p class="price">$<?=number_format($room['room_price'],2)?></p>
        <hr>
        <p><b>User Email:</b> <?=htmlspecialchars($user['email'] ?? '-')?></p>
        <p><b>User Phone:</b> <?=htmlspecialchars($user['phone'] ?? '-')?></p>
    </div>
</div>

<!-- RIGHT -->
<div class="right">
    <h3>Chat</h3>
    <div class="chat" id="chat"></div>
    <textarea id="message" placeholder="Type your reply..."></textarea>
    <button onclick="sendMsg()">Send Reply</button>
</div>

</div>

<script>
const chat=document.getElementById("chat");
let autoScroll=true;

chat.addEventListener("scroll",()=>{
    autoScroll=(chat.scrollTop+chat.clientHeight+20>=chat.scrollHeight);
});

function loadChat(){
    fetch("?ajax=fetch&room_id=<?=$room_id?>&user_id=<?=$user_id?>&hotel_id=<?=$hotel_id?>")
    .then(r=>r.text())
    .then(d=>{
        chat.innerHTML=d;
        if(autoScroll) chat.scrollTop=chat.scrollHeight;
    });
}

setInterval(loadChat,3000);
loadChat();

function sendMsg(){
    const msgInput=document.getElementById('message');
    if(msgInput.value.trim()==='') return;

    let fd=new FormData();
    fd.append("ajax","send");
    fd.append("message",msgInput.value);

    fetch("",{method:"POST",body:fd})
    .then(()=>{
        msgInput.value="";
        autoScroll=true;
        loadChat();
    });
}
</script>

</body>
</html>
