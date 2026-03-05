<?php
include '../includes/functions.php';
include '../includes/connection.php';
session_start();

$successMsg = '';
$email_error = '';
$conform_password_error = '';
$phonenumber_validate_error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = sanitize($con, $_POST['username']);
    $email    = sanitize($con, $_POST['email']);
    $phonenumber = sanitize($con, $_POST['phone']);
    $password = sanitize($con, $_POST['password']);
    $conform_password = sanitize($con, $_POST['conform_password']);

    // Store only safe fields in session (NOT password)
    $_SESSION['user_name'] = $username;
    $_SESSION['register_email'] = $email;
    $_SESSION['number'] = $phonenumber;

    // Check if email already exists
    $checkQuery = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($con, $checkQuery);

    if (mysqli_num_rows($result) > 0) {
        $email_error = "Email already exists. Please use another email.";
    } 
    elseif ($password != $conform_password) {
        $conform_password_error = "Confirm Password doesn't match with Password.";
    }
    elseif (!preg_match('/^9[78][0-9]{8}$/', $phonenumber)) {
        $phonenumber_validate_error = "Number must start with 98 or 97 and must be 10 digits.";
    }
    else {

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert user
        $query = "INSERT INTO users (name, email, phone, password, level) 
                  VALUES ('$username', '$email', '$phonenumber', '$hashed_password', 'user')";

        if (mysqli_query($con, $query)) {

            // Clear session values
            unset($_SESSION['user_name']);
            unset($_SESSION['register_email']);
            unset($_SESSION['number']);

            $_SESSION['register_success'] = [
                'message' => 'Registration successful!',
                'type' => 'success'
            ];

            header("Location: login.php");
            exit();
        } else {
            echo "Error: " . mysqli_error($con);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../style/formstyle.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>

<div class="container">
    <form method="POST" class="register-form">
        <div class="form-box">
            <h2>Register</h2>

            <input type="text" 
                   name="username" 
                   placeholder="Username" 
                   required 
                   value="<?= $_SESSION['user_name'] ?? '' ?>">

            <div>
                <input type="email" 
                       name="email" 
                       placeholder="Email" 
                       required 
                       value="<?= $_SESSION['register_email'] ?? '' ?>">
                <p class="email_error"><?= $email_error ?></p>
            </div>

            <div>
                <input type="text" 
                       name="phone" 
                       placeholder="Phone Number" 
                       required 
                       value="<?= $_SESSION['number'] ?? '' ?>">
                <p class="phone_validate_error"><?= $phonenumber_validate_error ?></p>
            </div>

            <div class="password-icon">
                <input type="password" 
                       name="password" 
                       placeholder="Password" 
                       class="password_input"
                       required>
                <img src="../uploads/login_icon/hide.png" alt="hide" class="hide_icon">
            </div>

            <div>
                <div class="password-icon">
                    <input type="password" 
                           name="conform_password" 
                           placeholder="Confirm Password" 
                           class="password_input"
                           required>
                    <img src="../uploads/login_icon/hide.png" alt="hide" class="hide_icon">
                </div>
                <p class="password_error"><?= $conform_password_error ?></p>
            </div>

            <button type="submit">Register</button>

            <p>Already have an account? 
                <a href="login.php">Login here</a>
            </p>
        </div>
    </form>
</div>

<script src="../script/register_validate.js"></script>
<script src="../script/form_icon.js"></script>

</body>
</html>