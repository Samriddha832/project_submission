<?php
session_start();
include '../includes/navbar.php';
include '../includes/connection.php';
include '../includes/functions.php';

// Only admin can access
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != "admin") {
    header("Location: login.php");
    exit();
}

// Fetch all hotels
$query = "SELECT * FROM users 
JOIN hotels ON users.user_id = hotels.hotel_admin_id";
$result = mysqli_query($con, $query);


?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../style/adminstyle.css">
<title>Admin Dashboard</title>
<style>

</style>
</head>
<body>

<div class="container">
    <h2>Admin Dashboard</h2>

        <?php if ($msg != ""): ?>
            <div class="msg <?php echo $msgType; ?>"><?php echo $msg; ?></div>
        <?php endif; ?>

    <a href="addnewuser.php" class="add-btn">+ Add New User</a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>User Name</th>
                <th>Hotel Name</th>
                <th>Email</th>
                <th>Roll</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo $row['user_id']; ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['hotel_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo $row['level']; ?></td>

                    <td>
                        <a href="edituser.php?id=<?php echo $row['user_id']; ?>" class="action-btn edit-btn">Edit</a>
                        <a href="deleteuser.php?id=<?php echo $row['user_id']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this hotel?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="7">No users found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
