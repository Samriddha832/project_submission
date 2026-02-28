<?php
session_start();

// remove all session data
$_SESSION = [];
session_destroy();

// start new clean session
session_start();

$_SESSION['toast'] = [
    'message' => 'Logout Successfully',
    'type' => 'success'
];

header("Location: login.php");
exit();
?>
