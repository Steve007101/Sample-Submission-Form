<!-- Authored by Steven Perry -->
<!--
  This is the php file associated with the sample update page which returns
  the search results as an html formatted table with stats and buttons at the bottom
  -->
<?php
header('Content-Type: text/plain; charset=utf-8');
// ini_set('display_errors', '1'); // for debug messages
$debug_info = "";

require_once('db_login.php');

// set variables recieved
$id=intval($_GET['id']);
$trader=($_GET['trader']);
$ocompany=($_GET['ocomp']);
$date=($_GET['date']);
$sort=($_GET['sort']);
$starting_row=($_GET['row']);
$rows_per_page = ($_GET['rpp']);

$debug_info .= "recieved variables: ID:".$id.", trader:".$trader.", ocompany:".$ocompany.
    ", date:".$date.", sort:".$sort.", starting row:".$starting_row.", rows per page:".$rows_per_page;

// create connection
$mysqli = new mysqli("$servername","$username","$password","$dbname");

if ($mysqli -> connect_errno) {
  $debug_info .= "\r\nFailed to connect to MySQL: " . $mysqli -> connect_error;
  echo $debug_info;
  exit();
}
$debug_info .= "\r\nConnection to MySQL successful...";

// now we build the SQL query

// potential query: 
// SELECT * FROM sl_sample_submission_form  AS sf
// LEFT JOIN sl_sample_origin_form          AS of ON of.id=sf.id
// LEFT JOIN sl_sample_recipient_form       AS rf ON rf.id=sf.id
// LEFT JOIN sl_sample_photos               AS p  ON p.id= sf.id
// LEFT JOIN sl_sample_report               AS r  ON r.id= sf.id
// (assuming anything in fields) WHERE [id/trader/ocompany] LIKE '%$variable%' (AND ... LIKE ... etc.)
// (AND) tscreated > Unix_timestamp($date)
// ORDER BY [tscreated/id/trader/ocompany] 
// LIMIT 0, 10 (0 is starting index, 10 is rows to return)

// start of query, first we combine all tables
$query_buffer = "SELECT * FROM sl_sample_submission_form  AS ssf
                  LEFT JOIN sl_sample_origin_form          AS sof ON sof.id=ssf.id
                  LEFT JOIN sl_sample_recipient_form       AS srf ON srf.id=ssf.id
                  LEFT JOIN sl_sample_photos               AS sp  ON sp.id= ssf.id
                  LEFT JOIN sl_sample_report               AS sr  ON sr.id= ssf.id
                  LEFT JOIN sl_sample_cover                AS sc  ON sc.id= ssf.id";
// need to keep track of Where statement number for inserting WHERE and AND's
$where_count = 0;
// check if id was specified
if ($id!="") {
  // obviously since this is the first statement, assume we need a WHERE
  $where_count++;
  // this only checks for exact ID's
  $query_buffer .= " WHERE ssf.id=".$id;
}
// check if trader is specified
if ($trader!="") {
  if ($where_count == 0) {
    $query_buffer .= " WHERE ";
  } else {
    $query_buffer .= " AND ";
  }
  $where_count++;
  // this checks for the entered string in any part of the trader entry
  $query_buffer .= "trader LIKE '%".$trader."%'";
}
if ($ocompany!="") {
  if ($where_count == 0) {
    $query_buffer .= " WHERE ";
  } else {
    $query_buffer .= " AND ";
  }
  $where_count++;
  // this checks for the entered string in any part of the ocompany entry
  $query_buffer .= "ocompany LIKE '%".$ocompany."%'";
}
// check if date specified
if ($date!="") {
  if ($where_count == 0) {
    $query_buffer .= " WHERE ";
  } else {
    $query_buffer .= " AND ";
  }
  $where_count++;
  // this checks for an entered date and searches for dates the same or later
  // since it's stored as a timestamp in the database we have the server do
  // the conversion for us
  $query_buffer .= "tscreated >= TIMESTAMP('$date')";
}
// now we check for order by command input 

// if they want to sort by ocompany 
// check if the value passed is a valid choice
if ($sort == "id") {
  $query_buffer .= " ORDER BY ssf.id";
} 
else if ($sort == "trader") {
  $query_buffer .= " ORDER BY trader";
} 
else if ($sort == "ocompany") {
  $query_buffer .= " ORDER BY ocompany";
}
else if ($sort == "tscreatedDesc") {
  $query_buffer .= " ORDER BY tscreated DESC";
} 
else if ($sort == "tscreatedAsc") {
  $query_buffer .= " ORDER BY tscreated ASC";
} 
// Finally we add the LIMIT statement in order to facilitate different pages of results
$query_buffer .= " LIMIT ".$starting_row.", ".$rows_per_page;

// send query
$result = $mysqli -> query($query_buffer);
$debug_info .= "\r\n".$query_buffer; // display query
  if ($result)
  {
    $debug_info .= "\r\nSucessful query...";
  }
  else {
    $debug_info .= "\r\nQuery failed...";
  }

  // display table with results
  echo "<table class='search_table' style='table-layout: fixed;'>
        <tr>
          <th class='search_table_id'>Sample ID</th>
          <th class='search_table_date'>Date</th>
          <th class='search_table_trader'>Trader</th>
          <th class='search_table_company'>Origin</th>
          <th class='search_table_company'>Recipient</th>
        </tr>";
/*      <th>Tracking #</th>
        <th>Lab Report</th>
        <th>Hazardous</th>
        <th>Flammable</th>
        <th>Description</th> */
while($row = $result -> fetch_array(MYSQLI_BOTH)) {
      // on click row will tell function what record it is
      echo "<tr class='selectable_row' id='row_".$row[0]."' onclick=displayRecord('".$row[0]."')>"; 
      echo "<td>".$row[0]."</td>"; // id, can't use 'id' for some reason
      // set custom timestamp output as a date
      // $row['tscreated'] uses YYYY-MM-DD HH:MM:SS
      // Bob wants MM/DD/YY slashes are important
      // so it's replaced by substr($row['tscreated'], 5, 2)."/".substr($row['tscreated'], 8, 2)."/".substr($row['rdate'], 2, 2)
      // $year = substr($row['tscreated'], 2, 2);
      // $month = substr($row['tscreated'], 5, 2);
      // $day = substr($row['tscreated'], 8, 2);
      // $ts_date_display = $month."/".$day."/".$year;
      echo "<td>".substr($row['tscreated'], 5, 2)."/".substr($row['tscreated'], 8, 2)."/".substr($row['tscreated'], 2, 2)."</td>";
      echo "<td>".$row['trader']."</td>";
      echo "<td>".$row['ocompany']."</td>";
      echo "<td>".$row['rcompany']."</td>";
/*    echo "<td>".($row['tracking_num'] != "" ? "Yes" : "No")."</td>";
      echo "<td>".($row['lab_report'] != "" ? "Yes" : "No")."</td>";
      echo "<td>".($row['hazardous'] ? "Yes" : "No")."</td>";
      echo "<td>".($row['flammable'] ? "Yes" : "No")."</td>";
      echo "<td>".$row['sdescription']."</td>"; */
      echo "</tr>";
  }
  echo "</table>";

// Page button/information section

  // now we send modified version of the original questy to determine
  // how many total results then use it to find which page we're on
  // and if next and previous page buttons should work

  // do the same query but SELECT COUNT(*) instead of SELECT * and remove LIMIT clause at the end
  $query_buffer = "SELECT COUNT(*)".substr($query_buffer, 8, ((strrpos($query_buffer, "LIMIT"))-8));
  $result = $mysqli -> query($query_buffer);
  $debug_info .= "\r\n".$query_buffer; // display query
  if ($result)
  {
    $debug_info .= "\r\nSucessful query...";
  }
  else {
    $debug_info .= "\r\nQuery failed...";
  }
  $row = $result -> fetch_array(MYSQLI_BOTH);
  $count = $row['COUNT(*)'];
  // Entries and page information
  echo "<div id='below_search'><div>".$count." total results<br>
    Page ".(intval($starting_row/$rows_per_page)+1)." of ".(ceil($count/$rows_per_page))."</div>";
  // Previous Page button
  echo "<button type='button' id='prev_page' onclick='newPage(".($starting_row-$rows_per_page).")'";
  // obviously if $starting_row would then be < 0 we can't go back a page, need to disable button
  if (($starting_row-$rows_per_page) < 0) {
    echo " disabled";
  }
  echo ">Previous Page</button>";
  // Next Page button
  echo "<button type='button' id='next_page' onclick='newPage(".($starting_row+$rows_per_page).")'";
  // if $count < $starting_row + $rows_per_page + 1 then we are on last page
  if ($count < ($starting_row + $rows_per_page + 1)) {
    echo " disabled";
  }
  echo ">Next Page</button> </div>";

  echo '<input type="hidden" id="search_debug_info" name="search_debug_info" value="'.$debug_info.'">';

// close connection
$mysqli -> close();
exit();
?>