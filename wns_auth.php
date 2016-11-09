<?php
define("CLIENT_SECRET","Y-O-U-R C-L-I-E-N-T S-E-C-R-E-T");
define("SID","Y-O-U-R A-P-P S-I-D");
define("DB_HOST","Your DB Host name");
define("DB_USER","DB Username");
define("DB_NAME","DB Name");
define("DB_PASSWORD","password");
$ChannelUri = $_POST['ChannelUri'];
try {
    $dbh = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8", DB_USER, DB_PASSWORD);
	$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    /*** echo a message saying we have connected ***/
   // echo 'Connected to database';
    }
catch(PDOException $e)
    {
     echo $e->getMessage();
    }
?>

/*https://arjunkr.quora.com/How-to-Windows-10-WNS-Windows-Notification-Service-via-PHP*/
