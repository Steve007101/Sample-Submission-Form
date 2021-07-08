<!-- Authored by Steven Perry -->
<!--
  This is a php loader to determine which page and record
  is loaded for either a trader or distributor
  -->
<?php
header('Content-Type: text/plain; charset=utf-8');
// ini_set('display_errors', '1'); // for debug messages
$debug_log = "";
$isDistributor = false;

$id = $_REQUEST['id'];
$key = $_REQUEST['key'];

$debug_log .= "Recieved: ID is ".$id." and key is ".$key."...";

require_once('db_login.php'); // need to check for URL key

include ($update_page_name); // load the main page

// add more HTML code
echo "<html>";
// begin enable editing line to pass to page
echo "<input type='hidden' id='enable_editing' name='enable_editing' value='";
// verify distributor by link key
if ($key == $URL_key) {
    $debug_log .= "key verified...";
    echo "Yes";
    $isDistributor = true;
} else {
    $debug_log .= "key not found...";
    echo "No";
}
// end enable editing line
echo "'>";

// pass record id to load
echo "<input type='hidden' id='load_record' name='load_record' value='".$id."'>";
echo "<input type='hidden' id='loader_debug_log' name='loader_debug_log' value='".$debug_log."'>";
echo "<input type='hidden' id='loader_debug_log2' name='loader_debug_log2' value=''>";
echo "<html>";
?>

<script type='text/JavaScript'>
// show debug info in console
console.log(document.getElementById("loader_debug_log").value)
if (document.getElementById("enable_editing").value == 'Yes') {
    // show all buttons related to adding tracking info/lab report
    // if it's the correct key
    document.getElementById("loader_debug_log2").value = "Attempting to unhide buttons...";
    console.log("Enable Editing value is Yes");
    document.getElementById("submit_tracking").hidden = false;
    document.getElementById("lab_file").hidden = false;
    document.getElementById("submit_lab_report").hidden = false;
} else {
    console.log ("Enable Editing value is No");
}
// make page load record ID and populate tracking/report fields
displayRecord(document.getElementById("load_record").value); 
populateTrackingandLabSections();
</script>