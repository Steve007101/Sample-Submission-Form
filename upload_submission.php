<!-- Authored by Steven Perry -->
<!--
  This is the php file associated with the sample submission form which
  uploads one full entry to the database (4/5 total tables are inserted into,
  the last table isn't part of this form) and uses a phpmailer (send_mail.php)
  to send mail with the info to the trader and distributor.

  Some minor functions are carried out: storing photos as files in a folder
  so only their path is inserted into the database, based on the # of photos
  passed in the photo array it changes the insert statement, and it only
  inserts company name in the sample/origin forms if the applicable option
  wasn't checked for filling out the full form. 

  A transaction (no auto-commit, commit at end) is used.

  This page does not have and could maybe use proper validation for picture files,
  proper validation for each individual field for origin/recipient forms
  (it assumes if origin_sample or recicpient_sample's values' required
  the forms they are filled out), and proper validation for entries
  expected to be bools. The form is expected to take care of the validation.
  Could use prepared statements instead but Nick told me it wasn't necessary due
  to checks on the server. This is an internal website anyway.
  -->
<?php
header('Content-Type: text/plain; charset=utf-8');
ini_set('display_errors', '1');
$debug_log = "";
require_once('db_login.php'); // has database login and other minor variables
require_once('send_email.php'); // has sendEmail function
require_once('generate_lab_cover.php'); // has generateLabCoverLetter function
// which still assumes at least 10 digits are passed

// make directory for photo file uploads
if (!file_exists($photos_dir)) {
  if (!mkdir($photos_dir, 0777)) {
    $debug_log .= "\r\nFailed to create photo file directory";
    echo $debug_log;
    exit();
  } else {
    $debug_log .= "\r\nPhoto file directory created...";
  }
} else {
  $debug_log .= "\r\nPhoto file directory exists...";
}

// create connection
$mysqli = new mysqli("$servername","$username","$password","$dbname");

if ($mysqli -> connect_errno) {
  $debug_log .= "\r\nFailed to connect to MySQL: " . $mysqli -> connect_error;
  echo $debug_log;
  exit();
}
$debug_log .= "\r\nConnection to MySQL succeeded...";

// set values
// for the three values that are bools I have assumed I am passed
// correct input by the form (0/1), they are marked with intval()

$trader=$_REQUEST['trader_name'];
$trader_email=$_REQUEST['trader_email'];

$sample_type = $_REQUEST['sample_type'];
$sample_origin = $_REQUEST['sample_origin'];
// origin form
$origin_company = $_REQUEST['origin_company'];
$origin_co = $_REQUEST['origin_co'];
$origin_address = $_REQUEST['origin_address'];
$origin_address2 = $_REQUEST['origin_address2'];
$origin_city = $_REQUEST['origin_city'];
$origin_region = $_REQUEST['origin_region'];
$origin_zip = $_REQUEST['origin_zip'];
$origin_country = $_REQUEST['origin_country'];
$origin_phone = $_REQUEST['origin_phone'];
$origin_email = $_REQUEST['origin_email'];
$origin_description = $_REQUEST['origin_description'];
$origin_shipping = $_REQUEST['origin_shipping'];
// end of origin form
$sample_recipient = $_REQUEST['sample_recipient'];
// lab form options
$lab_full_comp = $_REQUEST['lab_full_comp'];
$lab_oxide = $_REQUEST['lab_oxide'];
$lab_precious = $_REQUEST['lab_precious'];
$lab_moisture = $_REQUEST['lab_moisture'];
$lab_as = $_REQUEST['lab_as'];
$lab_ba = $_REQUEST['lab_ba'];
$lab_cd = $_REQUEST['lab_cd'];
$lab_cr = $_REQUEST['lab_cr'];
$lab_pb = $_REQUEST['lab_pb'];
$lab_hg = $_REQUEST['lab_hg'];
$lab_se = $_REQUEST['lab_se'];
$lab_ag = $_REQUEST['lab_ag'];
$lab_notes = $_REQUEST['lab_notes'];
// sample recipient form
$recipient_company = $_REQUEST['recipient_company'];
$recipient_co = $_REQUEST['recipient_co'];
$recipient_address = $_REQUEST['recipient_address'];
$recipient_address2 = $_REQUEST['recipient_address2'];
$recipient_city = $_REQUEST['recipient_city'];
$recipient_region = $_REQUEST['recipient_region'];
$recipient_zip = $_REQUEST['recipient_zip'];
$recipient_country = $_REQUEST['recipient_country'];
$recipient_phone = $_REQUEST['recipient_phone'];
$recipient_email = $_REQUEST['recipient_email'];
$recipient_date_radio = intval($_REQUEST['recipient_date_radio']);
$recipient_date = $_REQUEST['recipient_date'];
if ($recipient_date == "" || $recipient_date == NULL) {
  $recipient_date = "0000-00-00";
}
$recipient_description = $_REQUEST['recipient_description'];
$recipient_shipping = $_REQUEST['recipient_shipping'];
// end of sample recipient form
$hazardous_condition = intval($_REQUEST['hazardous_condition']);
$flammable_condition = intval($_REQUEST['flammable_condition']);
$sample_description = $_REQUEST['sample_description'];
$photo_insert_statement = "";
$photo_value_statement = "";
$pic_name_arr = [];
$pic_tally = 0;

// was passed an array, need to check each
// entry for a file
for ($i = 0; $i < $max_possible_photos; $i++)
{
  if ($_FILES["pic_files"]["size"][$i] > 0)
  $pic_tally++;
}
$debug_log .= "\r\nInternal variables set...";
$debug_log .= "\r\n$pic_tally file(s) exist...";

// Photos will need to be handled differently, moved to a location
// and then uploaded by the file-name, photos were passed as array pic_files[]
// no validation for the files is included
for ($key = 0; $key < $pic_tally; $key++) {
    $tmp_name = $_FILES["pic_files"]["tmp_name"][$key];
    // basename() may prevent filesystem traversal attacks;
    // further validation/sanitation of the filename may be appropriate
    $basename = basename($_FILES["pic_files"]["name"][$key]); // filename.extension
    $name = $basename;
    // in case of multiple files with same name, add a number
    $nameOffset = 1;
    while(file_exists("$photos_dir/$name"))
    {
      $name= $nameOffset.$basename;
      $nameOffset++;
    }
    move_uploaded_file($tmp_name, "$photos_dir/$name");
    //add name to photo name arr
   if (array_push($pic_name_arr,$name))
    $debug_log .= "\r\nPicture file $name uploaded to $photos_dir";
   else $debug_log .= "\r\nPicture file $name failed to upload to $photos_dir";
}
// now we have to create photo table insert and values statements
// based on how many we have

for ($i = 1; $i <= $pic_tally; $i++)
{
  if ($i > 1) {
    $photo_insert_statement .= "`,`";
    $photo_value_statement .= "','";
  }
  $photo_insert_statement .= "photo$i";
  //$offset = $i-1;
  $photo_value_statement .= $pic_name_arr[$i-1];
  if ($i == $pic_tally)
  $debug_log .= "\r\nPicture file statements generated...";
}

// Insert Statements

// do as transaction

$query_buffer = "START TRANSACTION";
$mysqli -> query($query_buffer);
$debug_log .= "\r\n".$query_buffer;

$failed_queries = 0;

// Sample Submission Form Table insert statement

$query_buffer = "INSERT INTO 
sl_sample_submission_form (trader, trader_email, stype, sorigin, srecipient, hazardous, flammable, sdescription)
VALUES ('$trader', '$trader_email', '$sample_type', '$sample_origin', '$sample_recipient', '$hazardous_condition', '$flammable_condition', '$sample_description')";
$query = $mysqli -> query($query_buffer);
$debug_log .= "\r\n".$query_buffer;
if ($query)
{
  $debug_log .= "\r\nQuery succeeded ...";
}
else {
  $debug_log .= "\r\nQuery failed...";
  $failed_queries++;
}
// we now store the id to make sure the rest of the entries
// are correctly inserted in other tables
$id = $mysqli->insert_id;
//$id = substr(str_repeat(0, 10).$id, - 10); // to add leading zeroes
$debug_log .= "\r\nLast ID: ".$id;

// Sample Origin Form Table insert statement
// have to check if this form was filled out before making query
// I assume the form would correctly force them to input all the
// Sample Origin Form values if they selected the relevant option
if ($sample_origin == "Supplier" || $sample_origin == "Customer") {
  $query_buffer ="INSERT INTO sl_sample_origin_form
  (id,ocompany,oco,oaddress,oaddress2,ocity,oregion,ozip,ocountry,ophone,oemail,odescription,
  oshipping)
  VALUES ('$id','$origin_company','$origin_co','$origin_address',
  '$origin_address2','$origin_city','$origin_region','$origin_zip',
  '$origin_country','$origin_phone','$origin_email','$origin_description',
  '$origin_shipping')";
  $query = $mysqli -> query($query_buffer);
  $debug_log .= "\r\n".$query_buffer;
  if ($query)
  {
    $debug_log .= "\r\nQuery succeeded ...";
  }
  else {
    $debug_log .= "\r\nQuery failed...";
    $failed_queries++;
  }
} else if ($sample_origin == "Warehouse") {
  // otherwise the origin is Warehouse (to be safe check anyway)
  // we set the company name for searching purposes later
  $query_buffer ="INSERT INTO sl_sample_origin_form
  (id,ocompany)
  VALUES ('$id','Warehouse')";
  $query = $mysqli -> query($query_buffer);
  $debug_log .= "\r\n".$query_buffer;
  if ($query)
  {
    $debug_log .= "\r\nQuery succeeded ...";
  }
  else {
    $debug_log .= "\r\nQuery failed...";
    $failed_queries++;
  }
}

// Sample Recipient Form Table insert statement
// have to check if this form was filled out before making query
// I assume the form would correctly force them to input all the
// Sample Recipient Form values if they selected the relevant option
// also: insert the date whether they set an arrival date or not based on the recipient_date_radio
if ($sample_recipient == "Supplier" || $sample_recipient == "Customer") {

  $query_buffer = "INSERT INTO sl_sample_recipient_form
  (id,rcompany,rco,raddress,raddress2,rcity,rregion,rzip,rcountry,rphone,remail,set_arrival,
  rdate,rdescription,rshipping)
  VALUES ('$id','$recipient_company','$recipient_co','$recipient_address',
  '$recipient_address2','$recipient_city','$recipient_region','$recipient_zip',
  '$recipient_country','$recipient_phone','$recipient_email','$recipient_date_radio',
  '$recipient_date','$recipient_description','$recipient_shipping')";
  $query = $mysqli -> query($query_buffer);
  $debug_log .= "\r\n".$query_buffer;
  if ($query)
  {
    $debug_log .= "\r\nQuery succeeded ...";
  }
  else {
    $debug_log .= "\r\nQuery failed...";
    $failed_queries++;
  }
} else if ($sample_recipient == "St Louis Testing (Lab)" || $sample_recipient == "UMSL Labs") {
  // otherwise the recipient is Lab (to be safe check anyway)
  // we set the company name for searching purposes later
  $query_buffer = "INSERT INTO sl_sample_recipient_form
  (id,rcompany)
  VALUES ('$id','$sample_recipient')";
  $query = $mysqli -> query($query_buffer);
  $debug_log .= "\r\n".$query_buffer;
  if ($query)
  {
    $debug_log .= "\r\nQuery succeeded ...";
  }
  else {
    $debug_log .= "\r\nQuery failed...";
    $failed_queries++;
  }
  // generate lab cover letter for labs with generateLabCoverLetter()
  // sending a processed date and left 0's trimmed ID for sample number
  $lab_cover_filename = generateLabCoverLetter(substr(date("m/d/Y"), 0, 8), ltrim($id, "0"), $lab_full_comp,
  $lab_oxide, $lab_precious, $lab_moisture, $lab_as, $lab_ba, $lab_cd, $lab_cr, $lab_pb, $lab_hg, $lab_se, $lab_ag, $lab_notes,
  $distributor_name, $distributor_phone, $distributor_email, $lab_cover_letter_dir, $lab_cover_letter_template, $sample_recipient);

  // also insert into the lab cover table
  $query_buffer = "INSERT INTO sl_sample_cover
  (id,lab_full_comp,lab_oxide,lab_precious,lab_moisture,lab_as,lab_ba,lab_cd,lab_cr,lab_pb,
  lab_hg,lab_se,lab_ag,lab_notes,lab_cover)
  VALUES ('$id','$lab_full_comp','$lab_oxide','$lab_precious','$lab_moisture','$lab_as','$lab_ba','$lab_cd',
  '$lab_cr','$lab_pb','$lab_hg','$lab_se','$lab_ag','$lab_notes','$lab_cover_filename')";
  $query = $mysqli -> query($query_buffer);
  $debug_log .= "\r\n".$query_buffer;
  if ($query)
  {
    $debug_log .= "\r\nQuery succeeded ...";
  }
  else {
    $debug_log .= "\r\nQuery failed...";
    $failed_queries++;
  }
}

// Sample Photos Table insert statement
// now we can use the premade statement variables from above
if ($pic_tally>0) {

  $query_buffer = "INSERT INTO `sl_sample_photos` (`id`, `$photo_insert_statement`) 
  VALUES ('$id', '$photo_value_statement');";
  $query = $mysqli -> query($query_buffer);
  $debug_log .= "\r\n".$query_buffer;
  if ($query)
  {
    $debug_log .= "\r\nQuery succeeded ...";
  }
  else {
    $debug_log .= "\r\nQuery failed...";
    $failed_queries++;
  }
}

// COMMIT if all queries succeeded, ROLLBACK if not
if ($failed_queries == 0) {
  // commit transaction
  $query_buffer = "COMMIT";
  $debug_log .= "\r\n".$query_buffer;
  if (!$mysqli -> query($query_buffer)) {
    echo "Failure";
    $debug_log .= "\r\nCommit transaction failed...";
  }
  else {
    echo "Success";
    $debug_log .= "\r\nCommit transaction succeeded...";
  }
}
else {
  // rollback transaction
  $query_buffer = "ROLLBACK";
  $debug_log .= "\r\n".$query_buffer;
  if (!$mysqli -> query($query_buffer)) {
    echo "Failure";
    $debug_log .= "\r\nRollback failed...";
  }
  else {
    echo "Failure";
    $debug_log .= "\r\nRollback succeeded...";
  }
}

echo $debug_log; // output debug text

// close connection
$mysqli -> close();

if ($failed_queries != 0)
  exit(); // stop here if a query failed

// need to send mail to the Trader and Distributor

// first we retrieve $record_table from generate_record_table_email.php
// which uses the $id from above
require('generate_record_table_email.php');

// Bob wants 3 leading zero's
$processed_id = "000".ltrim($id, "0");

// We send the trader email, values used
// below
$sendto_email = $trader_email;
$sendto_name = $trader;
$cc = $sample_group_email;
$subject = "Sample Submission -- ID ".$processed_id;
$body = $record_table // Sample table + link to sample
."<a href='".$absolute_dir."/".$update_page_loader."?id=".$id."' target='_blank'>View Sample</a>";
// send email
sendEmailCC($sendto_email, $sendto_name, $subject, $body, $cc);

// add lab cover letter link for distributor if it's a lab
if ($sample_recipient == "Lab" || $sample_recipient == "St Louis Testing (Lab)" || $sample_recipient == "UMSL Labs") {
$record_table .= "<table class='details_table'><tr><th>Lab Cover Letter</th><td><a href='".$absolute_dir."/".$lab_cover_letter_dir."/"
    .$lab_cover_filename."' target='_blank'>".$lab_cover_filename."</a></td></tr></table>";
}

// Finally we send the distributor email
$sendto_email = $distributor_email;
$sendto_name = $distributor_name;
$subject = "Sample Submission -- ID ".$processed_id;
$body = $record_table // Sample table + link to sample w/ editing enabled
."<a href='".$absolute_dir."/".$update_page_loader."?id=".$id."&key=".$URL_key."' target='_blank'>View Sample</a>";
// send email
sendEmail($sendto_email, $sendto_name, $subject, $body);

exit();
?>