<!-- Authored by Steven Perry -->
<!--
  This is the php file associated with the sample update page which takes
  a record selected based on the given record ID and returns a table
  from generate_record_table.php
  -->
<?php
header('Content-Type: text/plain; charset=utf-8');
// ini_set('display_errors', '1'); // for debug messages

// set variables recieved
$id=($_GET['id']); // used by generate_record_table.php

require_once('generate_record_table.php');

echo $record_table
?>