<!-- Authored by Steven Perry -->
<!--
  This is a php file which generates the table of a single record for the
  sample update emails. Intended to be called by another php file.
  Only creates a $record_table variable then ends.
  -->
<?php
// header('Content-Type: text/plain; charset=utf-8');
// ini_set('display_errors', '1'); // for debug messages
// assume calling PHP function is using $debug_log

require_once('db_login.php');
require_once('process_phone_number.php');

// set variables recieved
// $id is already passed from calling php file

$debug_log .= "In generate_record_table.php ID is set to ".$id;

// create connection
$mysqli = new mysqli("$servername","$username","$password","$dbname");

if ($mysqli -> connect_errno) {
  $debug_log .= "\r\nFailed to connect to MySQL: " . $mysqli -> connect_error;
  echo $debug_log;
  exit();
}
$debug_log .= "\r\nConnection to MySQL succeeded...";

// build the query
$query_buffer = "SELECT * FROM sl_sample_submission_form  AS ssf
                LEFT JOIN sl_sample_origin_form          AS sof ON sof.id=ssf.id
                LEFT JOIN sl_sample_recipient_form       AS srf ON srf.id=ssf.id
                LEFT JOIN sl_sample_photos               AS sp  ON sp.id= ssf.id
                LEFT JOIN sl_sample_report               AS sr  ON sr.id= ssf.id
                LEFT JOIN sl_sample_cover                AS sc  ON sc.id= ssf.id
                WHERE ssf.id=".$id;
// send query
$result = $mysqli -> query($query_buffer);
$debug_log .= "\r\n".$query_buffer; // display query
  if ($result)
  {
    $debug_log .= "\r\nQuery succeeded...";
  }
  else {
    $debug_log .= "\r\nQuery failed...";
  }

$row = $result -> fetch_array();

$record_table = "";


// Need to parse and format telephone numbers $row['ophone'] $row['rphone']
// Bob wants it to be dipalyed (xxx) xxx-xxxx (I will add a country code as +xx if applicable
// but Bob doesn't want one for US numbers at least)
// The Regex accepts almost any variety of 10 digits along with country codes
// I would rather parse it than force them to waste time re-entering
// and Bob was ok with them entering it however, as long as it displays properly

$ophone_output = processPhoneNumber($row['ophone']);
$rphone_output = processPhoneNumber($row['rphone']);

// set output table styling
$record_table .= "<style>
                    * {
                      font-family: 'Calibri';
                    }
                    .details_table {
                      width: 500px;
                      table-layout: fixed;
                    }
                    th {
                      text-align: left;
                      vertical-align: top;
                    }
                    td {
                      text-align: left;
                      vertical-align: top;
                    }
                    .details_table th {
                      width: 110px;
                    }
                </style>";
// result as a table
$record_table .= "<table id='details_table1' class='details_table'>";
$record_table .= "<input type='hidden' id='selected_id' name='selected_id' value='".$row[0]."'>";
$record_table .= "<tr><th class='details_header1'>Sample ID</th><td class='details_cell1'>"."000".ltrim($row[0], "0")."</td></tr>";
$record_table .= "<tr><th>Submitted</th><td>"
    // $row['tscreated'] uses YYYY-MM-DD HH:MM:SS
    // Bob wants it to be displayed MM/DD/YY slashes are important
    .substr($row['tscreated'], 5, 2)."/".substr($row['tscreated'], 8, 2)."/".substr($row['tscreated'], 2, 2)
    ."</td></tr>";
$record_table .= "<tr><th>Trader</th><td>".$row['trader']."</td></tr>";
$record_table .= "<tr><th>Type</th><td>".$row['stype']."</td></tr>";
$record_table .= ($row['sorigin'] != "" ? "<tr><th>Origin</th><td>".$row['sorigin']."</td></tr>" : "");
// sample origin section
if ( $row['sorigin'] == null || $row['sorigin'] == ""
  || $row['sorigin'] == "Supplier" || $row['sorigin'] == "Customer") {
    $record_table .= "<tr><th>Origin</th><td>".$row['ocompany']."</td></tr>";
    $record_table .= "<tr><th>Address</th><td>".$row['oco']."<br>"
                                        .$row['oaddress']."<br>"
                                        .($row['oaddress2'] != "" ? $row['oaddress2']."<br>" : "")
                                        .$row['ocity'].", ".$row['oregion']." ".$row['ozip']."<br>"
                                        .$row['ocountry'].
        "</td></tr>";
    $record_table .= "<tr><th>Phone</th><td>".$ophone_output."</td></tr>";
    $record_table .= "<tr><th>Email</th><td>".$row['oemail']."</td></tr>";
    $record_table .= "<tr><th>Description</th><td>".$row['odescription']."</td></tr>";
    $record_table .= "<tr><th>Shipping</th><td>".$row['oshipping']."</td></tr>";
}
$record_table .= "<tr><th>Recipient</th><td>".$row['srecipient']."</td></tr>";;
// sample recipient section
if ($row['srecipient'] == null || $row['srecipient'] == ""
    || $row['srecipient'] == "Supplier" || $row['srecipient'] == "Customer") {
    $record_table .= "<tr><th class='details_header2'>Recipient</th><td class='details_cell2'>".$row['rcompany']."</td></tr>";
    $record_table .= "<tr><th>Address</th><td>".$row['rco']."<br>"
                                        .$row['raddress']."<br>"
                                        .($row['raddress2'] != "" ? $row['raddress2']."<br>" : "")
                                        // there's a possibility it's all on address line 1
                                        .($row['rcity'] != "" ? ($row['rcity'].", ".$row['rregion']." ".$row['rzip']."<br>"
                                        .$row['rcountry']) : "")
        ."</td></tr>";
    $record_table .= "<tr><th>Phone</th><td>".$rphone_output."</td></tr>";
    $record_table .= "<tr><th>Email</th><td>".$row['remail']."</td></tr>";
    $record_table .= "<tr><th>Arrive by</th><td>".($row['set_arrival'] ? "Yes" : "No")."</td></tr>";
    $record_table .= ($row['set_arrival'] ? ("<tr><th>Arrive by</th><td>"
      // $row['rdate'] uses YYYY-MM-DD
      // Bob wants it to be displayed MM/DD/YY slashes are important
      .substr($row['rdate'], 5, 2)."/".substr($row['rdate'], 8, 2)."/".substr($row['rdate'], 2, 2)
      ."</td></tr>") : "");
    $record_table .= "<tr><th>Description</th><td>".$row['rdescription']."</td></tr>";
    $record_table .= "<tr><th>Shipping</th><td>".$row['rshipping']."</td></tr>";
}
else if ($row['srecipient'] == "Lab" || $row['srecipient'] =="St Louis Testing (Lab)") {
  $record_table .= "<tr><th class='details_header2'>Recipient</th>
  <td class='details_cell2'>St. Louis Testing Laboratories, Inc.</td></tr>";
  $record_table .= "<tr><th>Address</th><td>"
                                  ."2810 Clark Avenue<br>"
                                  ."Saint Louis, MO 63103"."</td></tr>"
                                  ."<tr><th>Contact</th><td>Jacob Long</td><tr>";
  $record_table .= "<tr><th>Phone</th><td>(314) 531 8080</td></tr>";
  $record_table .= "<tr><th>Fax</th><td>(314) 531 8085</td></tr>";
  $record_table .= "<tr><th>Email</th><td>testlab@labinc.com</td></tr>";
  // some old lab records have their description here
  if ($row['rdescription'] != null && $row['rdescription'] != "") {
    $record_table .= "<tr><th>Description</th><td>".$row['rdescription']."</td></tr>";
  }
  // lab report cover values here
  if ($row['lab_full_comp'] || $row['lab_oxide'] ||$row['lab_precious'] ||$row['lab_moisture'] 
  ||$row['lab_as'] ||$row['lab_ba'] ||$row['lab_cd'] ||$row['lab_cr'] 
  ||$row['lab_pb'] ||$row['lab_hg'] ||$row['lab_se'] ||$row['lab_ag']) {
    $record_table .= "<tr><th>Lab Report Flags</th><td>"
    .($row['lab_full_comp'] ? "Full Composition " : "").($row['lab_oxide'] ? "Oxides " : "").($row['lab_precious'] ? "Precious Metals " : "")
    .($row['lab_moisture'] ? "Moisture " : "").($row['lab_as'] ? "Arsenic " : "").($row['lab_ba'] ? "Barium " : "")
    .($row['lab_cd'] ? "Cadmium " : "").($row['lab_cr'] ? "Chromium " : "").($row['lab_pb'] ? "Lead " : "")
    .($row['lab_hg'] ? "Mercury " : "").($row['lab_se'] ? "Selenium " : "").($row['lab_ag'] ? "Silver " : "")
    ."</td></tr>";
  }
  if ($row['lab_notes']) {
    $record_table .= "<tr><th>Notes to Lab</th><td>".$row['lab_notes']."</td></tr>";
  }
}
else if ($row['srecipient'] == "UMSL Labs") {
$record_table .= "<tr><th class='details_header2'>Recipient</th>
<td class='details_cell2'>UMSL Labs</td></tr>";
$record_table .= "<tr><th>Address</th><td>"
                                ."University of Missouri - St. Louis<br>"
                                ."1 University Blvd.<br>"
                                ."Department of Chemistry 309 SLB<br>"
                                ."St. Louis, MO 63121"."</td></tr>"
                                ."<tr><th>Contact</th><td>Dr. Jack Harms</td><tr>";
$record_table .= "<tr><th>Phone</th><td>(618) 520-8929</td></tr>";
$record_table .= "<tr><th>Email</th><td>harmsjc@umsl.edu</td></tr>";
// some old lab records have their description here
if ($row['rdescription'] != null && $row['rdescription'] != "") {
  $record_table .= "<tr><th>Description</th><td>".$row['rdescription']."</td></tr>";
}
// lab report cover values here
if ($row['lab_full_comp'] || $row['lab_oxide'] ||$row['lab_precious'] ||$row['lab_moisture'] 
||$row['lab_as'] ||$row['lab_ba'] ||$row['lab_cd'] ||$row['lab_cr'] 
||$row['lab_pb'] ||$row['lab_hg'] ||$row['lab_se'] ||$row['lab_ag']) {
  $record_table .= "<tr><th>Lab Report Flags</th><td>"
  .($row['lab_full_comp'] ? "Full Composition " : "").($row['lab_oxide'] ? "Oxides " : "").($row['lab_precious'] ? "Precious Metals " : "")
  .($row['lab_moisture'] ? "Moisture " : "").($row['lab_as'] ? "Arsenic " : "").($row['lab_ba'] ? "Barium " : "")
  .($row['lab_cd'] ? "Cadmium " : "").($row['lab_cr'] ? "Chromium " : "").($row['lab_pb'] ? "Lead " : "")
  .($row['lab_hg'] ? "Mercury " : "").($row['lab_se'] ? "Selenium " : "").($row['lab_ag'] ? "Silver " : "")
  ."</td></tr>";
}
if ($row['lab_notes']) {
  $record_table .= "<tr><th>Notes to Lab</th><td>".$row['lab_notes']."</td></tr>";
}
}
$record_table .= "<tr><th>Hazardous</th><td>".($row['hazardous'] ? "Yes" : "No")."</td></tr>";
$record_table .= "<tr><th>Flammable</th><td>".($row['flammable'] ? "Yes" : "No")."</td></tr>";
$record_table .= "<tr><th>Description</th><td>".$row['sdescription']."</td></tr>";
// Photo links
for ($x = 1; $x <= $max_possible_photos; $x++) {
  if ($row['photo'.$x] != "") {
      $record_table .= "<tr><th>Photo ".$x."</th><td><a href='".$absolute_dir."/".$photos_dir."/"
      .$row['photo'.$x]."' target='_blank'>".$row['photo'.$x]."</a></td></tr>";
  }
}
// if tracking info exists, display it
if ($row['tracking_num'] != null && $row['tracking_num'] != "") {
  // $record_table .= "<tr><th>Tracking Information</th></tr>";
  $record_table .= "<tr><th>Tracking Submitted</th><td>"
        // $row['tstracking'] uses YYYY-MM-DD
      // Bob wants it to be displayed MM/DD/YY slashes are important
      .substr($row['tstracking'], 5, 2)."/".substr($row['tstracking'], 8, 2)."/".substr($row['tstracking'], 2, 2)
  ."</td></tr>";
  $record_table .= "<tr><th>Carrier</th><td>".$row['carrier']."</td></tr>";
  $record_table .= "<tr><th>Number</th><td>";
  // if tracking number is associated with major carrier, the number is a link
  if ($row['carrier'] == "FedEx") {
    $record_table .= "<a href='https://www.fedex.com/fedextrack/?tracknumbers="
                .$row['tracking_num']."' target='_blank'>".$row['tracking_num']."</a>";
  }
  else if ($row['carrier'] == "UPS") {
    $record_table .= "<a href='http://wwwapps.ups.com/WebTracking/processInputRequest?TypeOfInquiryNumber=T&InquiryNumber1="
                .$row['tracking_num']."' target='_blank'>".$row['tracking_num']."</a>";
  }
  else if ($row['carrier'] == "USPS") {
    $record_table .= "<a href='https://tools.usps.com/go/TrackConfirmAction.action?tLabels="
                .$row['tracking_num']."' target='_blank'>".$row['tracking_num']."</a>";
  } else {
    $record_table .= $row['tracking_num'];
  }
  $record_table .= "</td></tr>";
}
// if lab report info exists, display it
if ($row['lab_report'] != null && $row['lab_report'] != "") {
  $record_table .= "<tr><th>Lab Report</th><td>"
  ."<a href='".$absolute_dir."/".$lab_report_dir."/".$row['lab_report']."' target='_blank'>"
  .$row['lab_report']."</a>"
  ."</td></tr>";
}
$record_table .= "</table>";


// hidden values to pass for tracking and lab report
// $record_table .= "<input type='hidden' id='passed_tstracking' name='passed_tstracking' value='".$row['tstracking']."'>";
// $record_table .= "<input type='hidden' id='passed_carrier' name='passed_carrier' value='".$row['carrier']."'>";
// $record_table .= "<input type='hidden' id='passed_tracking_num' name='passed_tracking_num' value='".$row['tracking_num']."'>";
// $record_table .= "<input type='hidden' id='passed_lab_report' name='passed_lab_report' value='".$row['lab_report']."'>";
// $record_table .= "<input type='hidden' id='passed_lab_report_dir' name='passed_lab_report_dir' value='".$lab_report_dir."'>";
// $record_table .= "<input type='hidden' id='record_table_debug_info' name='record_table_debug_info' value='".$debug_log."'>";
// values for sending emails in other php files
$trader = $row['trader'];
$trader_email = $row['trader_email'];

// close connection
$mysqli -> close();
?>