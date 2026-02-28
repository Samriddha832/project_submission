<?php
session_start();
include '../includes/navbar.php';
include '../includes/connection.php';

$admin_id = $_SESSION['user_id'];

$q = mysqli_query($con, "
    SELECT DISTINCT m.room_id, m.hotel_id, m.sender_id, u.email, u.user_image
    FROM messages m
    JOIN users u ON m.sender_id = u.user_id
    WHERE m.receiver_id = $admin_id
");
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Enquiry List</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
body {
    font-family: Arial, sans-serif;
    background: #eef2f5;
    margin: 0;
    padding: 20px;
}

h2 {
    color: #0056ff;
    text-align: center;
    margin-bottom: 20px;
}

.enquiry-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #fff;
    padding: 12px 20px;
    margin: 10px auto;
    max-width: 700px;
    border-radius: 10px;
    text-decoration: none;
    color: #333;
    box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    transition: 0.25s ease;
}

.enquiry-card:hover {
    background: #0056ff;
    color: #fff;
    transform: translateY(-2px);
}

.user-info {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 16px;
    font-weight: 500;
}

.user-info img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #0056ff;
    transition: 0.25s ease;
}

.enquiry-card:hover .user-info img {
    border-color: #fff;
}

.arrow {
    font-size: 18px;
    color: #0056ff;
    transition: 0.25s ease;
}

.enquiry-card:hover .arrow {
    color: #fff;
}

@media(max-width:768px){
    .enquiry-card {
        flex-direction: column;
        align-items: flex-start;
    }
    .arrow {
        align-self: flex-end;
        margin-top: 5px;
    }
}
</style>
</head>
<body>

<h2>Enquiry List</h2>

<?php while($row = mysqli_fetch_assoc($q)) { 
    $userImage = !empty($row['user_image']) && file_exists("../uploads/users/".$row['user_image'])
                 ? "../uploads/users/".$row['user_image']
                 : "https://via.placeholder.com/40"; // default avatar
?>
<a href="admin_chat.php?room_id=<?= $row['room_id'] ?>&hotel_id=<?= $row['hotel_id'] ?>&user_id=<?= $row['sender_id'] ?>" class="enquiry-card">
    <div class="user-info">
        <img src="<?= $userImage ?>" alt="User Image">
        <?= htmlspecialchars($row['email']) ?>
    </div>
    <div class="arrow"><i class="fa fa-chevron-right"></i></div>
</a>
<?php } ?>

</body>
</html>
