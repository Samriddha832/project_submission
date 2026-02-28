<?php
session_start();
include '../includes/functions.php';
include '../includes/connection.php';
$email_error="";
$password_error="";



    if(!isset($_SESSION['email'])){
        $_SESSION['email'] = '';
    }

     if(!isset($_SESSION['password'])){
        $_SESSION['password'] = '';
    }

?>

<!-- Toast container -->
<div id="tostBox"></div>
<link rel="stylesheet" href="../Tost_Message/style.css">
<script src="../Tost_Message/script.js"></script>

<?php
// Show any toast message
if (isset($_SESSION['toast'])): 
    $toast = $_SESSION['toast']; 
?>
<script>
    showTost("<?= $toast['message']; ?>", "<?= $toast['type']; ?>");
</script>
<?php 
unset($_SESSION['toast']); 
endif;

// Show registration success
if (!empty($_SESSION['register_success'])): ?>
<script>
    showTost("<?= $_SESSION['register_success']['message']; ?>", "<?= $_SESSION['register_success']['type']; ?>");
</script>
<?php
unset($_SESSION['register_success']);
endif;




if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = sanitize($con, $_POST['email']);
    $password = sanitize($con, $_POST['password']);
    $_SESSION['email'] = $email;
    $_SESSION['password'] = $password;

    $query = "SELECT * FROM users WHERE email='$email' LIMIT 1";
    $result = mysqli_query($con, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['level']   = $row['level'];
            $_SESSION['name']   = $row['name'];
            $_SESSION['email'] = $row['email'];
            

            if ($row['level'] == 'hoteladmin') {
                header("Location: ../hotel-admin/admin_dashboard.php");
            } 
            elseif($row['level'] == 'admin'){
                header("Location: ../admin/index.php");
            }
            else {
                $_SESSION['tost'] = [
                'message' => 'Login Sucessfull,Welcome to Hotel Booking System',
                'type' => 'success'
        ];
                header("Location: ../index.php");
            }
            exit();
        } else {
            $password_error = "Invalid  password!";
        }
    } else {
        $email_error = "Invalid Email";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../Tost_Message/style.css">
<script src="https://kit.fontawesome.com/cbf281c702.js" crossorigin="anonymous"></script>
<link rel="stylesheet" href="../style/formstyle.css">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login</title>
<style>

</style>
</head>
<body>


<div class="container">
    
    
    <form method="POST" class="login-form">
        <div class="form-box">
             <h2>Login</h2>
             <div>
                 <input type="email" name="email" placeholder="Email" value="<?= $_SESSION['email'] ?>" required>
                 <p class="email_error"><?= $email_error ?></p>
             </div>
            
             <div>
                 <div class="password-icon">
                     <input type="password" name="password" placeholder="Password" required class="password_input" value="<?= $_SESSION['password'] ?>">
                     <img src="../uploads/login_icon/hide.png" alt="Hide Image" class="hide_icon">
                 </div>
                 <p class="password_error"><?= $password_error ?></p>
             </div>

            <button type="submit" name="submit">Login</button>
        </form>
        <div>
            <p>Don't have an account? <a href="register.php">Register here</a></p>
            <p> <a href="forget_password.php">Forget Password?</a></p>
        </div>
    </div>
</div>
<script src="../Tost_Message/script.js"></script>


</body>
</html>

<script src="../script/form_icon.js"></script>

