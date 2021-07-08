<!-- Authored by Steven Perry -->
<!--
  This is the php file associated with the sample update page which takes
  a lab report, uploads it to the database (stores in folder, database
  only has file name), and returns "Success" or "Failure" then
  sends the trader an email and echos debug log
  -->
<?php
header('Content-Type: text/plain; charset=utf-8');
// ini_set('display_errors', '1'); // for debug messages
$debug_log = "";
require_once('db_login.php'); // also has minor variables like $lab_report_dir
require_once('send_email.php'); // for sending emails

// make directory for photo file uploads
if (!file_exists($lab_report_dir)) {
  if (!mkdir($lab_report_dir, 0777)) {
    $debug_log .= "\r\nFailed to create Lab Report directory...";
    echo $debug_log;
    exit();
  } else {
    $debug_log .= "\r\nLab Report directory created...";
  }
} else {
  $debug_log .= "\r\nLab Report directory exists...";
}

// set internal variables
$id = $_REQUEST['id'];

$debug_log .= "ID obtained: ".$id.".....";
$debug_log .= "post_lab_report.php has started...";
$debug_log .= $_FILES["lab_file"]["name"]. " obtained...";

// check we have a file
//  and move file to lab report location
if ($_FILES["lab_file"]["size"] > 0) {
    $debug_log .= $_FILES["lab_file"]["name"]." has a file size";
    $tmp_name = $_FILES["lab_file"]["tmp_name"];
    $basename = basename($_FILES["lab_file"]["name"]);
    $name = $basename;
    // in case of multiple files with same name, add a number
    $nameOffset = 1;
    while(file_exists("$lab_report_dir/$name"))
    {
      $name= $nameOffset.$basename;
      $nameOffset++;
    }
    if (move_uploaded_file($tmp_name, "$lab_report_dir/$name")) {
      $debug_log .= "\r\nLab report file $name uploaded to $lab_report_dir";
    }
    else {
      $debug_log .= "Failed to upload report file to".$lab_report_dir;
      echo $debug_log;
      exit();
    }
}
else {
  $debug_log .= "Failed to obtain a file with size > 0";
  echo $debug_log;
  exit();
}

// create connection
$mysqli = new mysqli("$servername","$username","$password","$dbname");

if ($mysqli -> connect_errno) {
  $debug_log .= "\r\nFailed to connect to MySQL: " . $mysqli -> connect_error;
  echo $debug_log;
  exit();
}

// Update Statement

$query_buffer = "UPDATE sl_sample_report
SET lab_report = '$name'
WHERE id = '$id'";
$query = $mysqli -> query($query_buffer);
$debug_log .= "\r\n".$query_buffer;
if ($query)
{
  echo "Success";
}
else {
  echo "Failure";
  echo $debug_log;
  exit();
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
$subject = "Lab Report Posted -- ID ".$email_id;
$body = $record_table // Sample table + link to sample
."<html>
<a href='".$absolute_dir."/".$update_page_loader."?id=".$id."' target='_blank'>View Sample</a>
</html>";
// send email
sendEmail($sendto_email, $sendto_name, $subject, $body);


exit();
?>

