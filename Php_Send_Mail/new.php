<?php
require 'send_mails.php';
//send_mail("samriddhas57@gmail.com","mail","hello");
$otp = rand();
send_mail("samriddhas57@gmail.com", "otp", "this is otp ".$otp);

?>