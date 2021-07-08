<!-- Authored by Steven Perry -->
<!--
    This is a php file which holds a function to process
    phone numbers.

    Manager wants it to be dipalyed (xxx) xxx-xxxx (I will add a country code as +xx if applicable
    but Manager doesn't want one for US numbers)
    The Regex accepts almost any variety of 10 digits along with country code
    I would rather parse it than force them to waste time re-entering
    and Manager was ok with them entering it however, as long as it displays properly.

    This will put them in the database with the same formatting:
    US Numbers: (xxx) xxx-xxxx
    non US Numbers +xx (xxx) xxx-xxxx
-->
<?php

function processPhoneNumber($phone_num) {

// phone conversion to +xx (xxx) xxx-xxxx
$phone_digits_str;
$phone_output = "";
$phone_country_code_str;
$phone_main_digits_str;
// gather all the digits
for ($x = 0; $x < strlen($phone_num); $x++) {
  if (is_numeric($phone_num[$x])) {
    $phone_digits_str .= $phone_num[$x];
  }
}
// determine if country code given
if (strlen($phone_digits_str)>10) {
  for ($x = 0; $x < (strlen($phone_digits_str)-10); $x++) {
    $phone_country_code_str .= $phone_digits_str[$x];
  }
  for ($x = strlen($phone_country_code_str); $x < strlen($phone_digits_str); $x++) {
    $phone_main_digits_str .= $phone_digits_str[$x]; 
  }
  // if country code is not +1 (US) then we add it
  if ($phone_country_code_str != "1") {
    // add country code into output string here then
    $phone_output .= "+";
    for ($x = 0; $x < strlen($phone_country_code_str); $x++) {
        $phone_output .= $phone_country_code_str[$x];
    }
    $phone_output .= " ";
  }
} else {
  $phone_main_digits_str = $phone_digits_str;
}
// 3 digit area code added to output string
$phone_output .= "(";
for ($x = 0; $x < 3; $x++) {
  $phone_output .= $phone_main_digits_str[$x];
}
$phone_output .= ") ";
// first 3 of 7 digit telephone number
for ($x = 3; $x < 6; $x++) {
  $phone_output .= $phone_main_digits_str[$x];
}
$phone_output .= "-";
// last 4 of 7 digit telephone number
for ($x = 6; $x < 10; $x++) {
  $phone_output .= $phone_main_digits_str[$x];
}

return $phone_output;
}
?>