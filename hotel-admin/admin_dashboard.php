<?php
session_start();
include '../includes/navbar.php';
include '../includes/connection.php';
include '../includes/functions.php';

if ($_SESSION['level'] != 'hoteladmin') {
    header("Location: ../login/login.php");
    exit();
}

$hotel_admin_id = $_SESSION['user_id'];

// Fetch rooms with hotel info
$query = "SELECT hr.*, h.hotel_name, h.location 
          FROM hotel_rooms hr
          JOIN hotels h ON hr.hotel_id = h.hotel_id
          WHERE h.hotel_admin_id = $hotel_admin_id
          ORDER BY h.hotel_name, hr.room_number";

$result = mysqli_query($con, $query);
$msg = "";
if (isset($_SESSION['room_msg'])) {
    $msg = $_SESSION['room_msg']['text'];
    $msgType = $_SESSION['room_msg']['type'];
    unset($_SESSION['room_msg']);
}


?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<link rel="stylesheet" href="../style/adminstyle.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<title>Hotel Admin Dashboard</title>
</head>
<body>
    <div id="tostBox"></div>
    <link rel="stylesheet" href="../Tost_Message/style.css">
    <script src="../Tost_Message/script.js"></script>

<div class="container">
    <?php



        if(!empty($_SESSION['toast'])){
            $toast = $_SESSION['toast']; ?>

        <script>
                showTost("<?= $toast['message'] ?>","<?= $toast['type'] ?>");
            </script>
        <?php
        unset($_SESSION['toast']);

        }


?>
    <h2>Hotel Admin Dashboard</h2>

    <a href="add_room.php" class="add-btn">+ Add New Room</a>

    <table>
        <thead>
            <tr>
                <th>Hotel</th>
                <th>Room No.</th>
                <th>Type</th>
                <th>Price (₹)</th>
                <th>Room Image</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['hotel_name']) ?></td>
                    <td><?= htmlspecialchars($row['room_number']) ?></td>
                    <td><?= htmlspecialchars($row['room_type']) ?></td>
                    <td>₹<?= $row['room_price'] ?></td>

                    <td>
                        <?php
                        $imgPath = "../uploads/rooms/" . $row['room_image'];
                        if (!empty($row['room_image']) && file_exists($imgPath)) {
                            echo "<img src='$imgPath' style='width:80px;height:60px;object-fit:cover;border-radius:4px;'>";
                        } else {
                            echo "<img src='https://via.placeholder.com/80x60?text=No+Image'>";
                        }
                        ?>
                    </td>

                    <td>
                        <a href="edit_room.php?id=<?= $row['room_id'] ?>" class="action-btn edit-btn">Edit</a>
                        <a href="delete_room.php?id=<?= $row['room_id'] ?>" class="action-btn delete-btn"
                           onclick="return confirm('Are you sure you want to delete this room?');">
                           Delete
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="8">No rooms found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
