<!-- 
    Used by upload_submission.php
    Retrieves a user's phone number by looking it up from their email
    on the staff directory table
-->
<?php
// $debug_log assumed exists in calling php file

require_once('db_login.php'); // has database login and other minor variables

// create connection
$mysqli_staff = new mysqli("$servername","$username","$password","$staff_dbname");

if ($mysqli_staff -> connect_errno) {
  $debug_log .= "\r\nFailed to connect to MySQL: " . $mysqli_staff -> connect_error;
  exit();
}
$debug_log .= "\r\nConnection to MySQL succeeded...$staff_dbname";

// assumes $trader_email exists in calling PHP file

$query_buffer = "SELECT * FROM tbl_staff WHERE email='$trader_email'";
// send query
$result = $mysqli_staff -> query($query_buffer);
$debug_log .= "\r\n".$query_buffer; // display query
  if ($result)
  {
    $debug_log .= "\r\nSucessful query...";
  }
  else {
    $debug_log .= "\r\nQuery failed...";
  }

$trader_phone = "";

while ($row = $result -> fetch_array(MYSQLI_BOTH)) {
  $trader_phone = $row['direct_phone'];
}

$debug_log .= "\r\ntrader phone is ".$trader_phone."...";

$mysqli_staff -> close();

?>