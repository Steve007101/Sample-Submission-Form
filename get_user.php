<!-- 
  Echos hidden HTML elements with trader name and email 
  to be used by the sample submission form page. Also
  echos hidden element with $debug_log
  -->

<?php
 //ini_set('display_errors', '1');
 $debug_log = "";
require_once("/home/public_html//wp-load.php");
// need to pull user info

global $current_user; 
// from wp-load.php, included above
wp_get_current_user(); 
if ( is_user_logged_in() ) {
  $trader_name = $current_user->display_name;
  $trader_email = $current_user->user_login; 
  $debug_log .= "User obtained: ".$trader_name." at ".$trader_email;
}
else { 
  $debug_log .= "Failed to obtain user";
  wp_loginout(); 
}
// non-server only line
// $trader = "Steven Perry";
echo "<input type=hidden id='trader_name' name='trader_name' value='".$trader_name."'>";
echo "<input type=hidden id='trader_email' name='trader_email' value='".$trader_email."'>";
echo "<input type=hidden id='trader_debug_info' name='trader_debug_info' value='".$debug_log."'>";
?>
