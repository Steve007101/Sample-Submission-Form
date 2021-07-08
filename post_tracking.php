<!-- Authored by Steven Perry -->
<!--
  This is the php file associated with the sample update page which takes
  tracking information, uploads it to the database, echos "Success" or "Failure"
  then sends the trader an email and echos debug log
  -->
<?php
header('Content-Type: text/plain; charset=utf-8');
// ini_set('display_errors', '1'); // for debug messages
$debug_log = "";
require_once('db_login.php'); // also has minor variables
require_once('send_email.php'); // for sending emails


// set variables recieved
$id = $_REQUEST['id'];
$carrier = $_REQUEST['carrier'];
$tracking_num = $_REQUEST['tracking_num'];

// create connection
$mysqli = new mysqli("$servername","$username","$password","$dbname");

if ($mysqli -> connect_errno) {
  $debug_log .= "\r\nFailed to connect to MySQL: " . $mysqli -> connect_error;
  echo $debug_log;
  exit();
}

// Insert Statements

$query_buffer = "INSERT INTO 
sl_sample_report (id, tstracking, carrier, tracking_num)
VALUES ('$id', CURRENT_TIMESTAMP(), '$carrier', '$tracking_num')";
$query = $mysqli -> query($query_buffer);
$debug_log .= "\r\n".$query_buffer;
if ($query)
{
  echo "Success";
}
else {
  echo "Failure";
}
echo $debug_log;

// first we retrieve $record_table from generate_record_table_email.php
// which uses the $id from above
require('generate_record_table_email.php');
// also adds to $debug_log
// and gives us $trader, $trader_email

// Manager wants 3 leading zero's
$email_id = "000".ltrim($id, "0");

// send email to trader updating them
$sendto_email = $trader_email;
$sendto_name = $trader;
$subject = "Tracking Info Posted -- ID ".$email_id;
$body = $record_table // Sample table + link to sample
."<html>
<a href='".$absolute_dir."/".$update_page_loader."?id=".$id."' target='_blank'>View Sample</a>
</html>";
// send email
sendEmail($sendto_email, $sendto_name, $subject, $body);


exit();
?>

